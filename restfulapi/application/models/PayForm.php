<?php
/**
* 订单支付
* @version 0.1
* @package CFormModel
*/

class PayForm extends CFormModel {

	/**
	* 用户类型
	*/
	private $userType;


	/**
	* 当前用户 memberId
	*/
	private $memberId;

	public $payModel;

	public $logistics;

	public $deposit;

	public $paymentVoucher;

	public $payMethod;

	function __construct( $memberId,$userType) {
		parent::__construct();
		$this->memberId = $memberId;
		$this->userType = $userType;
	}

	public function rules()	{
		return array(
			array('payModel', 'required'),
			array('payModel,logistics,payMethod', "numerical","integerOnly"=>true),
			array('deposit,paymentVoucher','safe'),
			array('deposit','checkData'),
		);
	}

	/**
	*  检查数据格式,rules 规格
	*/
	public function checkData( $attribute,$params ){
		if($this->payModel == '4' && empty($this->logistics)){
			$label = $this->getAttributeLabel('logistics');
			$this->addError($attribute,Yii::t('msg','{attribute} can not be empty',array('{attribute}'=>$label)));
			return false;
		}

		if($this->payModel == '3' || $this->payModel == '4'){
			if( is_array($this->deposit) ){
				foreach( $this->deposit as $val ){
					if( $val!='' && ( !is_numeric ($val) || $val <=0 )  ){
						$label = $this->getAttributeLabel('deposit');
						$this->addError($attribute,Yii::t('msg','{attribute} must be a number',array('{attribute}'=>$label)));
						return false;
					}
				}
			}
	//	}else if( $this->payModel == '5' ){

		}else{
			$this->deposit = null;
		}
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签
	*/
	public function attributeLabels() {
		return array(
			'payModel' => '付款方式',
			'logistics' => '物流公司',
			'deposit'=>'订金',
		);
	}


	/**
	* 订单支付信息
	*/
	public function getOrder( $orderids ){
		$ids = explode(',',$orderids);

		$criteria = new CDbCriteria;
		$criteria->compare('t.orderId', $ids);
		$criteria->compare('t.payState', '0');
		$criteria->compare('t.state', array(0,1,2));

		$model = tbOrder::model()->findAll( $criteria );
		return $model;

	}

	/**
	* 订单支付信息,生成付款单号
	*/
	public function PayInfo( $model ){
		if( !$model ){
			return ;
		}

		$ids = array();
		$orderInfo = array();
		$order = new Order();
		//总支付价格
		$totalpayMents  = 0;
		foreach( $model as $val ){
			$ids[] = $val->orderId;
			$totalpayMents +=  $val->realPayment;
			$orderInfo[] = array(
							'orderId'=>$val->orderId,
							'orderType'=>$val->orderType,
							'orderTypeTitle'=>$order->orderType( $val->orderType ),
							'payment'=> $val->realPayment,
						);
		}
		$ids = implode(',',$ids);
		$tradeNo =  tbOrdPayment::setPaymentOrder( $ids ,$totalpayMents,'1',$this->memberId);
		$PayMents = tbPayMent::model()->getAppPayment();
		if( isset($PayMents['5']['methods'] ) ){
			foreach ( $PayMents['5']['methods'] as &$val ){
				if( !empty($val['logo'])){
					$val['logo'] = Yii::app()->params['domain_images'].$val['logo'];
				}
			}
		}
		return array('orderInfo'=>$orderInfo,'totalpayMents'=>$totalpayMents,'tradeNo'=>$tradeNo,'payModels'=>array_values( $PayMents ));
	}

	/**
	* 保存订单支付方式
	* @param array $dataArr 提交保存的数据
	* @param array $model   保存的订单对象组
	*/
	public function paymemtMethod( $model ){
		if( !$this->validate() || empty( $model )  ) {
			return false;
		}

		if( $this->payModel == '4' ){
			$logistics = tbLogistics::model()->getList( 1 );
			if( !isset ($logistics[$this->logistics]) ){
				$this->addError('logistics',Yii::t('msg','{attribute} not exist',array('{attribute}'=>'物流公司')));
				return false;
			}
		}else{
			$this->logistics = 0;
		}

		$transaction = Yii::app()->db->beginTransaction();
		try {
			foreach ( $model as $_model ){
				$_model->payModel = $this->payModel;
				$_model->payState = '2';
				$_model->logistics = $this->logistics;

				if(!$_model->save()){
					$this->addErrors( $_model->getErrors() );
					return false;
				}
			}
			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			$this->addError( 'PayForm','发生系统错误' );
			return false;
		}
	}




	/**
	* 保存订单支付方式
	* @param array $dataArr 提交保存的数据
	* @param array $model   保存的订单对象组
	*/
/* 	public function paymemtMethod_bak( $dataArr,$model ){
		$this->attributes = $dataArr;
		if( !$this->validate() ) {
			return false;
		}

		if(empty( $model ) ){
			return false;
		}
		$transaction = Yii::app()->db->beginTransaction();
		try {
			foreach ( $model as $_model ){
				$falg = 0 ;
				$tbOrderPayment = new tbOrderPayment();
				$tbOrderPayment->orderId = $_model->orderId;

				$tbOrderPayment->type = $this->payModel;
				if( $this->payMethod ){
					$tbOrderPayment->payMethod = $this->payMethod;
				}

				$tbOrderPayment->amountType = '1';
				$_model->payState = '1';
				switch( $this->payModel ){
					case '1':
						$_model->payState = '1';
						break;
					case '4':
						$tbOrderPayment->logistics = $this->logistics;
					case '3':
					case '2':
						if( !empty( $this->paymentVoucher ) ){
							$tbOrderPayment->voucher = $this->paymentVoucher;
							$_model->payState = '2';
							$falg = 1 ;
						}
						if( isset( $dataArr['deposit'][$_model->orderId] ) && $dataArr['deposit'][$_model->orderId]!= '' ){
							$tbOrderPayment->amount = $dataArr['deposit'][$_model->orderId];
							$tbOrderPayment->amountType = '0';
							$_model->payState= '1';
							$falg = 1 ;
						}
						break;
					case '5':
					case '6':
						$falg = 1 ;
						$_model->payState = '2';
						break;
				}

				if( $falg && !$tbOrderPayment->save()){
					$this->addErrors( $tbOrderPayment->getErrors() );
					return false;
				}

				$_model->payModel = $this->payModel;

				if(!$_model->save()){
					$this->addErrors( $_model->getErrors() );
					return false;
				}
			}
			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			$this->addError( 'PayForm','发生系统错误' );
			return false;
		}
	} */

	/**
	* 在线支付
	* @param object $model       付款单
	* @param string $trade_status 支付状态
	* @param string $trade_no     支付流水号
	*/
	public function onlinePay( $model ,$trade_status,$trade_no){
		if(empty( $trade_no )){
			return false;
		}

		$orderIds = explode(',',$model->orderIds);
		foreach ( $orderIds as $id ){
			$tbOrderPayment = new tbOrderPayment();
			$tbOrderPayment->orderId = $id;
			$tbOrderPayment->amountType = '1';
			$tbOrderPayment->type = 5;
			if(!$tbOrderPayment->save()){
				$this->addErrors( $tbOrderPayment->getErrors() );
				return false;
			}
		}

		$model->tradeNo = $trade_no;
		$model->state = 1;
		if(!$model->save()){
			$this->addErrors( $model->getErrors() );
			return false;
		}
		$count = tbOrder::model()->updateByPk($orderIds,array('payState'=>'2'));
		return true;
	}
}

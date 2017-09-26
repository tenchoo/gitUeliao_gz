<?php
/**
* 订单支付
* @version 0.2
* @package CFormModel
*/

class OrderPayForm extends CFormModel {

	public $payModel;

	public $logistics;

	public $paymentVoucher;

	public $payMethod;

	public $totalPrice;

	public $payTitle;

	//当前可用的支付方式
	private $_payMethods;

	//当前可选择的物流公司
	private $_logistics;

	//支付详细信息
	private $_orderPayMents = array();

	private $_memberId;

	private $_userType;

	//是否允许货到付款
	private $_payAfterArrival = true ;



	function __construct( $memberId = '' ,$userType = '' ) {
		parent::__construct();

		$uType = Yii::app()->user->getState('usertype');
		if( !empty($uType) ){
			$userType = Yii::app()->user->getState('usertype');
			$memberId = Yii::app()->user->id;
		}

		$this->_memberId = $memberId;
		$this->_userType = $userType;
	}

	public function rules()	{
		return array(
			array('payModel', 'required'),
			array('payModel,logistics,payMethod', "numerical","integerOnly"=>true),
			array('paymentVoucher','safe'),
			array('payModel','checkData'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签
	*/
	public function attributeLabels() {
		return array(
			'payModel' => '付款方式',
			'logistics' => '物流公司',
		);
	}

	/**
	*  检查数据格式,rules 规格
	*/
	public function checkData( $attribute,$params ){

		//判断支付方式是否存在
		if( !array_key_exists( $this->payModel,$this->_payMethods ) ){
			$this->addError($attribute,Yii::t('order','Payment is incorrect'));
			return false;
		}

		if($this->payModel == '4' ){
			//不允许货到付款
			if( !$this->_payAfterArrival ){
				$this->addError($attribute,Yii::t('order','You can not use cash on deposit'));
				return false;
			}
			if( empty($this->logistics) ){
				$label = $this->getAttributeLabel('logistics');
				$this->addError($attribute,Yii::t('msg','{attribute} can not be empty',array('{attribute}'=>$label)));
				return false;
			}

			//判断物流公司是否存在
			if( !array_key_exists( $this->logistics,$this->_logistics ) ){
				$this->addError($attribute,Yii::t('order','Logistics company does not exist'));
				return false;
			}
		}
	}


	/**
	*   取得支付订单信息
	*/
	public function getPayInfo( $orderids ){
		$ids = explode(',',$orderids);

		$c = new CDbCriteria();
		$c->compare('t.orderId',$ids );
		$c->compare('t.payState',array( 0,1 ));
		$c->compare('t.orderType',array( 0,1,3 ));
		$c->compare('t.state',array( 0,1,2 ));

		if( $this->_userType == tbMember::UTYPE_SALEMAN ){
			$userId[] =  $this->_memberId ;
			if( tbConfig::model()->get( 'default_saleman_id' ) == $this->_memberId ){
				$userId[] = 0;
			}

			$userId = implode(',',$userId);
			$c->join .= ' left join {{member}}  m on (m.memberId = t.memberId and m.userId in ('.$userId.') )';
			$c->addCondition("m.memberId is not null ");
		}else{
			$c->compare('t.memberId',$this->_memberId );
		}

		$model = tbOrder::model()->with('products')->findAll( $c );
		if( !$model ){
			return ;
		}

		$deposit = $depositids = array();
		foreach ( $model as $val ){
			if( $val->orderType == tbOrder::TYPE_BOOKING ){
				$depositids[] = $val->orderId;
			}
		}

		if( !empty( $depositids ) ){
			$depositModel = tbOrderDeposit::model()->findAllByPk( $ids ,'amount >0');
			foreach ( $depositModel as $val ){
				$deposit[$val->orderId] = $val->attributes;
			}
		}

		$totalPrice = 0;
		$payTitle = array();
		foreach ( $model as $key=>$val ){
			if( $val->orderType == tbOrder::TYPE_TAIL ){
				//尾货订单不允许货到付款
				$this->_payAfterArrival = false;
			}

			if( $val->orderType == tbOrder::TYPE_BOOKING ){
				//订货订单备货完成方需要支付尾款
				if( $val->state < '2' ){
					if( array_key_exists ($val->orderId,$deposit) && $deposit[$val->orderId]['payState']!='1' ){
						$this->_payAfterArrival = false;
						$payTitle[] = '订单编号：'.$val->orderId.' 订金 '.$deposit[$val->orderId]['amount'].'元' ;
						$totalPrice = bcadd( $totalPrice,$deposit[$val->orderId]['amount'] ,2) ;

						$this->_orderPayMents[$val->orderId] = array('amountType'=>'0','amount'=>$deposit[$val->orderId]['amount']);
					}else{
						unset( $model[$key] );
					}
				}else{
					//如果已支付订金，支付金额需减去已支付订金
					if( array_key_exists ($val->orderId,$deposit) && $deposit[$val->orderId]['payState'] == '1' ){
						$payAmout = bcsub( $val,$deposit[$val->orderId]['amount'] );
						$payTitle[] = '订单编号：'.$val->orderId.' 尾款 ' ;//.$payAmout.'元'
						$totalPrice = bcadd( $totalPrice,$payAmout,2 ) ;
					}else{
						$payAmout = $val->realPayment;
						$payTitle[] = '订单编号：'.$val->orderId ;//.$val->realPayment.'元'
					}
					$totalPrice = bcadd( $totalPrice, $payAmout,2) ;
					$this->_orderPayMents[$val->orderId] = array('amountType'=>'1','amount'=>$payAmout);
				}
			}else{
				$payTitle[] = '订单编号：'.$val->orderId ;//.'   '.$val->realPayment.'元'
				$totalPrice = bcadd( $totalPrice,$val->realPayment,2 ) ;
				$this->_orderPayMents[$val->orderId] = array('amountType'=>'1','amount'=>$val->realPayment);
			}
		}

		$this->totalPrice = $totalPrice;
		$this->payTitle = implode(',',$payTitle);
		return $model;
	}

	/**
	* PC端支付方式
	*/
	public function pcPayment(){
		$payModels  = tbPayMent::model()->getPcPayment();
		if( array_key_exists('1',$payModels ) ){
			unset( $payModels['1'] );
		}

		$logistics = null;

		//订金不能使用货到付款支付。
		if( array_key_exists('4',$payModels ) ){
			if ( !$this->_payAfterArrival ){
				unset( $payModels['4'] );
			}else{
				$logistics = tbLogistics::model()->getList( 1 );
			}
		}

		$this->_payMethods = $payModels;
		$this->_logistics = $logistics;


		return array($payModels,$logistics);
	}

	/**
	* wx端支付方式
	*/
	public function wxPayment(){
		$payModels = tbPayMent::model()->getWXPayment();
		if( array_key_exists('1',$payModels ) ){
			unset( $payModels['1'] );
		}
		if( array_key_exists('6',$payModels ) ){
			unset( $payModels['6'] );
		}

		if( array_key_exists('4',$payModels ) ){
			//不能使用货到付款支付。
			if ( !$this->_payAfterArrival ){
				unset( $payModels['4'] );
			}else{
				$logistics = tbLogistics::model()->getList( 1 );
				$this->_logistics = $logistics;
				foreach ( $logistics as $key=>$logi){
					$payModels['4']['methods'][] = array ('id'=>$key,'title'=>$logi);
				}
			}
		}

		$this->_payMethods = $payModels;
		return array_values($payModels);
	}

	/**
	* APP端支付方式
	*/
	public function appPayment(){
		$payModels = tbPayMent::model()->getAppPayment();
		if( isset($payModels['5']['methods'] ) ){
			foreach ( $payModels['5']['methods'] as &$val ){
				if( !empty($val['logo'])){
					$val['logo'] = Yii::app()->params['domain_images'].$val['logo'];
				}
			}
		}

		if( array_key_exists('1',$payModels ) ){
			unset( $payModels['1'] );
		}

		if( array_key_exists('4',$payModels ) ){
			//订金不能使用货到付款支付。
			if ( !$this->_payAfterArrival ){
				unset( $payModels['4'] );
			}else{
				$logistics = tbLogistics::model()->getList( 1 );
				$this->_logistics = $logistics;
				foreach ( $logistics as $key=>$logi){
					$payModels['4']['methods'][] = array ('id'=>$key,'title'=>$logi);
				}
			}
		}

		$this->_payMethods = $payModels;
		return array_values($payModels);
	}

	/**
	* 保存订单支付方式
	* @param array $dataArr 提交保存的数据
	* @param array $model   保存的订单对象组
	*/
	public function paymemtMethod( $dataArr,$model ){
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
				$tbOrderPayment = new tbOrderPayment();
				$tbOrderPayment->attributes = $this->_orderPayMents[$_model->orderId];
				$tbOrderPayment->orderId = $_model->orderId;
				$tbOrderPayment->type = $this->payModel;
				if( $this->payMethod ){
					$tbOrderPayment->payMethod = $this->payMethod;
				}

				$_model->payTime = new CDbExpression('NOW()');
				switch( $this->payModel ){
					case '4':
						$tbOrderPayment->logistics = $this->logistics;
					case '3':
					case '2':
						if( !empty( $this->paymentVoucher ) ){
							$tbOrderPayment->voucher = $this->paymentVoucher;
							$_model->payState = '2';
							$falg = 1 ;
						}
						break;
				}

				if( !$tbOrderPayment->save()){
					$this->addErrors( $tbOrderPayment->getErrors() );
					return false;
				}

				//判断是否月结客户
				$creditInfo = tbMemberCredit::creditInfo( $_model->memberId );
				if( !empty( $creditInfo ) ){
					$_model->payModel = 1; //标记支付方式为月结
				}else{
					$_model->payModel = $this->payModel;
				}

				//是否需要审核，若是业务员下单，不需要审核
				if( $_model->state >0 ){
					if( $_model->orderType == tbOrder::TYPE_NORMAL || $_model->orderType == tbOrder::TYPE_TAIL  ){
						//现货订单，PUSH进待分配
						$falg = tbOrderDistribution::addOne( $_model->orderId );
					}else if( $_model->orderType == tbOrder::TYPE_BOOKING ){
						//订货订单，PUSH进待采购
						tbOrderPurchase2::importOrder( $_model );
					}
				}else{
					//非业务员下单，进入业务员审核。在线支付的判断到已全额支付可不需要审核

				}

				if(!$_model->save()){
					$this->addErrors( $_model->getErrors() );
					return false;
				}
			}
			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			throw new CHttpException(500,$e);
			return false;
		}
	}

	/**
	* 生成付款单
	* @param integer type  在线支付类型：如 0支付宝，1微信
	*/
	public function setPaymentOrder( $type='0' ){
		if( empty($this->_orderPayMents) ) return ;

		$paymentModel = new tbOrdPayment();
		$paymentModel->type  = $type;
		$paymentModel->title = $this->payTitle;
		$paymentModel->price = $this->totalPrice;
		$paymentModel->orderIds = serialize( $this->_orderPayMents );
		$paymentModel->memberId = $this->_memberId;

		if( $paymentModel->save() ){
			return $paymentModel;
		}else{
			var_dump( $paymentModel->errors );exit;
			return ;
		}
	}

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

		//更改支付状态
		if( !empty( $model->title ) ){
			$payinfo = unserialize( $model->orderIds );
			foreach ( $payinfo as $key=>$val ){

				$tbOrderPayment = new tbOrderPayment();
				$tbOrderPayment->attributes = $val;
				$tbOrderPayment->orderId = $key;
				$tbOrderPayment->type = 5;
				if(!$tbOrderPayment->save()){
					$this->addErrors( $tbOrderPayment->getErrors() );
					return false;
				}

				$order = tbOrder::model()->findByPk( $tbOrderPayment->orderId );
				$_model->payTime = new CDbExpression('NOW()');
				//支付的是定金
				if( $tbOrderPayment->amountType == '0' ){
					$order->payState = '1';

					$count = tbOrderDeposit::model()->updateByPk( $tbOrderPayment->orderId,
									array('payState'=>'1','state'=>'1','payModel'=>'5','amount'=>$tbOrderPayment->amount) );

					//判断是否月结客户
					$creditInfo = tbMemberCredit::creditInfo( $_model->memberId );
					if( !empty( $creditInfo ) ){
						$order->payModel = '1'; //标记支付方式为月结
						$order->payState = '2';//改为已支付
					}
				}else{
					$order->payModel = '5';
					$order->payState = '2';
					if( $order->state == '0' ){
						$order->state = '1'; //在线全部支付，则不再需要审核
					}
				}

				if(!$order->save()){
					$this->addErrors( $order->getErrors() );
					return false;
				}

				//是否需要审核
				if($order->state == '1' ){
					if( $order->orderType == tbOrder::TYPE_NORMAL  || $order->orderType == tbOrder::TYPE_TAIL ){
						//现货订单，PUSH进待分配
						$falg = tbOrderDistribution::addOne( $order->orderId );
					}else if( $order->orderType == tbOrder::TYPE_BOOKING ){
						//订货订单，PUSH进待采购
						tbOrderPurchase2::importOrder( $order );
					}
				}
			}
		}else{
			//兼容原来的调用，APP初始版本使用
			$orderids = explode( ',',$model->orderIds );
			$m = tbOrder::model()->updateByPk( $orderids,array('payState'=>'2','payModel'=>'5' ) );
		}
		$model->tradeNo = $trade_no;
		$model->state = 1;
		if(!$model->save()){
			$this->addErrors( $model->getErrors() );
			return false;
		}

		return true;
	}
}

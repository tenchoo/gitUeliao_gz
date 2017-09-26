<?php
/**
 * 收货
 * @author liang
 * @version 0.1
 * @package CFormModel
 *
 */

class Received extends CFormModel {

	public $num;

	/**
	* @var int 收货操作者ID
	*/
	public $opId;

	/**
	* @var int 收货操作者类型
	*/
	public $utype;


	public function init(){
		parent::init();
		$this->opId =  Yii::app()->user->id;
		$this->utype = Yii::app()->user->getState('usertype');
	}

	/**
	* Declares the validation rules.
	* The rules state that account and password are required,
	* and password needs to be authenticated.
	*/
	public function rules()	{
		return array(
			array('num,opId','required'),
			array('num', "numerical",'min'=>'0.1'),
			array('opId', "numerical",'min'=>'1','integerOnly'=>true),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签
	*/
	public function attributeLabels() {
		return array(
			'num' => '收货数量',
		);
	}

	/**
	* 收货确定
	* @param array $dataArr 收货的数据
	* @param obj $model
	* @param integer	$receivedType	确认收货来源；0前台确认收货，1后台确认收货。
	*/
	public function save( $dataArr,$model,$receivedType ='0' ){
		if(empty( $dataArr ) || !is_array($dataArr) ){
			$this->addError('packNum',Yii::t('base','No Received data'));
			return false;
		}

		foreach( $dataArr as $val ){
			$this->num = $val;
			if( !$this->validate() ) {
				return false ;
			}
		}

		//使用事务处理
		$transaction = Yii::app()->db->beginTransaction();
		try {
			foreach( $model->products as $val ){
				if( !isset($dataArr[$val->orderProductId]) ) {
					$msg = Yii::t('base','No Received data');
					$this->addError('packNum',$val->singleNumber.$msg);
					return false;
				}

				$val->receivedNum = $dataArr[$val->orderProductId];
				if( $val->receivedNum > $val->deliveryNum ){
					$this->addError('num',Yii::t('base','The quantity of goods received can not be greater than the quantity of the goods delivered'));
					return false;
				}

				if( !$val->save() ){
					$this->addErrors( $_model->getErrors() );
					return false;
				}

				//触发器绑定[商家日销量统计]
				tbSellerSales::salesLog( new CEvent($val)  );
			}
			$set = array('state'=>'1',
						'receivedTime'=>new CDbExpression('NOW()'),
						'receivedUserId'=>$this->opId,
						'receivedType'=>$receivedType,
						);
			tbDelivery::model()->updateAll($set,'orderId =:orderId',array(':orderId'=>$model->orderId));

			$model->state = 6; //收货后，订单交易成功。
			$model->dealTime =  new CDbExpression('NOW()'); //交易完成时间

			if( !$model->save() ){
				$this->addErrors( $model->getErrors() );
				return false;
			}

			if( $receivedType ='0' ){
				if( $this->utype == tbMember::UTYPE_SALEMAN ){
					$message = '操作人：'. tbProfile::model()->getMemberUserName( $this->opId ).'(业务员 memberId:'.$this->opId.')';
				}else{
					$message = '操作人：客户';
				}
			}else{
				$message = '操作人：'. Yii::app()->user->getState('username').'(后台 userId:'.$this->opId.')';
			}

			//生成订单追踪信息
			tbOrderMessage::addMessage2( $model->orderId,'订单确认收货',$message );
			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			throw new CHttpException(503,$e);
			return false;
		}
	}

}
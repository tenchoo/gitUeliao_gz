<?php
/**
 * 订单价格申请
 * @author liang
 * @version 0.1
 * @package CFormModel
 *
 */

class OrderApplyPrice extends CFormModel {

	public $price;

	public $state;

	/**
	* Declares the validation rules.
	* The rules state that account and password are required,
	* and password needs to be authenticated.
	*/
	public function rules()	{
		return array(
			array('price','required'),
			array('price', "numerical",'min'=>'0',"integerOnly"=>false),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签
	*/
	public function attributeLabels() {
		return array(
			'price' => '申请价格',
			'state' => '审核状态',
		);
	}

	/**
	* 订单价格申请
	* @param array $dataArr 价格申请提交的数据
	* @param integer $orderId
	* @param integer $applyType 申请来源，0前台业务员提交申请，1后台提交申请
	*/
	public function save( $dataArr,$orderId ,$applyType = 0 ){
		if( !is_array($dataArr) || empty($dataArr) ){
			$this->addError( 'price','no apply data' );
			return false;
		}
		foreach ( $dataArr as $val ){
			$this->price = str_replace(',','',$val );
			if(!$this->validate()){
				return false;
			}
		}

		//使用事务处理
		$transaction = Yii::app()->db->beginTransaction();
		try {
			$applyPrice = new tbOrderApplyprice();
			$applyPrice->orderId   = $orderId;
			$applyPrice->applyType = $applyType;
			$applyPrice->prices = serialize($dataArr);

			if( !$applyPrice->save() ){
				$this->addErrors( $applyPrice->getErrors() );
				return false;
			}

			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			throw new CHttpException(503,$e);
			return false;
		}
	}


	/**
	* 价格审核通过处理
	* @param array $dataArr 价格审核提交的数据
	* @param obj $model
	*/
	public function check( $dataArr,$model,$apply ){
		//审核不通过
		if( $this->state == '2' ){
			$apply->state = '2';
			$apply->checkUserId = Yii::app()->user->id;
			$apply->checkTime = new CDbExpression('NOW()');
			if( !$apply->save() ){
				$this->addErrors( $apply->getErrors() );
				return false;
			}
			return true;
		}
		
		
		//若订单已生成结算单，则不再允许更改
		if( $model->isSettled >0 ){
			$this->addError( 'state',Yii::t('order','Order form has been issued, can not modify the order information!')  );
			return false;
		}
		

		foreach ( $dataArr as $val ){
			$this->price = str_replace(',','',$val );
			if(!$this->validate()){
				return false;
			}
		}

		//使用事务处理
		$transaction = Yii::app()->db->beginTransaction();
		try {
			//若价格不一样，更新价格，并计算金额差，更新总金额
			$m = array();
			foreach ( $model->products as $val ){
				if( !isset( $dataArr[$val->orderProductId] ) || $val->price == $dataArr[$val->orderProductId] ){
					continue;
				}

				if( $val->isSample == '0'){
					//用新的价格-原来的价格，乘以数量,先转成整数再处理
					$m[] = ( (int)($dataArr[$val->orderProductId]*100) - (int)($val->price*100) ) * $val->num/100;
				}
				$val->price = $dataArr[$val->orderProductId];
				if( !$val->save() ){
					$this->addErrors( $val->getErrors() );
					return false;
				}
			}

			$model->realPayment = $model->realPayment + array_sum( $m ) ;
			if( !$model->save() ){
				$this->addErrors( $model->getErrors() );
				return false;
			}

			$apply->prices = serialize($dataArr);
			$apply->state = '1';
			$apply->checkUserId = Yii::app()->user->id;
			$apply->checkTime = new CDbExpression('NOW()');
			if( !$apply->save() ){
				$this->addErrors( $apply->getErrors() );
				return false;
			}

			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			throw new CHttpException(503,$e);
			return false;
		}
	}

}
<?php
/**
 * 订货订单订金单表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$orderId			订单ID
 * @property integer	$state				是否已提交支付：0未支付，1已提交支付
 * @property integer	$payState			是否已确定收款
 * @property integer	$isDel				是否删除
 * @property numerical	$payModel			支付方式
 * @property numerical	$amount				订金金额
 * @property timestamp	$createTime
 *
 */


 class tbOrderDeposit extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{order_deposit}}";
	}

	public function rules() {
		return array(
			array('orderId,amount,payModel','required'),
			array('amount', "numerical",'min'=>0,'max'=>'9999999'),
			array('orderId,payModel', "numerical","integerOnly"=>true),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'orderId' => '订单ID',
			'payModel' => '支付方式',
			'amount' => '订金金额',
		);
	}

	/**
	* 修改订金金额
	* @param number $amount 修改后订金的金额
	* @param CActiveRecord $model 订单model
	*/
	public function changeDeposit( $amount,$model ){
		$comp = bccomp( $this->amount,$amount );	//订金无变化
		if( $comp == 0 ) return true;

		if( $amount > $model->realPayment  ){
			$this->addError( 'amount','订金不能大于订单总额' );
			return false;
		}

		$this->amount = $amount;

		//判断是否月结客户
		$creditInfo = tbMemberCredit::creditInfo( $model->memberId );

		$oldMstate = $model->state;

		$isZero = bccomp( $amount,0 );//订金为0
		if( $isZero  == 0 ){
			$this->payState = 1;
			$this->state = 1;

			$model->payTime = new CDbExpression('NOW()');
			if( !empty( $creditInfo ) ){
				$order->payModel = '1'; //标记支付方式为月结
				$order->payState = '2';//改为已支付
			}else{
				$model->payState = 1; //订金为0，无须支付，标为已支付订金。
			}

			if( $model->originatorType == '1' ){
				$model->state = 1;
			}
		}

		$transaction = Yii::app()->db->beginTransaction();
		try {
			if( !$this->save() ){
				return false;
			}

			if( !$model->save() ){
				$this->addErrors( $model->getErrors() );
				return false;
			}

			if( $model->state == '1' && $oldMstate == '0' ){
				//PUSH进待采购
				tbOrderPurchase2::importOrder( $model );
			}

			//月结用户额度变动，改为信用使用额度信息
			if( !empty( $creditInfo ) ){
				//修改信用使用额度信息
				$creditdetail =tbMemberCreditDetail::model()->findByAttributes( array('memberId'=>$model->memberId,'orderId'=>$model->orderId) );
				if( !$creditdetail ){
					$creditdetail = new tbMemberCreditDetail();
					$creditdetail->memberId = $model->memberId;
					$creditdetail->orderId = $model->orderId;
					$creditdetail->isCheck = $model->originatorType;
				}

				$creditdetail->amount = bcsub( $model->realPayment, $this->amount );
				if( !$creditdetail->save() ){
					$this->addErrors( $creditdetail->getErrors() );
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
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
		}
		return true;
	}
}
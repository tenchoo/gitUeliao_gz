<?php
/**
 * 确认收款
 * @author liang
 * @version 0.1
 * @package CFormModel
 *
 */

class Reconciliation extends CFormModel {

	public $type;

	public $amount;

	public $voucher;

	/**
	* Declares the validation rules.
	* The rules state that account and password are required,
	* and password needs to be authenticated.
	*/
	public function rules()	{
		return array(
			array('type,amount,voucher','required'),
			array('type,amount', "numerical",'min'=>'0'),
			array('voucher','safe'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签
	*/
	public function attributeLabels() {
		return array(
			'type' => '付款类型',
			'amount' => '收款金额',
			'voucher' => '上传凭证',
		);
	}

	/**
	* 保存发货单
	* @param array $dataArr 发货的数据
	* @param obj $model
	*/
	public function save( $model ){
		if( !$this->validate() ) {
			return false ;
		}

		//使用事务处理
		$transaction = Yii::app()->db->beginTransaction();
		try {
			$tbOrderPayment = new tbOrderPayment();
			$tbOrderPayment->orderId = $model->orderId;
			$tbOrderPayment->amountType = 1;
			$tbOrderPayment->type = $this->type;
			$tbOrderPayment->amount = $this->amount;
			$tbOrderPayment->voucher = $this->voucher;
			if( !$tbOrderPayment->save() ){
				$this->addErrors( $tbOrderPayment->getErrors() );
				return false;
			}

			$model->state = 6;
			$model->payState = 2;
			if( !$model->save() ){
				$this->addErrors( $model->getErrors() );
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
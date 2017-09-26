<?php
/**
 * 订单表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$paymentId
 * @property integer	$orderId			订单ID
 * @property integer	$type				支付类型
 * @property integer	$payMethod			支付方法ID
 * @property numerical	$amountType			货款类型，0订金，1货款
 * @property numerical	$amount				付款金额
 * @property integer	$logistics			物流公司编号
 * @property timestamp	$payTime			支付时间
 * @property string		$voucher			支付凭证图片uri
 *
 */

 class tbOrderPayment extends CActiveRecord {

	const AMOUNT_TYPE_DEPOSIT = 0;   //订金
	const AMOUNT_TYPE_GOODS = 1; //货款

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{order_payment}}";
	}

	public function rules() {
		return array(
			array('orderId,type,amount,amountType','required'),
			array('amount', "numerical"),
			array('orderId,type,logistics,payMethod,amountType', "numerical","integerOnly"=>true),
			array('payTime,voucher', "safe"),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'orderId' => '订单ID',
			'productId' => '产品ID',
			'type' => '支付类型',
			'amount' => '支付金额',
			'payTime' => '支付时间',
			'voucher' => '支付凭证',
			'logistics'=>'物流公司编号',
			'payMethod'=>'支付方法ID',
		);
	}

	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			if( empty($this->payTime) ){
				$this->payTime = new CDbExpression('NOW()');
			}
		}
		return true;
	}
}
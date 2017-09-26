<?php
/**
 * 订单退货信息表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$refundId		退货单ID
 * @property integer	$orderId		订单编号
 * @property integer	$applyType		申请人类型：0客户，1业务员
 * @property integer	$state			当前状态:0待审核，1.待仓库确认，2 待财务确认 3.已退货  10 已取消退货
 * @property decimal	$payModel		支付方式
 * @property decimal	$payState		是否已支付退款
 * @property integer	$warrantId		入库单ID
 * @property decimal	$realPayment	退货金款
 * @property integer	$createTime		申请时间
 * @property integer	$payTime		支付退款时间
 * @property string		$cause			退货理由
 *
 */

 class tbOrderRefund extends CActiveRecord {

	const TYPE_SALEMAN = 1; //由业务员申请
	const TYPE_CUSTOM = 0;  //由客户申请

	const STATE_WAITCHECK	= 0;	//0.待审核
	const STATE_WARRANT		= 1;	//1.待仓库确认,待入库
	const STATE_CONFIRM		= 2;	//2 待财务确认
	const STATE_OK			= 3;	//3.已退货
	const STATE_CANCLE		= 10;	//10 已取消退货

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{order_refund}}";
	}

	public function relations(){
		return array(
			'products'=>array(self::HAS_MANY,'tbOrderRefundProduct','refundId'),
			'order'=>array(self::BELONGS_TO,'tbOrder','orderId'),
		);
	}

	public function rules() {
		return array(
			array('orderId,realPayment,cause','required'),
			array('applyType', 'in', 'range'=>array('0','1')),
			array('realPayment', "numerical",'min'=>'0'),
			array('orderId', "numerical","integerOnly"=>true),
			array('cause', "safe"),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'orderId' => '订单编号',
			'realPayment' => '退货金款',
			'cause' => '退货理由',

		);
	}

	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
		}
		return parent::beforeSave();
	}
}
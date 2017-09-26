<?php
/**
 * 订单表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$batchesId
 * @property integer	$orderId			订单ID
 * @property date		$exprise			要求交货日期
 * @property string		$remark				备注
 *
 */

 class tbOrderBatches extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{order_batches}}";
	}

	public function relations(){
		return array(
			'order'=>array(self::BELONGS_TO,'tbOrder','orderId'),
		);
	}

	public function rules() {
		return array(
			array('orderId,exprise','required'),
			array('orderId', "numerical","integerOnly"=>true),
			array('exprise','type','dateFormat'=>'yyyy-MM-dd','type'=>'date'),
			array('remark', "safe"),
			array('remark', 'length', 'max'=>20),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'orderId' => '订单ID',
			'exprise'=>'要求交货日期',
			'remark'=>'分批交货备注',

		);
	}
}
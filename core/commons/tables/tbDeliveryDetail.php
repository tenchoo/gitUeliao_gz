<?php
/**
 * 产品销售订单发货单明细表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *

 * @property integer	$deliveryId			发货单ID
 * @property integer	$productId 			产品ID
 * @property integer	$stockId			产品库存规格ID
 * @property numerical	$num				分拣数量
 * @property numerical	$receivedNum		已收货数量
 *
 */

 class tbDeliveryDetail extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{order_deliverydetail}}";
	}

	public function rules() {
		return array(
			array('deliveryId,productId,stockId,num','required'),
			array('deliveryId,productId,stockId', "numerical","integerOnly"=>true),
			array('num,receivedNum', "numerical"),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'deliveryId' => '发货单ID',
			'productId' => '产品ID',
			'stockId' => '产品库存规格ID',
			'num' => '发货数量',
			'receivedNum'=>'已收货数量',
		);
	}

}
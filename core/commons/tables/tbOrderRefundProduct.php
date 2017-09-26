<?php
/**
 * 订单退货产品信息表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$id
 * @property integer	$refundId		退货单ID
 * @property integer	$orderId		订单编号
 * @property integer	$orderProductId	订单产品ID
 * @property integer	$productId 		产品ID
 * @property integer	$tailId 		尾货ID
 * @property integer	$state			当前状态
 * @property decimal	$num			退货数量
 * @property decimal	$price			退货价格
 * @property string     $singleNumber	单品编码
 * @property string		$color			颜色
 *
 */

 class tbOrderRefundProduct extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{order_refund_product}}";
	}

	public function relations(){
		return array(
			'orderproduct'=>array(self::BELONGS_TO,'tbOrderProduct','orderProductId'),
		);
	}

	public function rules() {
		return array(
			array('refundId,orderId,orderProductId,num,price','required'),
			array('price', "numerical",'min'=>'0'),
			array('num', "numerical"),
			array('refundId,orderId,orderProductId,productId,tailId', "numerical","integerOnly"=>true),
			array('color,singleNumber', "safe"),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'refundId' => '尾货来源',
			'productId' => '产品ID',
			'price' => '退货价格',
			'orderId'=>'订单编号',
			'num' => '退货数量',
		);
	}
}
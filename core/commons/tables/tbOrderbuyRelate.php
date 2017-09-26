<?php
/**
 * 采购单关联信息
 * @author yagas
 * @package CActiveRecord
 */
class tbOrderbuyRelate extends CActiveRecord {
	
	public $id;
	public $orderId;
	public $orderProductId;
	public $source;
	public $fromOrderId;
	public $fromDetailId;
	public $singleNumber;
	public $unitName;
	public $total;
	public $comment;
	public $color;
	
	public function tableName() {
		return '{{order_buy_relate}}';
	}
	
	public static function model( $className=__CLASS__ ) {
		return parent::model( $className );
	}
	
	public function rules() {
		return array(
			array('orderId,orderProductId,source,fromOrderId,fromDetailId','required'),
			array('singleNumber,unitName,total,comment,color','safe')
		);
	}

	/**
	 * 记录采购订单关系
	 * @param tbOrderbuyProduct $op 采购订单明细产品对象
	 * @return bool
	 */
	public static function event_relate( CEvent $event ) {
		$op = $event->sender;

		if( ! $op instanceof tbOrderbuyProduct || $op->getScenario() !== 'insert' ) {
			return false;
		}

		$realte = explode(':', $op->relate );

		foreach( $realte as $item ) {
			$purchase = tbOrderPurchase::model()->findByPk( $item );
			$re = new tbOrderbuyRelate();
			$re->attributes = $purchase->getAttributes( array('source','fromOrderId','fromDetailId','singleNumber','total','unitName','comment','color') );
			$re->orderId = $op->orderId;
			$re->orderProductId = $op->id;

			if( !$re->save() ) {
				return false;
			}
		}
		return true;
	}
}
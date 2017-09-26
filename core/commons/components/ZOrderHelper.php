<?php
/**
 * 订单助手
 * @author yagas
 * @package CApplicationComponent
 */
class ZOrderHelper extends CApplicationComponent {
	
	/**
	 * 生成订单流水号
	 * @return string
	 */
	public static function getOrderId() {
		$uuid  = str_split( md5(uniqid(mt_rand(0,90))) );
		$newid = "";
		foreach ( $uuid as $item ) {
			$newid .= ord($item);
		}
		return date('YmdH').substr( $newid, 0, 4 );
	}
	
	/**
	 * 获取单品ID计价单位名称
	 * @param string $singleNumber
	 * @return string
	 */
	public static function getUnitName($singleNumber) {
		$product = tbProductStock::model()->unitName($singleNumber);
		return empty($product)? '码' : $product;
	}

	/**
	 * 获取留货申请过期时间
	 * @param integer $createTime
	 */
	public static function expiresTime( $createTime ) {
		if( !is_numeric($createTime) ) {
			$createTime = strtotime( $createTime );
		}

		$config = tbConfig::model()->get('order_save_time');
		$expireTime = strtotime("+{$config} day", $createTime);
		return date( 'Y-m-d', $expireTime );

	}
	
	/**
	 * 添加待采购产品清单
	 * @param CEvent $event
	 */
	public static function eventToPurchase( CEvent $event ) {
		if( $event->sender instanceof CActiveRecord ) {
			Yii::log("Invalid notify to purchase");
			return false;
		}
		
		$from = get_class( $event->sender );
	}

	public static function stateAssign( $isAssign ) {
		if( $isAssign == 1 ) {
			return Yii::t('order','assigned');
		}
		return Yii::t('order','unassign');
	}

	/**
	 * 通过单品编码获取产品颜色
	 * @param string $productCode 产品单品编码
	 * @return string
	 */
	public static function getColor( $productCode ) {
		$product = tbProductStock::model()->findByAttributes( array('singleNumber'=>$productCode) );
		if( !$product ) {
			goto nothing_data;
		}

		$colorId = substr($product->relation,strpos($product->relation,':')+1);
		$spec = tbSpecvalue::model()->findByPk( $colorId );
		if( !$spec ) {
			goto nothing_data;
		}

		return $spec->title;

		nothing_data:
			return '';
	}
}
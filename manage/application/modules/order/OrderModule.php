<?php
/**
 * 订单管理
 * @author liang
 * @access 订单管理
 * @package CWebModule
 * @version 0.1
 */
class OrderModule extends CWebModule {
	
	public function init() {
		parent::init();
		Yii::import('order.models.*');
	}
}
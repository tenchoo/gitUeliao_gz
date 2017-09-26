<?php
/**
 * 产品管理
 * @author yagas
 * @access 产品管理
 * @package CWebModule
 * @version 0.1
 */
class ProductModule extends CWebModule {
	
	public function init() {
		parent::init();
		Yii::import('product.models.*');
	}
}
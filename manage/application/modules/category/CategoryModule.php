<?php
/**
 * 产品类目管理
 * @author yagas
 * @package CWebModule
 * @version 0.1
 * @access 产品类目管理
 */
class CategoryModule extends CWebModule {
	
	public function init() {
		parent::init();
		Yii::import('category.models.*');
	}
}
<?php
/**
 * 工厂订单管理
 * @author yagas
 * @access 内容管理
 */
class FactoryModule extends CWebModule {
	
	public function init() {
		parent::init();
		Yii::import('factory.models.*');
	}
	
}
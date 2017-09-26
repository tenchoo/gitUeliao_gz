<?php
class FactoryModule extends CWebModule {
	
	public $layout = "//layouts/factory";
	
	public function init() {
		parent::init();
		Yii::app()->user->loginUrl = '/factory/user/login.html';
		
		Yii::import('factory.controllers.*');
		Yii::import('factory.models.*');
	}
	
	public function beforeControllerAction($controller, $action) {
		return parent::beforeControllerAction($controller, $action);
	}
}
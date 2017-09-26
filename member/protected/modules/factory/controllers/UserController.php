<?php
class UserController extends FactoryController {
	
	public $layout = false;
	
	public function accessRules() {
		return array();
	}
	
	public function actionLogin() {
		$this->render('login');
	}
}
<?php
/**
 * 会员管理模块
 * @author yagas
 * @package CWebmodule
 * @subpackage MemberModule
 * @access 会员管理
 */
class MemberModule extends CWebModule {
	
	/* public function beforeControllerAction($controller,$action) {		
		parent::beforeControllerAction($controller, $action);
		Yii::import('member.models.*');
		return true;
	}
	
	public function afterControllerAction($controller, $action) {
		parent::afterControllerAction($controller, $action);
		return true;
	} */
	public function init() {
		parent::init();
		Yii::import('member.models.*');
	}
}
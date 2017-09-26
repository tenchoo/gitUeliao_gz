<?php
/**
 * 角色/角色组管理
 * @author yagas
 * @access 角色管理
 */
class RoleModule extends CWebModule {
	public function init() {
		parent::init();
		Yii::import('role.models.*');
	}
}
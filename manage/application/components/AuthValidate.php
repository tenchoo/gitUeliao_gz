<?php
/**
 * 访问权限校验器
 * @author yagas
 * @version 0.1
 * @package CApplicationComponent
 * 
 * 工作原理：
 * 通过路由从menuMap表中可以找到菜单的权限隶属的角色组，与会员所属的角色组进行匹配可知
 * 道用户是否对菜单（动作）是否有权限进行操作。
 * 
 * 校验算法：
 * 1．执行动作前，首先通过路由地址查询拥有执行该动作权限的角色组列表
 * 2．通过遍历用户角色组编号（角色ID），比对角色编号是否在上面查询出来的可执行动作角色组
 * 列表中。
 */
class AuthValidate extends CApplicationComponent {
	
	/**
	 * 访问校验
	 * @param string $route 路由地址
	 * @param string $userId 用户ID，由CWebUser传递，暂未使用
	 * @param array $params 拥有访问权限的角色组ID列表
	 * @return boolean
	 */
	public function checkAccess( $route, $userId, $params=array() ) {
		if( (int)Yii::app()->user->getState('isAdmin') === 1 ) {
			return true;
		}

		$roles = Yii::app()->user->getState('roles');
		if(!$roles) {
			return false;
		}

		$criteria = new CDbCriteria();
		$criteria->condition = 'menuId='.Yii::app()->controller->getRouteId();
		$criteria->addInCondition('roleId', $roles);

		$permission = tbPermission::model()->findAll($criteria);
		if($permission) {
			return true;
		}
		return false;
	}
}

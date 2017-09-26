<?php
/**
 * 角色用户权限管理
 * @author yagas
 * @version 0.1
 * @package CModel
 */
class PermissionAssignment extends CModel {
	
	public function attributeNames() {
		return array('assignment');
	}
	
	/**
	 * 进行角色权限分配
	 * @param integer $role
	 * @param array[integer] $routeArray
	 * @throws CException
	 * @return boolean
	 */
	public function assignment( $role, $routeArray ) {
		if( !is_array( $routeArray ) ) {
			throw new CException( 'Invalid params $routeArrat', 10001 );
		}
		
		foreach( $routeArray as $record ) {
			$row = new tbRoleMap();
			$row->roleId = $role->roleId;
			$row->menuId = $record;
			if( !$row->save() ) {
				$this->addError( 'error_for_insert', $record );
			}
		}
		return $this->hasErrors( 'error_for_insert' );
	}
}
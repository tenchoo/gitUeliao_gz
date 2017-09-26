<?php
class tbPermission extends CActiveRecord {
	
	/**
	 * 权限编号
	 * @var int
	 */
	public $id;
	
	/**
	 * 角色组ID
	 * @var int
	 */
	public $roleId;
	
	/**
	 * 动作ID
	 * @var int
	 */
	public $menuId;
	
	public static function model($className=__CLASS__) {
		return parent::model( $className );
	}
	
	public function tableName() {
		return '{{permission}}';
	}
	
	public function primaryKey() {
		return "id";
	}
	
	public function rules() {
		return array(
			array('roleId,menuId', 'required')
		);
	}
	
	public function relations() {
		return array(
			'task' => array(self::BELONGS_TO,'tbTask','taskId')
		);
	}
	
	/**
	 * 读取角色权限
	 * @param integer $roleId
	 * @return array
	 */
	public function readPermission( $roleId ) {
		$sql = "select id,roleId,menuId from {{permission}} where roleId=".$roleId;
		$cmd = $this->getDbConnection()->createCommand( $sql );
		return $cmd->queryAll();
	}
	
	/**
	 * 通过路由地址获取有权限的角色组ID列表
	 * @param string $route
	 * @return array
	 */
	public function getRoles( $route ) {
		$roles     = array();
		$joinTable = tbSysmenu::model()->tableName();
		$contion   = new CDbCriteria();
		$contion->select = "roleId";
		$contion->join = "LEFT JOIN $joinTable AS map ON map.id=t.menuId";
		$contion->condition = "map.route=:route";
		$contion->params = array( ':route' => '/'.$route );
		$result = $this->findAll( $contion );
		if( is_null($result) ) {
			return $roles;
		}
		
		foreach ( $result as $item ) {
			array_push( $roles, $item->roleId );
		}
		return $roles;
	}
	
	/**
	 * 为角色分配权限
	 * @param integer $roleId  角色ID
	 * @param array   $taskIds 动作ID
	 * @return boolean
	 */
	public function assignPermission($roleId,$taskIds) {
		if( empty( $taskIds ) ) return false;
		$sql  = "insert into {$this->tableName()}(`roleId`,`menuId`)values";
		$data = "";
		foreach( $taskIds as $task ) {
			$data .= sprintf(",('%s','%s')", $roleId, $task );
		}
		$sql .= substr( $data, 1);
		$cmd  = $this->getDbConnection()->createCommand( $sql );
		$result = $cmd->execute();
		return $result>0;
	}
	
	/**
	 * 移除角色权限
	 * @param integer $roleId  角色ID
	 * @param array   $taskIds 动作ID
	 * @return boolean
	 */
	public function removePermission($roleId,$taskIds) {
		if( empty( $taskIds ) ) return false;
		$criteria = new CDbCriteria();
		$criteria->addCondition(array('roleId'=>$roleId));
		$criteria->addInCondition('menuId', $taskIds);
		$result = $this->deleteAll( $criteria );
		return $result;
	}
}
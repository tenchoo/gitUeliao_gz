<?php
/**
 * 角色组关系模型
 * @author yagas
 *
 * @property groupId 角色组ID
 */
class tbRoleMap extends CActiveRecord {
	
	/**
	 * 角色组ID
	 * @var integer
	 */
	public $groupId;
	
	/**
	 * 角色ID
	 * @var integer
	 */
	public $roleId;
	
	static public function model( $className=__CLASS__ ) {
		return parent::model( $className );
	}
	
	public function tableName() {
		return "{{role_map}}";
	}
	
	public function primaryKey() {
		return "id";
	}
	
	/**
	 * 角色组校验规则
	 * @see CModel::rules()
	 */
	public function rules() {
		return array(
			array('groupId,roleId','required'),
			array('groupId,roleId', "numerical","integerOnly"=>true),
		);
	}
	
	public function relations() {
		return array(
			'role' => array(self::BELONGS_TO,'tbRole','roleId'),
		);
	}
}
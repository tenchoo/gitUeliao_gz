<?php
/**
 * 角色组数据模型
 * @author yagas
 *
 * @property groupId 角色组ID
 */
class tbRoleGroup extends CActiveRecord {

	public $roleId;
	public $deppositionId;
	public $departmentId;
	public $state = 0;

	static public function model( $className=__CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{role_group}}";
	}

	public function primaryKey() {
		return "groupId";
	}

	/**
	 * 角色组校验规则
	 * @see CModel::rules()
	 */
	public function rules() {
		return array(
			array('departmentId,deppositionId,roleId','required'),
			array('state','in','range'=>array(0,1)),
			array('departmentId,deppositionId,roleId', "numerical","integerOnly"=>true),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'departmentId' => '所属部门',
			'deppositionId' => '职位'
		);
	}
	
	/**
	* 查询/列表
	* @param array $condition 查询的条件
	* @param integer $perSize 每页显示条数
	*/
	public function search( $condition = array(),$perSize = 1 ){
		$criteria = new CDbCriteria;
		if( is_array($condition) ){
			foreach ( $condition as $key=>$val ){
				if( $val=='' ) continue;
				$criteria->compare('t.'.$key,$val);
			}
		}
		$criteria->compare('t.state','0');
		$model = new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$perSize,'pageVar'=>'page'),
		));

		$result['list'] = $model->getData();
		$result['pages'] = $model->getPagination();
		return $result;
	}

	/**
	 * 清除角色组成员，一般是重新分配角色时使用
	 * @param $depPositionId
	 * @return int
	 */
	public function removeRoles($depPositionId) {
		return $this->deleteAllByAttributes(['deppositionId'=>$depPositionId]);
	}
}
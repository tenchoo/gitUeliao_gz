<?php
class tbRole extends CActiveRecord {
	
	public $roleName;
	
	public static function model($className=__CLASS__) {
		return parent::model( $className );
	}
	
	public function tableName() {
		return '{{role}}';
	}
	
	public function primaryKey() {
		return "roleId";
	}
	
	public function rules() {
		return array(
			array('roleName', 'required'),
			array('roleName', 'unique', 'attributeName'=>'roleName', 'message'=>Yii::t('base','role name has been exists.')),
			array('departmentId', "numerical","integerOnly"=>true),
			array('roleName,description', 'safe',)			
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
	* 根据部门ID取得角色
	* @param integer $id 部门ID
	*/
	public function getByDepId( $id ){
		$result = array();
		if( !empty( $id )){
			$data = $this->findAll( 'state = 0 and departmentId = '.$id );
			foreach ( $data as $val ){
				$result[$val->roleId] = $val->roleName;
			}
		}
		return $result;
	}
}
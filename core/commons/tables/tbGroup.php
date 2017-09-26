<?php
/**
 * 前台用户组
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$groupId
 * @property string		$title		用户组名称
 *
 */

 class tbGroup extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{group}}";
	}

	public function rules() {
		return array(
			array('title','required'),
			array('title','safe'),
			array('title','unique'),

		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'title' => '用户组名称',
		);
	}
	
	/**
	* 取得所有用户组，不包含业务员组
	*
	*/
	public function getList(){
		$model = $this->findAll( 'groupId>1' );
		$result = array();
		foreach ( $model as $val ){
			$result[$val->groupId] = $val->title;
		}
		return $result;
	}
}
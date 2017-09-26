<?php
/**
 * 前台用户等级
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$groupId	单位ID
 * @property string		$title		等级名称
 * @property string		$logo		等级图标
 *
 */

 class tbLevel extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{level}}";
	}

	public function rules() {
		return array(
			array('title','required'),
			array('title','length','min'=>'2','max'=>'10'),
			array('title,logo','safe'),
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
			'logo'=>'图标'
		);
	}
	
	/**
	* 取得全员等级
	*/
	public function getLevels(){
		$model = $this->findAll();
		$result = array();
		foreach ( $model as $val ){
			$result[$val->levelId] = $val->attributes;
		}
		return $result ;
	}
}
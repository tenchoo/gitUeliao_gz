<?php
/**
 * 预定义规格属性组
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$setGroupId		组ID 
 * @property integer	$type 			类型，1:属性组 2:色系 
 * @property integer	$listOrder		排序值
 * @property string		$title	 		名称
 *
 */

class tbSetGroup extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{set_group}}";
	}

	public function rules() {
		return array(
			array('title','required'),
			array('type,listOrder','numerical','integerOnly'=>true),
			array('title','safe'),

		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'type' => '类型',
			'listOrder' => '排序值 ',
			'title' => '名称',
		);
	}
	
	/**
	* 取得预设组列表
	* @param integer $type 
	*/
	public function getList( $type ){
		$model = $this->findAll(array(
			'select'=>'setGroupId,title',
			'condition'=>'type = '.$type,
			'order' =>' listOrder asc',
		));
		$result = array();
		
		foreach( $model as $val ){
			$k = $val->setGroupId;
			$result[$k] = $val->title;
		}
		return $result;
	}
}
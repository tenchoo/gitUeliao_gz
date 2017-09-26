<?php
/**
 * 系统参数配置表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$setId
 * @property integer	$variable			变量名
 * @property integer	$title	 			参数名称
 * @property integer	$setValue			参数值
 * @property integer	$unit				单位
 *
 */

 class tbSetting extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{setting}}";
	}


	public function rules() {
		return array(
			array('variable,setValue','required'),
			array('variable,title,setValue,unit', "safe"),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'variable' => '变量名',
			'title' => '参数名称',
			'setValue' => '参数值',
			'unit' => '单位',
		);
	}

}
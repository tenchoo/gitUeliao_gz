<?php

/**
 * 帮助分类单页内容表模型
 *
 * @property integer $categoryId
 * @property string	 $content		内容
 * @version 0.1
 * @package CActiveRecord
 */

class tbHelpCategoryPage extends CActiveRecord {

	/**
	 * 构造数据库表模型单例方法
	 * @param system $className
	 * @return static
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{help_category_page}}';
	}

	/**
	 * @return array 模型验证规则.
	 */
	public function rules()	{
		return array(
			array('categoryId,content','required'),
			array('categoryId', 'numerical','integerOnly'=>true),
			array('content','safe'),

		);
	}

	public function attributeLabels(){
		return array(
			'categoryId'=>'分类ID',
			'content'=>'内容',
		);
	}
}
?>
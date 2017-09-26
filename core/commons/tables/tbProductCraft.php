<?php

/**
 * 产品特殊工艺配置表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$id
 * @property integer	$productId			产品ID
 * @property string		$craftCode			工艺代表编号
 *
 */

class tbProductCraft extends CActiveRecord {


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
		return '{{product_craft}}';
	}

	/**
	 * @return array 模型验证规则.
	 */
	public function rules()
	{
		return array(
			array('productId,craftCode','required'),
			array('productId','numerical','integerOnly'=>true),
			array('craftCode', 'safe'),
		);
	}

	public function relations() {
		return array(
			"detail" => array(self::BELONGS_TO, 'tbProduct', 'productId')
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'productId' => '产品ID',
			'craftCode' => '工艺编号',
		);
	}


	/**
	* 取得产品的所有工艺,并取得工艺图标
	*/
	public function getAllCraft( $productId ){
		$models = $this->findAll( 'productId = :p',array(':p'=>$productId) );
		return array_map ( function ($i){ return $i->craftCode;},$models);
	}
}
?>
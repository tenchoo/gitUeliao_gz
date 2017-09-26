<?php
/**
 * 产品描述
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$productId				产品ID
 * @property string		$testResults			产品特性
 * @property string		$content				产品描述
 * @property string		$phoneContent			手机端产品描述
 * @property string		$pictures				产品图片JSON数据
 *
 */

 class tbProductDetail extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{product_detail}}";
	}

	public function rules() {
		return array(
			array('productId,content,pictures','required'),
			array('productId','numerical','integerOnly'=>true),
			array('content,pictures,phoneContent,testResults','safe'),

		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'productId' => '产品ID',
			'content' => '产品描述',
			'pictures'=>'产品图片',
			'phoneContent'=>'手机端产品描述',
			'testResults'=>'产品测试',
		);
	}

}
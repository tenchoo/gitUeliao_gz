<?php
/**
 * 产品呆滞级别时长设置
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$id
 * @property integer	$glassLevelId		呆滞等级ID
 * @property integer	$conditions			呆滞时长,单位为天
 * @property integer	$productId			产品ID
 *
 */
 class tbProductGlassyLevel extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{glassy_level_product}}";
	}

	public function rules() {
		return array(
			array('glassLevelId,productId,conditions','required'),
			array('glassLevelId,productId', "numerical","integerOnly"=>true,'min'=>'1'),
			array('conditions', "numerical","integerOnly"=>true,'min'=>'1','max'=>'10000'),			
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'productId' => '产品',
			'glassLevelId' => '呆滞等级',
			'conditions'=>'呆滞时长'
		);
	}

}
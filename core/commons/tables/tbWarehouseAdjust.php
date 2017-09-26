<?php
/**
 * 仓库调整单模型
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$adjustId		调整单ID
 * @property integer	$num			调整数量
 * @property integer	$userId			操作人userId
 * @property integer	$createTime		制单时间
 * @property string		$singleNumber	单品编码
 * @property string		$remark			备注
 *
 */

 class tbWarehouseAdjust extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{warehouse_adjust}}";
	}
	
	public function relations(){
		return array(
			'detail'=>array(self::HAS_MANY,'tbWarehouseAdjustDetail','adjustId'),
		);
	}

	public function rules() {
		return array(
			array('singleNumber,num','required'),
			array('num', "numerical","integerOnly"=>false,'min'=>'0.1','max'=>'10000'),
			array('remark', "length",'max'=>'50'),
			array('singleNumber,remark', "safe"),

		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'num' => '调整总数量',
			'singleNumber' => '单品编码',
			'remark'=>'备注',
		);
	}
}
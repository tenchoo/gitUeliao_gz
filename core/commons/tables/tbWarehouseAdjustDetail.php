<?php
/**
 * 调整单明细表模型
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$id
 * @property integer	$adjustId		调整单ID
 * @property integer	$positionId		仓位ID
 * @property integer	$warehouseId	仓库ID
 * @property integer	$num			调整数量
 * @property string		$batch			调整后产品批次
 * @property string		$oldbatch		调整前产品批次
 *
 */

 class tbWarehouseAdjustDetail extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{warehouse_adjust_detail}}";
	}

	public function rules() {
		return array(
			array('adjustId,positionId,warehouseId,num,batch','required'),
			array('adjustId,positionId,warehouseId', "numerical","integerOnly"=>true,'min'=>'1'),
			array('num', "numerical","integerOnly"=>false,'min'=>'0.1','max'=>'10000'),
			array('batch,oldbatch', "length",'min'=>'2','max'=>'20'),
			array('batch,oldbatch', "safe"),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'num' => '调整数量',
			'positionId' => '仓库',
			'warehouseId'=>'仓库',
			'batch'=>'产品批次',
			'oldbatch'=>'调整前产品批次',
		);
	}

}
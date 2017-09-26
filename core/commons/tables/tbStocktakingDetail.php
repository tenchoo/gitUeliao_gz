<?php
/**
 * 仓库盘点单
 *
 * @property integer	$id
 * @property integer	$stocktakingId	盘点单ID
 * @property integer	$productId		产品ID
 * @property integer	$positionId		仓位ID
 * @property numerical	$oldnum			原库存数量
 * @property numerical	$num			盘点数量
 * @property string		$singleNumber	单品编码
 * @property string		$productBatch	产品批次
 * @property string		$color			颜色
 * @property string		$unit			单位
 * @property string		$title
 * @property string		$positionTitle	仓位名称
 *
 */

class tbStocktakingDetail extends CActiveRecord
{
	public $unit;
	/**
	 * 返回基于自身的AR实例
	 * @param string $className 类名
	 * @return CActiveRecord 实例
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string 返回表名
	 */
	public function tableName()
	{
		return '{{stocktaking_detail}}';
	}


	/**
	 * @return array 模型验证规则.
	 */
	public function rules()
	{
		return array(
			array('stocktakingId,positionId,num,singleNumber,productBatch', 'required'),
			array('stocktakingId,positionId,productId', "numerical","integerOnly"=>true),
			array('num', "numerical","integerOnly"=>false),
			array('singleNumber,color,productBatch,positionTitle,unit','safe'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'stocktakingId' => '盘点单ID',
			'num' => '盘点数量',
			'singleNumber' => '产品编码',
			'color' => '颜色',
			'productBatch' => '产品批次',
			'positionId'=>'仓位',
		);
	}
}
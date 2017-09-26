<?php
/**
 * 仓库盘点单--计数 按单品计数
 *
 * @property integer	$id
 * @property integer	$stocktakingId	盘点单ID
  * @property integer	$warehouseId	仓库ID
 * @property integer	$productId		产品ID
 * @property numerical	$num			盘点数量
 * @property string		$singleNumber	单品编码
 * @property TIMESTAMP	$createTime		计算时间
 *
 */

class tbStocktakingCount extends CActiveRecord
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
		return '{{stocktaking_count}}';
	}


	/**
	 * @return array 模型验证规则.
	 */
	public function rules()
	{
		return array(
			array('stocktakingId,warehouseId,num,singleNumber', 'required'),
			array('warehouseId,stocktakingId,productId', "numerical","integerOnly"=>true),
			array('num', "numerical","integerOnly"=>false),
			array('singleNumber','safe'),
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
			'warehouseId'=>'仓库ID',
		);
	}
}
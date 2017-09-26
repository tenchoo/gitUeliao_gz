<?php
/**
 * 仓库调拨单
 *
 * @property integer	$id
 * @property integer	$allocationId	调拨单ID
 * @property integer	$productId		产品ID
 * @property integer	$positionId		仓位ID
 * @property numerical	$num			调拨数量
 * @property integer	$state			状态：0正常，1删除
 * @property string		$singleNumber	单品编码
 * @property string		$color			颜色
 * @property string		$productBatch	产品批次
 * @property string		$positionTitle	仓位名称
 * @property string		$remark			备注
 *
 */

class tbAllocationDetail extends CActiveRecord
{
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
		return '{{allocation_detail}}';
	}


	/**
	 * @return array 模型验证规则.
	 */
	public function rules()
	{
		return array(
			array('allocationId,positionId,num,singleNumber,productId', 'required'),
			array('allocationId,positionId,productId', "numerical","integerOnly"=>true),
			array('num', "numerical",'min'=>'0.1'),
			array('singleNumber,color,remark,positionTitle,productBatch','safe'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'allocationId' => '调拨单ID',
			'positionId' => '仓位ID',
			'num' => '调拨数量',
			'singleNumber' => '产品编码',
			'color' => '颜色',
			'remark' => '备注',
			'productBatch'=>'产品批次',
		);
	}
}
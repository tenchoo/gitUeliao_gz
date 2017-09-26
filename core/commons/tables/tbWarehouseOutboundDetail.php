<?php
/**
 * 仓库出库单明细
 *
 * @property integer	$id
 * @property integer	$outboundId		出库单ID
 * @property integer	$positionId		仓位ID
 * @property numerical	$num			出库数量
 * @property string		$singleNumber	单品编码
 * @property string		$color			颜色
 * @property string     $productBatch   批次
 *
 */

class tbWarehouseOutboundDetail extends CActiveRecord
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
		return '{{warehouse_outbound_detail}}';
	}

	/**
	 * @return array 模型验证规则.
	 */
	public function rules()
	{
		return array(
			array('outboundId,num,positionId,singleNumber,productBatch', 'required'),
			array('outboundId,positionId', "numerical","integerOnly"=>true),
			array('num', "numerical"),
			array('singleNumber,color,productBatch','safe'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'outboundId' => '出库单编号',
			'num' => '出库数量',
			'singleNumber' => '产品编码',
			'color' => '颜色',
			'positionId' => '仓位',
			'productBatch'=>'产品批次',
		);
	}


	 /**
	 * 保存后的操作
	 */
	protected function afterSave(){
		if($this->isNewRecord){
			//出库
			$this->onAfterSave = array('tbWarehouseProduct','event_outbound');
			$this->onAfterSave( new CEvent( $this )  );
		}
	}
}
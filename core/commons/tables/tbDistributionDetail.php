<?php
/**
 * 分配分拣记录明细表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$id
 * @property integer	$distributionId		分配记录表ID
 * @property integer	$warehouseId	 	仓库ID
 * @property integer	$positionId 		仓位ID
 * @property integer	$orderProductId 	订单明细表ID
 * @property integer	$productId		 	对应产品ID
 * @property integer	$unitRate			单位换算量
 * @property integer	$distributionNum	分配数量
 * @property string		$singleNumber		单品编码
 * @property string		$color				颜色
 * @property string		$productBatch		产品批次
 *
 */

 class tbDistributionDetail extends CActiveRecord {

	public $orderId;

	public $positionTitle;

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{distribution_detail}}";
	}

	public function rules() {
		return array(
			array('orderId,distributionId,warehouseId,positionId,orderProductId,productId,distributionNum,productBatch,singleNumber,color','required'),
			array('orderId,distributionId,warehouseId,positionId,orderProductId,productId,unitRate', "numerical","integerOnly"=>true),
			array('distributionNum', "numerical"),
			array('singleNumber,color,productBatch','safe'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'distributionId' => '分配记录ID',
			'orderProductId' => '产品ID',
			'warehouseId' => '仓库ID',
			'productId'=>'产品ID',
			'distributionNum'=>'分配数量',
			'singleNumber'=>'产品编号',
			'color'=>'颜色',
			'productBatch'=>'产品批次',
			'unitRate'=>'单位换算量',

		);
	}


	protected function afterFind(){
		if(  $this->positionId >0 ){
			$model = tbWarehousePosition::model()->findByPk( $this->positionId );
			if( $model ){
				$this->positionTitle = $model->title;
			}
		}

		parent::afterFind();
	}



	/**
	 * 保存后的操作,锁定库存数量
	 */
	protected function afterSave(){
		if($this->isNewRecord){
			$lock = new tbWarehouseLock();
			$lock->type = tbWarehouseLock::TYPE_DISTRIBUTION;
			$lock->sourceId = $this->distributionId;
			$lock->orderId = $this->orderId;
			$lock->warehouseId = $this->warehouseId;
			$lock->num = $this->distributionNum;
			$lock->singleNumber = $this->singleNumber;
			$lock->productBatch = $this->productBatch;
			$lock->positionId = $this->positionId;
			$lock->save();
		}
		return parent::afterSave();
	}
}
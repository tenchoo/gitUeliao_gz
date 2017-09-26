<?php
/**
 * 产品销售订单分拣单表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$packingId				分拣单ID
 * @property integer	$distributionId			分配记录表ID
 * @property integer	$orderId				订单ID
 * @property integer	$warehouseId			分拣仓库ID
 * @property integer	$deliveryWarehouseId	发货仓库ID
 * @property integer	$userId					分拣操作人userId
 * @property integer	$state					状态：０待分拣，1已确认分拣,4订单取消
 * @property timestamp	$createTime				分拣单生成时间
 * @property timestamp	$packingTime			确认分拣时间
 *
 */

 class tbPacking extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{packing}}";
	}

	public function primaryKey() {
        return 'packingId';
    }

	public function relations(){
		return array(
			'order'=>array(self::BELONGS_TO,'tbOrder','orderId'),
			'orderTime'=>array(self::BELONGS_TO,'tbOrder','orderId','select'=>'createTime,state'),
			'detail'=>array(self::HAS_MANY,'tbPackingDetail','packingId'),
			'operator'=>array(self::BELONGS_TO,'tbUser','userId', 'select'=>'username'),
			'distribution'=>array(self::HAS_MANY,'tbDistributionDetail','','on'=>'t.distributionId = distribution.distributionId and t.warehouseId = distribution.warehouseId'),
		);
	}

	public function rules() {
		return array(
			array('orderId,warehouseId,deliveryWarehouseId,distributionId','required'),
			array('orderId,warehouseId,deliveryWarehouseId,state,distributionId', "numerical","integerOnly"=>true),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'packingId' => '分拣单ID',
			'orderId' => '订单ID',
			'userId'=>'分拣单提交人userId',
			'state'=>'状态',
			'distributionId'=>'分配记录表ID',
		);
	}


	/**
	 * 自动生成待分拣的分拣单
	 * @param CActiveRecord $record
	 * @return boolean
	 */
	private static function createPacking( $record ) {
		$model = new tbPacking();

		$model->distributionId = $record->distributionId;
		$model->orderId = $record->orderId;
		$model->warehouseId = $record->warehouseId;
		$model->deliveryWarehouseId = $record->deliveryWarehouseId;
		$model->state = 0;

		if($model->save()){
			return true;
		}else{
			$record->addErrors($model->getErrors());
			return false;
		}
	}

	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
		}
		return true;
	}

}
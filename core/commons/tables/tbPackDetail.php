<?php
/**
 * 新分拣单明细
 * @author liang
 * @version 0.2
 *
 * @property int    $id
 * @property int    $orderProductId   订单产品ID
 * @property int    $positionId  	  分区ID
 * @property int    $wholes		  	  整码数量：0为零码
 * @property number $packingNum 	  分拣数量
 * @property string $positionTitle	  仓位名称
 * @property string $productBatch	  产品批次
 */
class tbPackDetail extends CActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model( $className );
	}

	public function tableName() {
		return '{{pack_detail}}';
	}


	public function rules() {
		return array(
			array('wholes,orderProductId,positionId,packingNum','required'),
			array('wholes,orderProductId,positionId', "numerical","integerOnly"=>true),
			array('packingNum', "numerical"),
			array('productBatch,positionTitle','safe'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'orderProductId' => '产品ID',
			'positionId' => '仓位ID',
			'packingNum'=>'分拣数量',
			'productBatch'=>'产品批次',
			'remark'=>'分拣说明',
		);
	}

	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			//产品批次，预留字段，先设置默认值
			$this->productBatch = tbWarehouseProduct::DEATULE_BATCH;
		}

		return parent::beforeSave();
	}

}

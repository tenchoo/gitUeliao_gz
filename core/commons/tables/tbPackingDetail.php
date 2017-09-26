<?php
/**
 * 产品销售订单分拣单明细表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$id
 * @property integer	$packingId			分拣单ID
 * @property integer	$orderProductId 	订单明细表ID
 * @property integer	$productId		 	订单明细表ID
 * @property integer	$positionId			仓位ID
 * @property integer	$unitRate			单位换算量
 * @property numerical	$packingNum			分拣数量
 * @property string		$singleNumber		单品编码
 * @property string		$color				颜色
 * @property string		$productBatch		产品批次
 * @property string		$positionTitle		仓位名称
 *
 */

 class tbPackingDetail extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{packing_detail}}";
	}

	public function rules() {
		return array(
			array('packingId,orderProductId,positionId,productId','required'),
			array('packingId,orderProductId,positionId,unitRate,productId', "numerical","integerOnly"=>true),
			array('packingNum', "numerical"),
			array('singleNumber,color,productBatch,positionTitle','safe'),
			array('orderProductId','checkExists','on'=>'insert'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'packingId' => '分拣单ID',
			'orderProductId' => '产品ID',
			'positionId' => '仓位ID',
			'packingNum'=>'分拣数量',
			'singleNumber'=>'产品编号',
			'color'=>'颜色',
			'productBatch'=>'产品批次',
			'unitRate'=>'单位换算量',
			'productId'=>'产品ID',
		);
	}

	/**
	* 检查是否存在
	*/
	public function checkExists( $attribute,$params ){
		if( empty( $this->$attribute ) ){
			return;
		}

		$criteria=new CDbCriteria;
		$criteria->compare('orderProductId',$this->orderProductId);
		$criteria->compare('packingId',$this->packingId);
		$criteria->compare('positionId',$this->positionId);
		$criteria->compare('productBatch',$this->productBatch);

		$model = $this->exists( $criteria );
		if( $model ){
			$label = $this->getAttributeLabel($attribute);
			$this->addError($attribute,'请不要重复提交分拣');
		}
	}

}
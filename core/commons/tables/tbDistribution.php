<?php
/**
 * 订单产品分配记录表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$distributionId			分配ID
 * @property integer	$orderId				订单ID
 * @property integer	$deliveryWarehouseId	发货仓库ID
 * @property integer	$userId					分拣操作人userId
 * @property timestamp	$createTime				分配时间
 *
 */

 class tbDistribution extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{distribution}}";
	}

	public function primaryKey() {
        return 'distributionId';
    }

	public function relations(){
		return array(
			'order'=>array(self::BELONGS_TO,'tbOrder','orderId'),
			'detail'=>array(self::HAS_MANY,'tbDistributionDetail','distributionId'),
			'operator'=>array(self::BELONGS_TO,'tbUser','', 'on' => 't.userId=operator.userId','select'=>'username'),
		);
	}

	public function rules() {
		return array(
			array('orderId,deliveryWarehouseId','required'),
			array('orderId,deliveryWarehouseId', "numerical","integerOnly"=>true),
			array('orderId', "unique"),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'distributionId' => '分配ID',
			'orderId' => '订单ID',
			'userId'=>'分配提交人userId',
		);
	}

	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
			$this->userId = Yii::app()->user->id;
		}
		return true;
	}

	protected function afterSave(){
		if($this->isNewRecord){
			tbOrderMessage::addMessage( $this->orderId,'has_distribution' );
		}
	}

}
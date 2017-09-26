<?php
/**
 * 仓库调拨单
 *
 * @property integer	$allocationId		调拨单ID
 * @property integer	$warehouseId		调拨仓库ID
 * @property integer	$targetWarehouseId	目标仓库Id
 * @property integer	$orderId			订单编号
 * @property integer	$userId				调拨人userId
 * @property integer	$comfirmUserId		确认调拨人userId
 * @property integer	$state				状态：0待调拨,1待确认调拨,2调拨完成,10取消调拨
 * @property integer	$driverUserId		驾驶员userId
 * @property integer	$vehicleId			车辆编号
 * @property timestamp	$createTime			新建时间
 * @property timestamp	$comfirmTime		确认调拨时间
 * @property string		$userName			调拨人
 * @property string		$driverName			驾驶员姓名
 * @property string		$plateNumber		车牌号
 * @property string		$comfirmUser		确认调拨人姓名
 * @property string		$remark				备注
 *
 */

class tbAllocation extends CActiveRecord
{
	CONST TYPE_NOMAL = 0;//新建内部调拨
	CONST TYPE_ORDER = 1;//订单调拨
	CONST TYPE_CALLBACK = 2;//回调调拨

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
		return '{{allocation}}';
	}

	public function relations(){
		return array(
			'detail'=>array(self::HAS_MANY,'tbAllocationDetail','allocationId'),
		);
	}


	/**
	 * @return array 模型验证规则.
	 */
	public function rules()
	{
		return array(
			array('warehouseId,targetWarehouseId,driverUserId', 'required'),
			array('warehouseId,targetWarehouseId,orderId,userId,comfirmUserId,state,driverUserId,vehicleId', 'numerical', 'integerOnly'=>true),
			array('userName,driverName,comfirmUser,remark,comfirmTime,createTime,plateNumber','safe'),
			array('packingId','checkExists','on'=>'insert'),
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
		$criteria->compare('isCallback',$this->isCallback);
		$criteria->compare('packingId',$this->packingId);

		$model = $this->exists( $criteria );
		if( $model ){
			$label = $this->getAttributeLabel($attribute);
			$this->addError($attribute,'请不要重复提交数据');
		}
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'warehouseId' => '调拨仓库',
			'targetWarehouseId' => '目标仓库Id',
			'orderId' => '订单编号',
			'driverUserId' => '驾驶员',
			'remark' => '备注',
			'remark' => '备注',
			'remark' => '备注',
		);
	}


	public function init(){
		$this->userName ='';
		$this->driverName ='';
		$this->plateNumber ='';
		$this->comfirmUser ='';
		$this->remark ='';

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
}
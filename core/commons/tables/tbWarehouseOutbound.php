<?php
/**
 * 出库单表
 *
 * @property integer	$outboundId		出库单Id
 * @property integer	$source			入库类型 0:发货出货 1:调拨出库,2:调整出库
 * @property integer	$sourceId		来源单号
 * @property integer	$warehouseId	仓库ID
 * @property integer	$userId			操作人userId
 * @property timestamp	$createTime		创建时间
 * @property timestamp	$realTime		实际出库时间
 * @property string		$operator		操作员
 * @property string		$remark			备注
 *
 */
class tbWarehouseOutbound extends CActiveRecord
{
	//发货出货
	const TO_DELIVERY = 0;
	//调拨出库
	const TO_ALLOCATION = 1;

	//调整出库
	const TO_ADJUST = 2;

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
		return '{{warehouse_outbound}}';
	}

	public function init() {
		$this->createTime  = new CDbExpression('NOW()');
		$this->realTime    =  new CDbExpression('NOW()');
		$this->userId      = Yii::app()->user->id;
		$this->operator    = Yii::app()->getUser()->getstate('username');
	}

	/**
	 * @return array 模型验证规则.
	 */
	public function rules()
	{
		return array(
			array('warehouseId,source,sourceId', 'required'),
			array('source','in','range'=>array(0,1,2,3,4)),
			array('warehouseId,sourceId', "numerical","integerOnly"=>true,'min'=>1),
			array('remark', 'safe'),
			array('sourceId','checkExists','on'=>'insert'),
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
		$criteria->compare('source',$this->source);
		$criteria->compare('sourceId',$this->sourceId);

		$model = $this->exists( $criteria );
		if( $model ){
			$label = $this->getAttributeLabel($attribute);
			$this->addError($attribute,'请不要重复出库');
		}
	}

	public function relations(){
		return array(
			'detail'=>array(self::HAS_MANY,'tbWarehouseOutboundDetail','outboundId'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'warehouseId' => '仓库ID',
			'source'=>'出库类型',
		);
	}
}
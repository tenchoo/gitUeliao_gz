<?php

/**
 * 采购单取消操作记录表模型.
 *
 * @property integer	$purchaseId		采购单ID
 * @property integer	$opId			操作者ID
 * @property timestamp	$createTime		取消订单时间
 * @property string		$reason			取消理由
 */

class tbPurchasingClose extends CActiveRecord
{

	public function init()
	{
		parent::init();
		$this->opId = Yii::app()->user->id;
		$this->createTime = new CDbExpression('NOW()');
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return tbOrderClose the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{purchasing_close}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('purchaseId,opId,reason', 'required'),
			array('purchaseId,opId', 'numerical', 'integerOnly'=>true),
			array('reason', 'safe'),
		);
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'purchaseId' => '采购单',
			'opId' => '操作者ID',
			'reason' => '取消订单原因',
		);
	}
}
<?php

/**
 * This is the model class for table "{{express_com}}".
 *
 * The followings are the available columns in table '{{express_com}}':
 * @property integer $companyId 物流公司ID
 * @property string $companyName 物流公司名称
 * @property string $companyMark 标识
 */
class ExpressCom extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ExpressCom the static model class
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
		return '{{express_com}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()	{
		return array(
			array('companyName, companyMark', 'required'),
			array('companyName, companyName', 'length', 'max'=>50),
			array('companyMark, companyName', 'safe'),
		);
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'companyId' => '物流公司ID',
			'companyName' => '物流公司名称',
			'companyMark' => '标识',
		);
	}

	

}
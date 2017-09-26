<?php

/**
 * This is the model class for table "{{sessions}}".
 *
 * The followings are the available columns in table '{{sessions}}':
 * @property integer $zm_id
 * @property string $zse_sesskey
 * @property integer $zse_expiry
 * @property integer $zse_admin
 * @property string $zse_ip
 * @property string $zse_data
 * @property integer $zse_overflow
 */
class Sessions extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Sessions the static model class
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
		return '{{sessions}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('zm_id, zse_expiry, zse_admin, zse_overflow', 'numerical', 'integerOnly'=>true),
			array('zse_sesskey', 'length', 'max'=>32),
			array('zse_ip', 'length', 'max'=>15),
			array('zse_data', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('zm_id, zse_sesskey, zse_expiry, zse_admin, zse_ip, zse_data, zse_overflow', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'zm_id' => 'Zm',
			'zse_sesskey' => 'Zse Sesskey',
			'zse_expiry' => 'Zse Expiry',
			'zse_admin' => 'Zse Admin',
			'zse_ip' => 'Zse Ip',
			'zse_data' => 'Zse Data',
			'zse_overflow' => 'Zse Overflow',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('zm_id',$this->zm_id);
		$criteria->compare('zse_sesskey',$this->zse_sesskey,true);
		$criteria->compare('zse_expiry',$this->zse_expiry);
		$criteria->compare('zse_admin',$this->zse_admin);
		$criteria->compare('zse_ip',$this->zse_ip,true);
		$criteria->compare('zse_data',$this->zse_data,true);
		$criteria->compare('zse_overflow',$this->zse_overflow);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
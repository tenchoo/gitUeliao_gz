<?php

/**
 * This is the model class for table "{{weiapi}}".
 *
 * The followings are the available columns in table '{{weiapi}}':
 * @property integer $zwe_id
 * @property string $zwe_sid
 * @property string $zwe_source
 * @property integer $zwe_type
 * @property string $zwe_title
 * @property string $zwe_url
 */
class Weiapi extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Weiapi the static model class
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
		return '{{weiapi}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('zwe_title,zwe_sid','required'),
			array('zwe_sid', 'unique'),
			array('zwe_type,zwe_cache,zwe_db,zwe_listorder', 'numerical', 'integerOnly'=>true),
			array('zwe_sid,zwe_ico', 'length', 'max'=>100),
			array('zwe_source', 'length', 'max'=>4),
			array('zwe_title,zwe_table', 'length', 'max'=>255),
			array('zwe_url,zwe_sql', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('zwe_id, zwe_sid, zwe_source, zwe_type, zwe_title, zwe_url,zwe_cache', 'safe', 'on'=>'search'),
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
			'zwe_id' => 'ID',
			'zwe_sid' => '标识',
			'zwe_source' => '数据源格式',
			'zwe_type' => '页面类型',
			'zwe_title' => '标题',
			'zwe_url' => '接口地址',
			'zwe_cache' => '缓存时间',
			'zwe_db' => '接口类型',
			'zwe_table' => '查询表',
			'zwe_sql' => '自定义查询语句',
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

		$criteria->compare('zwe_id',$this->zwe_id);
		$criteria->compare('zwe_sid',$this->zwe_sid,true);
		$criteria->compare('zwe_source',$this->zwe_source,true);
		$criteria->compare('zwe_type',$this->zwe_type);
		$criteria->compare('zwe_title',$this->zwe_title,true);
		$criteria->compare('zwe_url',$this->zwe_url,true);
		$criteria->compare('zwe_cache',$this->zwe_cache,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	

	/**
	 * 类型
	 * @param $type  值
	 */
	public function getType($type=null){
		if($type==null){
			return array(
					'1'=>'单页',
					'2'=>'列表',
					'3'=>'其它'	
				);
		}else{
			$data = $this->getType();
			if(array_key_exists($type,$data))
				return $data[$type];
		}
	}

	/**
	 * 数据源格式
	 * @param $type  值
	 */
	public function getSource($type=null){
		if($type==null){
			return array(
					'json'=>'json',
					'xml'=>'xml',					
				);
		}else{
			$data = $this->getType();
			if(array_key_exists($type,$data))
				return $data[$type];
		}
	}

	/**
	 * 接口类型
	 * @param $type  值
	 */
	public function getApitype($type=null){
		if($type==null){
			return array(
					'1'=>'数据接口',
					'2'=>'数据库',					
				);
		}else{
			$data = $this->getType();
			if(array_key_exists($type,$data))
				return $data[$type];
		}
	}
}
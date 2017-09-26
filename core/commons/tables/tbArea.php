<?php

/**
 * 地域表  "{{area}}".
 *
 * The followings are the available columns in table '{{area}}':
 * @property integer $areaId
 * @property integer $parentid
 * @property string $title
 * @property integer $listorder
 */
class tbArea extends CActiveRecord
{
	public $child;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Area the static model class
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
		return '{{area}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title', 'required'),
			array('parentid,listOrder', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>255),

			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('areaId,parentid, title, listOrder', 'safe', 'on'=>'search'),
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
			'areaId' => 'ID',
			'parentid' => '上级ID',
			'title' => '标题',
			'listOrder' => '排序',
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

		$criteria->compare('areaId',$this->areaId);
		$criteria->compare('parentid',$this->parentid);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('listOrder',$this->listOrder);
		$criteria->order = " listOrder DESC ";
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}


	/**
	 * 查找 某个地址的父级信息
	 *@param $filed 一个字符串，或者用逗号隔开
	 */
	public function getParentInfoByid($zar_id,$filed='parentid'){
		$criteria =  new CDbCriteria;
		$criteria ->select=$filed;
		$criteria ->condition='areaId=:zar_id';
		$criteria ->params=array(':zar_id'=>$zar_id);
		return Area::model()->find($criteria);
	}


	/**
	 * 查找某一级下面的全部子项
	 * @param $parentid 父级id
	 */
	public function getParentList($parentid='0')
	{
		$model = self::model()->findAllByAttributes(array('parentid'=>$parentid));
		return CHtml::listData($model, 'areaId', 'title');
	}



	/**
	 * 返回子菜单列表
	* */
	public function loadChild($parentid){
		$criteria=new CDbCriteria;
		$criteria->compare('parentid',$parentid);
		$criteria->order = 'listOrder ASC, areaId ASC';
		return new CActiveDataProvider($this, array(
				'criteria'=>$criteria,
				'pagination'=>array(
						'pageSize'=>100,
				)
		));
	}
	/**
	 * 根据最低一级id返回完整省市县信息
	 * @param int $id 最低一级id
	 * @return $return 数组
	 */
	public function getFullAreaByFloorId($id)
	{
		$model = self::model()->findByPk($id);
		$area = array();
		if($model){
			$area1 = $model->attributes;
			$area['area1'] = $area1;
			$model = $model->getParentInfoByid($area1['parentid'],'*');
			if($model){
				$area2 = $model->attributes;
				$area['area2'] = $area2;
				$model = $model->getParentInfoByid($area2['parentid'],'*');
				if($model){
					$area3 = $model->attributes;
					$area['area3'] = $area3;
				}
			}
		}
		return $area;
	}

	/**
	 * 根据最低一级id返回完整省市县信息
	 * $id 最低一级id
	 * $return 字符串
	 */
	public static function getAreaStrByFloorId($id)
	{
		$str = '';
		while ( $id ) {
			$model = self::model()->findByPk($id);
			if( $model ) {
				$id = $model->parentid;
				$str =$model->title .$str;
			}else{
				$id = 0;
			}
		}
		return $str;
	}
	
	/**
	 * 根据最低一级id返回完整省市县信息
	 * $id 最低一级id
	 * $return array 地区数据数组
	 */
	public static function getAreaArrByFloorId($id)
	{
		$arr = array();
		while ( $id ) {
			$model = self::model()->findByPk($id);
			if( $model ) {
				array_unshift($arr,array('areaId'=>$model->areaId,'title'=>$model->title));
			
				$id = $model->parentid;
			}else{
				$id = 0;
			}
		}
		return $arr;
	}

	/**
	 * 返回完整地址
	 * @return Ambigous <string, unknown>
	 */
	public function getAddress(){
		return $this->getAreaStrByFloorId($this->areaId);
	}

	/**
	 * 读取地域缓存
	 * @return array
	 */
	public function  getCache(){
		$data = json_decode(Yii::app()->cache->get('area'),true);//获取缓存
		//如果缓存不存在，则直接读取数据库
		if(empty($data)){
			$province = array();
			$city = array();
			$county = array();

			$tbArea = new tbArea();
			$model = $tbArea->findAll('level<4');
			foreach ( $model as $key=>$val ){
				if( $val->parentid =='0'){
					$province[$val->areaId] =  $val->getAttributes(array('areaId','title'));
				}else if( isset( $province[$val->parentid] ) ){
					$city[$val->parentid][$val->areaId] = $val->title;
				}else{
					$county[$val->parentid][$val->areaId] = $val->title;
				}
				unset($model[$key]);
			}

			$data['provinces']=$province;
			$data['citys']=$city;
			$data['countys']=$county;
			Yii::app()->cache->set('area',CJSON::encode($data),3600*24);
			return $data;
		}
		else{
			return $data;
		}
	}


	/**
	 * 地域片区
	 * */
	public function getGroups($type=null){
		if($type==null){
			return array(
					'1'=>'华东',
					'2'=>'华北',
					'3'=>'华中',
					'4'=>'华南',
					'5'=>'东北',
					'6'=>'西北',
					'7'=>'西南',
					'8'=>'港澳台',
					'9'=>'海外',
			);
		}else{
			$level = $this->getGroups();
			if(array_key_exists($type,$level))
				return $level[$type];
		}
	}


	/**
	 * 获取运送地区
	 */
	public function getAreas($areaid){
		$criteria = new CDbCriteria;
		$criteria->compare('areaId',explode(',',$areaid));
		$result = $this->findAll($criteria);
		$data = CHtml::ListData($result,'areaId','title');
		return join(',',$data);
	}

}
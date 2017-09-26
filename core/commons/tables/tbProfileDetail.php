<?php

/**
 * 会员企业详细信息
 *
 * @property string $memberId
 * @property string $companyname
 * @property integer $areaId
 * @property string $address
 * @property string $corporate
 * @property string $tel
 * @property integer $companytype
 * @property integer $saleregion
 * @property string $mainproduct
 * @property integer $peoplenumber
 * @property integer $outputvalue
 * @property string $brand
 * @property integer $stalls
 * @property string $stallsaddress
 * @property integer $factory
 * @property integer $factoryatt
 * @property string $gm
 * @property string $pdm
 * @property string $designers
 * @property string $cfo
 */
class tbProfileDetail extends CActiveRecord {

	//初始化数据
	public function init() {
		$this->tel = '';
		$this->brand = '';
		$this->corporate = '';
		$this->companyname = '';
		$this->mainproduct = '';
		$this->gm = '';
		$this->pdm = '';
		$this->designers = '';
		$this->cfo = '';
		$this->address = '';
		$this->stallsaddress = '';
	}

	/**
	 * @return string 数据库表名称
	 */
	public function tableName()
	{
		return '{{profile_detail}}';
	}

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	/**
	 * @return array 验证字段.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('memberId, companyname, shortname', 'required','on'=>'modify'),
			array('areaId, companytype, saleregion, peoplenumber, outputvalue, stalls, factory, factoryatt', 'numerical', 'integerOnly'=>true),
			array('memberId', 'length', 'max'=>10),
			array('shortname', 'length', 'max'=>10,'min'=>'2'),
			array('companyname', 'length', 'max'=>80,'min'=>'2'),
			array('address, stallsaddress', 'length', 'max'=>255),
			array('corporate, brand', 'length', 'max'=>20),
			array('tel', 'length', 'max'=>15),
			array('mainproduct', 'length', 'max'=>60),
			array('gm, pdm, designers, cfo', 'length', 'max'=>120),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('memberId, companyname, areaId, address, corporate, tel, companytype, saleregion, mainproduct, peoplenumber, outputvalue, brand, stalls, stallsaddress, factory, factoryatt, gm, pdm, designers, cfo', 'safe', 'on'=>'search'),
			array('companyname, shortname', 'unique','on'=>'modify'),
		);
	}

	/**
	 * @return array relational 关联表.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array label字段说明
	 */
	public function attributeLabels()
	{
		return array(
			'memberId' => '会员ID',
			'companyname' => '公司名称',
			'shortname' => '公司简称',
			'areaId' => '省市ID',
			'address' => '详细地址',
			'corporate' => '企业法人',
			'tel' => '电话',
			'companytype' => '公司性质',
			'saleregion' => '销售区域',
			'mainproduct' => '主营产品',
			'peoplenumber' => '生产人数',
			'outputvalue' => '年产出',
			'brand' => '品牌',
			'stalls' => '有无档口',
			'stallsaddress' => '档口地址',
			'factory' => '有无工厂',
			'factoryatt' => '工厂属性',
			'gm' => '总经理',
			'pdm' => '采购经理',
			'designers' => '设计人员',
			'cfo' => '财务经理',
		);
	}


	/**
	* 根据公司名称查找用户,模糊匹配查找-联想搜索
	* @param string $keyword
	*/
	public function search( $keyword,$limit = '10'  ){
		if( empty( $keyword ) ){
			return;
		}
		$criteria=new CDbCriteria;
		$criteria->select ='t.memberId,t.companyname,t2.priceType as areaId';
		$criteria->addSearchCondition('t.companyname', $keyword);

		$userType = Yii::app()->user->getState('usertype');
		if( $userType == 'saleman'){
			//业务员只能查找自己服务的客户
			$userId[] = Yii::app()->user->id ;
			if( tbConfig::model()->get( 'default_saleman_id' ) == $userId['0'] ){
				$userId[] = 0;
			}
			$userId = implode(',',$userId);
			$criteria->join = " join {{member}} t2 on( t.memberId=t2.memberId and t2.state = 'Normal' and t2.groupId != 1 and t2.userId in( $userId ) )";
		}else{
			//连接表
			$criteria->join = " join {{member}} t2 on( t.memberId=t2.memberId and t2.state = 'Normal and t2.groupId != 1' )";
		}

		$criteria->limit = $limit;
		$model = $this->findAll( $criteria );

		$result = array();
		foreach( $model as $val ){
			$result[$val->memberId]['id'] = $val->memberId;
			$result[$val->memberId]['title'] = $val->companyname;
		}

		return $result ;
	}
	
	public function companyname( $memberId ){
		$model = $this->findByPk( $memberId );
		if( $model ){
			return $model->companyname;
		}
	}
}

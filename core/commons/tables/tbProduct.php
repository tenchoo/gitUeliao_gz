<?php
/**
 * 产品基本信息
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$productId		产品ID
 * @property integer	type			产品类型。默认0为主产品，1为附属产品
 * @property integer	baseProductId  	主产品ID，针对附属工艺产品
 * @property integer	$categoryId		产品分类
 * @property integer	$state			产品状态
 * @property numerical	$unitId	 		基础单位
 * @property integer	$auxiliaryUnit	辅助单位
 * @property numerical	$unitConversion	单位换算量
 * @property integer	$expressId		运费模板ID
 * @property numerical	$price			默认价格，散剪价
 * @property numerical	$tradePrice		大货价
 * @property numerical	$unitWeight		单位重量
 * @property numerical	$salesVolume	销售量
 * @property timestamp	$createTime		创建时间
 * @property timestamp	$updateTime		更新时间
 * @property timestamp	$publishTime	发布时间
 * @property timestamp	$salesTime		上架时间
 * @property string		$title			产品标题
 * @property string		$serialNumber	产品编号
 * @property string		$mainPic		产品主图
 *
 */

 class tbProduct extends CActiveRecord {

	const TYPE_MAIN = 0;
	const TYPE_CRAFT = 1;

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{product}}";
	}

	public function rules() {
		return array(
			array('categoryId,serialNumber,title,mainPic','required'),
			array('categoryId,state,unitId,expressId,auxiliaryUnit,baseProductId','numerical','integerOnly'=>true),
			array('unitConversion,unitWeight','numerical'),
			array('price,tradePrice', "numerical",'max'=>'10000'),
			array('serialNumber', "length",'max'=>'18'),
		//	array('publishTime', 'type', 'type'=>'datetime','datetimeFormat'=>'yyyy-MM-dd hh:mm:ss','allowEmpty'=>true ),
			array('serialNumber,title,mainPic','safe'),
			array('serialNumber','match','pattern'=>'/^[a-zA-Z][a-zA-Z0-9\-]+$/','message'=>Yii::t('product','serial must be in English')),
			array('serialNumber','unique'),

		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'productId' => '产品ID',
			'categoryId' => '产品分类ID',
			'state' => '产品状态',
			'price' => '散剪价',
			'tradePrice' => '大货价',
			'unitId' => '基础单位',
			'expressId' => '运费模板ID',
			'publishTime' => '发布时间',
			'title' => '产品标题',
			'serialNumber'=>'产品编号',
			'mainPic' => '产品主图',
			'auxiliaryUnit' => '辅助单位',
			'unitConversion' => '单位换算量',
			'unitWeight' => '单位重量',

		);
	}


	 /**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->updateTime = $this->createTime = new CDbExpression('NOW()');
		}else{
			$this->updateTime = new CDbExpression('NOW()');
		}
		if( $this->unitConversion == '' ){
			$this->unitConversion = '0';
		}

		if( $this->unitWeight == '' ){
			$this->unitWeight = '0';
		}

		if( $this->auxiliaryUnit == '' ){
			$this->auxiliaryUnit = '0';
		}


		$this->serialNumber = trim( $this->serialNumber );
		$this->title = trim( $this->title );
		return true;
	}


	public function relations() {
		return array(
			'property'=>array(self::HAS_MANY,'tbProductAttribute','productId'),
			'specifications'=>array(self::HAS_MANY,'tbProductSpec','productId'),
			'detail'=>array(self::HAS_ONE,'tbProductDetail','productId'),
			'unit'=>array(self::BELONGS_TO,'tbUnit','unitId')
			//	'commentCount'=>array(self::HAS_MANY,'tbOrderPayment','productId'),
		);
	}

	/**
	 * 根据ID获取产品信息
	 * @param array $ids
	 * @return array
	 */
	public function searchAllByPk( $ids ) {
		$cmd = $this->getDbConnection()->createCommand();
		$cmd->select('productId,categoryId,title,price,tradePrice,mainPic,unitId,serialNumber,state');
		$cmd->from( $this->tableName() );
		$cmd->where( array('in','productId',$ids) );
		$products = $cmd->queryAll();

		$productData = array_fill_keys($ids, null);
		foreach( $products as $item ) {
			$item['unit'] =  tbUnit::getUnitName( $item['unitId'] );
			$productData[$item['productId']] = $item;
		}
		return $productData;
	}

	/**
	* 根据产品ID取得产品的单位/辅助单位和换算率
	* @param array $productIds
	*/
	public function getUnitConversion( array $productIds ){
		if( empty( $productIds )) return ;

		$productIds = implode(',',array_unique($productIds));
		$units = tbUnit::model()->getUnits();
		$tbProduct = tbProduct::model()->findAll(
					array( 'select'=>'productId,unitConversion,unitId,auxiliaryUnit',
						  'condition'=>'productId in ('.$productIds.')',
					));
		$pInfo = array();
		foreach ( $tbProduct as $val){
			$pInfo[$val->productId]['unitConversion'] = ($val->unitConversion>0)?$val->unitConversion:'';
			$pInfo[$val->productId]['unit'] = isset($units[$val->unitId])?$units[$val->unitId]:'';
			$pInfo[$val->productId]['auxiliaryUnit'] =  isset($units[$val->auxiliaryUnit])?$units[$val->auxiliaryUnit]:'';
		}

		return $pInfo;
	}

	/**
	* 取得产品的工艺信息
	* @param integer $productId
	*/
	public function getCrafts(){
		if( empty( $this->productId ) ) return array();

		$codes = tbProductCraft::model()->getAllCraft( $this->productId );
		if( empty( $codes ) ) return array();

		$crafts = tbCraft::model()->getCrafts( $codes );
		return array_values( $crafts );

	}

	/**
	* 取得产品的工艺列表
	* @param boolean $hasSelf 是否包含自身
	*/
	public function getCraftList( $hasSelf = false ){
		if( empty( $this->productId ) ) return array();

		if( $this->type == tbProduct::TYPE_CRAFT ){
			if( empty( $this->baseProductId ) ) return array();
			$condition = 'baseProductId = '.$this->baseProductId.' or productId = '.$this->baseProductId;
		}else{
			$condition = 'baseProductId = '.$this->productId;
		}

		$model = $this->findAll( array(
				'select'=>'productId,serialNumber,type,mainPic',
				'condition'=>$condition,
				'order'=>'serialNumber asc'
			) );

		$list = array();
		foreach ( $model as $val ){
			if( !$hasSelf && $val->productId == $this->productId ) continue;
			$crafts = $val->getCraftTitle();
			$title = empty( $crafts )?$val->serialNumber:$val->serialNumber.' '.$crafts;
			$list[] = array('productId'=>$val->productId,'title'=>$title, 'mainPic'=>$val->mainPic );
		}

		return $list;
	}

	public function getCraftTitle(){
		if( $this->type != tbProduct::TYPE_CRAFT ) return '';
		$crafts = $this->getCrafts();
		$crafts = array_map ( function ($i){ return $i['title'];},$crafts);
		return implode('-',$crafts);
	}

	/**
	* 产品图片规格大小类型，为缩略图，大小为正方形，单位PX
	*
	*/
	public static function imgSizes(){
		return array('50','80','100','160','200','600');
	}

	/**
	* 取得自增的ID,生成尾货产品时调用
	*/
	public function getAutoIncrement(){
		$tableName = $this->tableSchema->name;
		$str = explode(';',substr(Yii::app()->db->connectionString,6));
		foreach( $str as $val ){
			if( strpos($val,"dbname") !== false  ){
				$str = explode('=',$val);
				$dbname = $str['1'];
				break;
			}
		}

		$sql = "SELECT AUTO_INCREMENT FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name =  '$tableName' LIMIT 0 , 30";
		$command = Yii::app()->db->createCommand( $sql );
		$dataReader = $command->queryAll();
		$auto = (int)$dataReader[0]['AUTO_INCREMENT'];

		$sql = "ALTER TABLE $tableName auto_increment= ".($auto+1);
		$command = Yii::app()->db->createCommand( $sql );
		$command->execute();

		return $auto;
	}

}
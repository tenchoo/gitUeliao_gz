<?php
/**
 * 发布产品,销售信息
 * @version 0.1
 * @package CFormModel
 */
class SaleInfoForm extends CFormModel {

	/**
	 * @var integer
	 */
	public $unitId,$productId, $timeType,$auxiliaryUnit,$expressId,$categoryId;

	/**
	 * @var string
	 */
	public $specs,$specStock;

	public $publishTime;

	public $price,$tradePrice,$unitConversion,$unitWeight,$serialNumber,$craft;


	private $_product ;

	public $productType;

	private $_spec = array();
	/**
	* Declares the validation rules.
	* The rules state that account and password are required,
	* and password needs to be authenticated.
	*/
	public function rules()	{
		return array(
			array('unitId,price,tradePrice,unitWeight', 'required'),
			array('publishTime', 'required','on'=>'setTime'),			
			array('timeType', 'in', 'range'=>array('1','2')),
			array('unitId,auxiliaryUnit,expressId,categoryId', "numerical","integerOnly"=>true),
			array('price,tradePrice','numerical','min'=>'0.01','max'=>'100000'),
			array('unitConversion,unitWeight', "numerical","integerOnly"=>false),
			array('publishTime', 'type', 'type'=>'datetime','datetimeFormat'=>'yyyy-MM-dd hh:mm:ss'),
			array('specs,specStock,serialNumber,craft','safe')
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签
	*/
	public function attributeLabels() {
		return array(
			'unitId' => '基础单位',
			'specs' => '规格库存设置',
			'specStock' => '规格库存设置',
			'price' => '零售价',
			'tradePrice'=>'大货价',
			'expressId'=>'物流模板ID',
			'auxiliaryUnit'=>'辅助单位',
			'unitConversion'=>'换算量',
			'publishTime'=>'发布时间',
			'unitWeight' => '单位重量',
			'craft'=>'特殊工艺',
			'serialNumber'=>'产品编号',
		);
	}

	public function save() {
		$this->price = str_replace(',','',$this->price );
		$this->tradePrice = str_replace(',','',$this->tradePrice );

		if($this->timeType =='1'){
			$this->publishTime = null;
		}else{
			$this->scenario = 'setTime';
		}
		
		if( !$this->validate() ) {
			return false ;
		}

		//使用事务处理，以确保这组数据全部成功插入
		$transaction = Yii::app()->db->beginTransaction();
		try {
			//因要计算总库存量，所以规格库存要先处理
			$product = $this->_product;

			//保存价格信息
			$product->attributes = $this->getAttributes( array('unitId','price','tradePrice','auxiliaryUnit','unitConversion','expressId','unitWeight') );
			if($this->timeType =='1'){
				$product->publishTime = '0000-00-00 00:00:00';
				if( $product->state != 0 ){
					$product->state = 0;
					$product->salesTime = date('Y-m-d H:i:s');//上架时间
				}
			}else{
				$product->publishTime = $product->salesTime = $this->publishTime;//上架时间
			}
			if( !$product->save() ) {
				$this->addErrors( $product->getErrors() );
				return false;
			}

			$this->productId = $product->productId;

			

			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			throw new CHttpException(503,$e);
			return false;
		}
	}

	
	public function getInfo( $productId ){
		if( empty( $productId ) ){
			return ;
		}

		//取得产品信息
		$product = tbProduct::model()->findByPk( $productId );
		if ( !$product ) return ;

		$this->_product = $product;

		$this->attributes = $product->getAttributes( array( 'categoryId','unitId','price','tradePrice','auxiliaryUnit','unitConversion','expressId','unitWeight','publishTime','serialNumber') ) ;
		$this->productType =  $product->type;
		$this->productId = $productId;
		if($this->publishTime =='0000-00-00 00:00:00'){
			$this->timeType = 1;
		}else{
			$this->timeType = 2;
		}	
	}

	/**
	* 取得运费模板列表
	*/
	public function getExpress(){
		return array(
			'1'=>'运费模板1',
			'2'=>'运费模板2',
			'3'=>'运费模板3',
			'4'=>'运费模板4',
			);
	}

	/**
	* 添加工艺产品
	*/
	public function addCraftProduct( $model ){
		if( empty($model) ) return false;

		if( !is_array($this->craft) || empty($this->craft) ){
			$this->addError( 'craft',Yii::t('product','Crafts must be selected') );
			return false;
		}


		//检查工艺是否可提交。
		$c = new CDbCriteria;
		$c->compare('craftCode',$this->craft);
		$crafts = tbCraft::model()->findAll ( $c );

		if( count($this->craft) != count($crafts) ){
			$this->addError( 'craft',Yii::t('product','Illegal submission process') );
			return false;
		}

		$codes =  array();
		foreach ( $crafts  as $val ){
			if( $val->parentCode ){
				$codes[$val->parentCode][] = $val->craftCode;
			}else{
				$codes[$val->craftCode][]  = $val->craftCode;
			}
		}

		foreach ( $codes  as $val ){
			if( count($val) >1 ){
				$this->addError( 'craft',Yii::t('product','{craft} Process can not be submitted at the same time',array('{craft}'=>implode(',',$val))) );
				return false;
			}
		}

		$product = new tbProduct();
		$product->type = tbProduct::TYPE_CRAFT;
		$product->baseProductId = $model->productId;
		$product->categoryId = $model->categoryId;
		$product->title = $model->title;
		$product->mainPic = $model->mainPic;
		$product->serialNumber = $this->serialNumber;
		$product->salesTime = date('Y-m-d H:i:s');//上架时间
		$this->_product = $product;

		if( !$this->save() ) return false;

		$tbProductCraft = new tbProductCraft();
		$tbProductCraft->productId = $this->productId;
		foreach ( $this->craft  as $val ){
			$_model = clone $tbProductCraft;
			$_model->craftCode = $val;
			if(!$_model->save()){
				$this->addErrors( $_model->getErrors() );
				return false;
			}
		}

		$db = Yii::app()->db;

		//从主产品处复制一遍基本信息，产品属性信息和采购信息
		$sql = "INSERT INTO {{product_detail}} (`productId`,`testResults`,`content`,`phoneContent`,`pictures`) SELECT ".$this->productId.",`testResults`,`content`,`phoneContent`,`pictures` FROM {{product_detail}} where `productId`='".$model->productId."'";
		$command = $db->createCommand($sql);
		$command->execute();

		//复制采购信息
		$sql = "INSERT INTO {{product_attribute}} (`productId`,`attributeId`,`title`,`attrValue`) SELECT ".$this->productId.",`attributeId`,`title`,`attrValue` FROM {{product_attribute}} where `productId`='".$model->productId."'";
		$command = $db->createCommand($sql);
		$command->execute();
		return true;
	}
}
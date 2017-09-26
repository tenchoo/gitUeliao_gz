<?php
/**
 * 发布产品,产品基本信息
 * @version 0.1
 * @package CFormModel
 */
class ProductForm extends CFormModel {

	/**
	 * @var integer
	 */
	public $categoryId,$productId;

	/**
	 * @var string
	 */
	public $title,$serialNumber,$content,$pictures,$mainPic,$attrs,$phoneContent,$testResults;

	/**
	* 操作类型 add/edit
	*/
	public $action = 'add';

	public $productType,$baseProductId;

	public $craft,$specs,$relation;

	private $_spec = array();

	/**
	* 原产品编号，添加工艺产品时使用
	*/
	public $baseSerialNumber;

	/**
	* Declares the validation rules.
	* The rules state that account and password are required,
	* and password needs to be authenticated.
	*/
	public function rules()	{
		return array(
			array('categoryId','required','on'=>'step1'),
			array('categoryId', "numerical","integerOnly"=>true,'min'=>'1','on'=>'step1'),
			array('title,categoryId,serialNumber,specs,relation,pictures,content', 'required','on'=>'step2'),
			array('title', 'length', 'max'=>30, 'min'=>3,'on'=>'step2'),
			array('title,serialNumber,content,phoneContent,pictures,mainPic,attrs,testResults,craft,specs,relation','safe','on'=>'step2'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签
	*/
	public function attributeLabels() {
		return array(
			'title' => '产品名称',
			'serialNumber' => '产品编号',
			'mainPic'=>'产品主图',
			'categoryId'=>'行业分类',
			'content'=>'产品描述',
			'pictures'=>'产品图片',
			'phoneContent'=>'手机端产品描述',
			'testResults'=>'产品测试',
			'craft'=>'特殊工艺',
			'specs' => '规格',
			'relation'=>'规格relation',
		);
	}

	/**
	* 重置这些数据，按提交的数据重新获取
	*/
	private function setData(){
		$this->attributes = Yii::app()->request->getPost('p');
		$pictures = Yii::app()->request->getPost('pictures');
		$this->attrs = Yii::app()->request->getPost('attrs');
		$this->relation = Yii::app()->request->getPost('relation');
		$specPic = Yii::app()->request->getPost('specPic');

		foreach ( $pictures as $key=>$val ){
			if( empty( $val ) ){
				unset( $pictures[$key] );
			}
		}

		if( !empty( $pictures ) ){
			$this->mainPic = reset( $pictures );
			$this->pictures = $pictures;
		}else{
			$this->pictures ='';
		}


		$spec = array();
		if( is_array( $specPic ) ){
			foreach ( $specPic as $k1=>$val ){
				if( !is_array( $val ) ) continue;
				foreach ( $val as $k2=>$pic ){
					$spec[$k2] = array('specId'=>$k1,'picture'=>$pic);
				}
			}			
		}
		

		if( empty( $spec ) ){
			$this->specs = '';
		}else{
			$this->specs = $spec;
		}
	}

	public function save() {
		$this->scenario = 'step2' ;
		$this->setData();
		if( !$this->validate() || !$this->checkCraft()) {
			return false ;
		}

		//产品表
		if( $this->action =='edit'){
			$product = tbProduct::model()->findByPk( $this->productId );
		}else{
			$product = new tbProduct();
			$product->state = 1;
			if( $this->action == 'addCraft' ){
				$product->type = $this->productType;
				$product->baseProductId = $this->baseProductId;
			}

			$product->serialNumber = $this->serialNumber;
			$product->categoryId = $this->categoryId;
		}

		$product->attributes = $this->getAttributes( array('title','mainPic') );

		//使用事务处理，以确保这组数据全部成功插入
		$transaction = Yii::app()->db->beginTransaction();
		try {
			if( !$product->save() ) {
				$this->addErrors( $product->getErrors() );
				return false;
			} else {
				$this->productId = $product->productId;
			}

			$productDetail = tbProductDetail::model()->findByPk( $this->productId );

			//产品描述
			if( !$productDetail ){
				$productDetail = new tbProductDetail();
				$productDetail->productId = $product->productId;
			}
			$productDetail->attributes = $this->getAttributes( array('productId','content','pictures','phoneContent','testResults') );
			$productDetail->pictures = json_encode( $productDetail->pictures );
			if( !$productDetail->save() ) {
				$this->addErrors( $productDetail->getErrors() );
				return false;
			}

			//产品属性
			if( !$this->saveProductAttrs( $product->productId ) ){
				return false;
			}

			//保存规格数据
			if( !$this->saveSpecAttr()){
				return false;
			}

			//保存规格库存
			if( !$this->saveSpecSorck() ){
				return false;
			}

			//保存规格库存
			if( !$this->saveCraft() ){
				return false;
			}

			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			throw new CHttpException(503,$e);
			return false;
		}
	}

	private function checkCraft(){
		if( $this->action != 'addCraft' ){
			return true;
		}

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

		return true;
	}

	private function saveCraft(){
		if( $this->action == 'addCraft' ){
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
		}
		return true;
	}

	/**
	* 保存规格数据
	*/
	private function saveSpecAttr(){
		if( empty( $this->specs ) || !is_array( $this->specs ) ) return true;

		$saveSpec = array_keys( $this->specs );
		$deleteSpec = array_diff( $this->_spec, $saveSpec );
		$addSpec = array_diff( $saveSpec,$this->_spec );

		//无变更
		if( empty($deleteSpec) && empty($addSpec) ) return true;

		$model = new tbProductSpec();
		$model->productId = $this->productId;

		if( !empty( $deleteSpec ) ){
			$c = new CDbCriteria;
			$c->compare( 'productId',$this->productId );
			$c->compare( 'specvalueId',$deleteSpec );
			$model->deleteAll( $c );
		}

		$models = tbProductSpec::model()->findAll( 'productId =:productId ',array(':productId'=>$this->productId));
		foreach( $models as $_model ){
			if( array_key_exists ( $_model->specvalueId , $this->specs ) ){
				if( $_model->picture !=  $this->specs[$_model->specvalueId]['picture'] ){
					$_model->picture = $this->specs[$_model->specvalueId]['picture'];
					if( !$_model->save() ) {
						$this->addErrors( $_model->getErrors() );
						return false;
					}
				}
			}else{
				$model->delete();
			}
		}

		foreach ( $addSpec as $val ){
			$_model = clone $model;
			$_model->specvalueId = $val;
			$_model->attributes =  $this->specs[$val];
			if( !$_model->save() ) {
				$this->addErrors( $_model->getErrors() );
				return false;
			}
		}
		return true;
	}

	/**
	* 保存规格库存
	*/
	private function saveSpecSorck(){
		if( empty( $this->relation ) || !is_array( $this->relation )  ) return true;

		$model = new tbProductStock();
		$model->productId = $this->productId;

		$specStock = array();
		foreach ( $this->relation as $val ){
			if( !empty ( $val )){
				$single = $model->singleSerialNumber( $this->serialNumber,$val );
				if( !empty( $single )){
					$specStock[$single] = $val;
				}
			}
		}

		$specs = $model->findAll( 'productId =:productId ',array(':productId'=>$this->productId) );
		foreach ( $specs as $val ){
			if( array_key_exists ( $val->singleNumber , $specStock) ){
				$state = 0;
				unset( $specStock[$val->singleNumber] );
			}else{
				$state = 1;
			}
			if( $val->state != $state ){
				$val->state = $state;
				$val->save();
			}
		}

		foreach( $specStock as $key=>$val ){
			$_model = clone $model;
			$_model->relation =  $val;
			$_model->state = 0;
			$_model->singleNumber = $key;
			if( !$_model->save() ) {
				$this->addErrors( $_model->getErrors() );
				return false;
			}
		}
		return true;
	}


	/**
	* 保存属性数据
	* @param integer $productId 产品ID
	*/
	private function saveProductAttrs( $productId ){
		$model = new tbProductAttribute();
		if( !is_array( $this->attrs ) || empty( $this->attrs ) ){
			if( $this->action =='edit'){
				//删除
				$model->deleteAll( 'productId = '.$productId );
			}
			return true;
		}

		$model->productId = $productId;

		if( $this->action =='edit'){
			$attrs = tbProductAttribute::model()->findAll ( 'productId = :productId',array(':productId'=>$productId) );


		}else{
			$attrs = array();
		}

		$i = 0;

		foreach ( $this->attrs as $key=>$val ){
			if( empty( $val ) ){
				continue;
			}

			if( array_key_exists( $i, $attrs ) ){
					$_model = $attrs[$i];
					$i++;
			}else{
				$_model = clone $model;
			}

			$_model->attrId = $key;

			if( is_array ( $val ) ) {
				$_model->attrValue = implode( ',', $val );
			} else{
				$_model->attrValue = $val ;
			}

			if( !$_model->save() ) {
				$this->addErrors( $_model->getErrors() );
				return false;
			}
		}

		$len = count( $attrs );
		if( $i < $len ){
			for( $i;$i<$len;$i++ ){
				if( array_key_exists( $i, $attrs ) ){
						$attrs[$i]->delete();
				}
			}
		}

		return true;
	}

	/**
	* 编辑时取得初始数据
	* @param integer $productId
	*/
	public function getInfo( $productId ){
		if( empty( $productId ) ){
			return ;
		}
		$this->action ='edit';
		$this->scenario = 'step2' ;

		//取得产品信息
		$product =  tbProduct::model()->findByPk( $productId );
		if( !$product ){
			return ;
		}

		$this->attributes = $product->getAttributes( array('title','serialNumber') ) ;
		$this->productId = $product->productId;
		$this->categoryId = $product->categoryId;
		$this->productType = $product->type;


		$productDetail = tbProductDetail::model()->findByPk( $this->productId );
		if( $productDetail ){
			$this->attributes = $productDetail->getAttributes( array('content','pictures','phoneContent','testResults') ) ;
			$this->pictures  = json_decode( $this->pictures,true );
		}

		//产品属性
		$attrs = tbProductAttribute::model()->findAll ( 'productId = :productId',array(':productId'=>$productId) );

		$attr = array();
		$tbAttrValue = new tbAttrValue();
		foreach ( $attrs as $val){
			$attr[$val->attrId] = $tbAttrValue->getValueById( $val->attrValue );
		}
		$this->attrs = $attr;

		//规格信息
		$this->specs = tbProductSpec::getSpec ( $productId );

		$this->_spec  = array_keys( $this->specs );

		if( $this->productType ==  tbProduct::TYPE_CRAFT ){
			$this->craft = tbProductCraft::model()->getAllCraft( $productId );
		}
	}
}
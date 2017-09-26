<?php
/*
* 发布产品
* @access 发布产品
*/
class PublishController extends Controller {

	public $currentTitle;
	/**
	* 发布产品
	* @access 发布基本信息
	*/
	public function actionIndex() {
		$step = Yii::app()->request->getQuery('step');
		$arr = array(	'1'=>'',
						'2'=>'产品基本信息',
						'craft'=>'发布工艺产品',
						'voices'=>'语音介绍',
						'addvoice'=>'新增语音介绍',
						'editvoice'=>'编辑语音介绍',
						'delvoice'=>'删除语音介绍',);
		if( !array_key_exists( $step,$arr ) ){
			$step = 1;
		}
		$this->currentTitle = $arr[$step];
		$func = 'step_'.$step;
		$this->$func();
	}

	/**
	* 发布产品第一步--选择类目
	*/
	private function step_1(){
		$publish = Yii::app()->session->get('publish');
		if( is_null( $publish ) || !($publish instanceof ProductForm) || $publish->action!='add'  ){
			$publish = new ProductForm( 'step1' );
			Yii::app()->session->add('publish', $publish );
		}

		if( Yii::app()->request->isPostRequest ) {
			$publish->scenario = 'step1' ;
			$publish->categoryId = Yii::app()->request->getPost("categoryId");
			if( $publish->validate() ) {
				Yii::app()->session->add('publish',$publish);
				if ( $publish->action == 'edit' ){
					$this->redirect( $this->createUrl('edit',array('step'=>2,'id'=>$publish->productId )) );
				}else{
					$this->redirect( $this->createUrl('index',array('step'=>2)) );
				}
			}else{
				$errors = $publish->getErrors();
				$this->dealError( $errors );
			}
		}

		if( $publish->categoryId ){
			//行业分类标题
			$categorys = tbCategory::model()->getParentNames( $publish->categoryId );
		}else{
			$categorys = array();
		}
		$this->render( 'setp1',array( 'categoryId'=>$publish->categoryId,'categorys'=>$categorys ) );
	}

	/**
	* 发布产品第二步--商品信息描述
	*/
	private function step_2(){
		$publish = Yii::app()->session->get('publish');
		if( empty( $publish->categoryId ) ){
			$this->redirect( $this->createUrl('index') );
		}

		$this->saveProduct( $publish );
	}

	/**
	* 发布工艺产品
	* @access 发布工艺产品
	*/
	private function step_craft() {
		$id = Yii::app()->request->getQuery('id');
		$product = $this->getProductModel( $id,'0' );

		$publish = new ProductForm( 'step2' );
		$publish->categoryId = $product->categoryId;
		$publish->attributes = $product->getAttributes( array('title','serialNumber') ) ;
		$publish->baseSerialNumber = $product->serialNumber;
		$publish->productType = tbProduct::TYPE_CRAFT;
		$publish->baseProductId = $product->productId;

		$publish->action = 'addCraft';
		$productDetail = tbProductDetail::model()->findByPk( $product->productId );
		if( $productDetail ){
			$publish->attributes = $productDetail->getAttributes( array('content','pictures','phoneContent','testResults') ) ;
			$publish->pictures  = json_decode( $publish->pictures,true );
		}

		//产品属性
		$attrs = tbProductAttribute::model()->findAll ( 'productId = :productId',array(':productId'=>$product->productId) );

		$attr = array();
		$tbAttrValue = new tbAttrValue();
		foreach ( $attrs as $val){
			$attr[$val->attrId] = $tbAttrValue->getValueById( $val->attrValue );
		}

		$publish->attrs = $attr;
		$this->saveProduct( $publish );
	}


	/**
	* 发布产品-语音文件
	* @access 语音文件
	*/
	private function step_voices(){
		$productId = (int)Yii::app()->request->getQuery('id');
		$productModel = $this->getProductModel( $productId );
		$list = tbProductSound::model()->findAllByProductID( $productId );
		$this->render( 'voices',array( 'list'=>$list,'serialNumber'=> $productModel->serialNumber,'productId'=> $productId )  );
	}
	/**
	* @access 保存产品
	*/
	private function saveProduct( $publish ){
		set_time_limit(0);

		$canSave = $this->checkAccess( '/product/publish/index' );
		if( $canSave && Yii::app()->request->isPostRequest ) {
			if( $publish->save() ) {
				Yii::app()->session->add('publish',null);
				$url = $this->createUrl( 'list' );
				$this->dealSuccess($url);
			} else{
				$errors = $publish->getErrors();
				$this->dealError( $errors );
			}
		}

		//行业分类标题
		$categorys = tbCategory::model()->getParentNames( $publish->categoryId );

		//取得产品属性
		$attrlist = tbAttribute::getLists2( $publish->categoryId );

		//属性组
		$attrgroups = tbSetGroup::model()->getList( 1 );

		$arr = array('attrs','craft','specs','pictures');
		foreach ( $arr as $val ){
			if( !is_array( $publish->$val ) ){
				$publish->$val = array();
			}
		}

		//取得规格数据
		$speclist = tbSpec::getSpecinfo($publish->categoryId);

		//颜色色系统
		$colorgroups = tbSetGroup::model()->getList( 2 );

		$craft = array();
		if( $publish->productType ==  tbProduct::TYPE_CRAFT ){
			$craft = tbCraft::model()->getAllCraft();
		}

		$this->render( 'setp2' ,array( 'publicinfo'=>$publish->attributes, 'categorys'=>$categorys,'attrlist'=>$attrlist,'attrgroups'=>$attrgroups,'speclist'=>$speclist,'colorgroups'=>$colorgroups,'craft'=>$craft,'canSave'=>$canSave ));
	}


	/**
	* 发布产品-销售信息
	* @access 销售信息
	*/
	public function actionSaleinfo(){
		$type = Yii::app()->request->getQuery('type');
		if( $type == 'deposit' ){
			//安全库存设置
			$this->currentTitle = '订金比例设置';
			$this->singleNumberDo( 'depositRatio','deposit' );
		}else{
			$productId = (int)Yii::app()->request->getQuery('id');
			$model = new SaleInfoForm();
			$model->getInfo( $productId );
			if( !$model->productId ){
				$this->redirect( $this->createUrl('index') );
			}

			if( Yii::app()->request->isPostRequest ) {
				$model->attributes = Yii::app()->request->getPost('product');
				if( $model->save() ){
					$url = $this->createUrl( 'saleinfo',array('id'=>$productId,'falg'=>rand(0,9999) ) );
					$this->dealSuccess($url);
				}else{
					$errors = $model->getErrors();
					$this->dealError( $errors );
				}
			}

			$this-> showSaleinfo( $model );
		}
	}



	/**
	* 产品销售信息页面
	*/
	private function showSaleinfo( $model ,$baseinfo = array()  ){

		//取得规格数据
		$speclist = tbSpec::getSpecinfo($model->categoryId);
		$units = tbUnit::model()->getUnits();
		//颜色色系统
		$setgroups = tbSetGroup::model()->getList( 2 );

		//取得运费模板列表
		$express = $model->getExpress();

		$specStock = array();
		if(	is_array(  $model->specStock ) ){
			foreach( $model->specStock as $val ){
				$specStock[$val['relation']]= $val['total'];
			}
			$model->specStock = $specStock ;
		}

		$craft = array();
		if( $model->productType ==  tbProduct::TYPE_CRAFT ){
			$craft = tbCraft::model()->getAllCraft();
			$model->craft =  is_array($model->craft)?$model->craft:array();
		}
		$this->render( 'saleinfo',array( 'data'=>$model->attributes,'units'=>$units,'speclist'=>$speclist,'setgroups'=>$setgroups,'express'=>$express ,'craft'=>$craft,'baseinfo'=>$baseinfo) );
	}


	/**
	* 发布产品-采购信息
	* @access 采购信息
	*/
	public function actionProcurement(){
		$type = Yii::app()->request->getQuery('type');
		switch( $type  ){
			case 'ajaxsupplier':	 //供应商查找
				$keyword = Yii::app()->request->getParam('keyword');
				$data = tbSupplier::model()->searchbyName( $keyword );
				$json=new AjaxData(true,null,$data);
				echo $json->toJson();
				Yii::app()->end();
				break;
			case 'safetystock':			//安全库存设置
				$this->currentTitle = '安全库存设置';
				$this->singleNumberDo( 'safetyStock','safetystock' );
				break;
			default:		//采购信息
				$this->procurement();
				break;

		}
	}

	/**
	* @access 采购信息
	*/
	private function procurement(){
		$productId = Yii::app()->request->getQuery('id');
		$productModel = $this->getProductModel( $productId );

		$model = new ProductSetForm();
		$model->productId = $productId ;
		$model->getInfo( $productId );
		if( Yii::app()->request->isPostRequest ) {
			$model->attributes = Yii::app()->request->getPost('product');
			if( $model->save() ){
				$url = $this->createUrl( 'procurement',array('id'=>$productId,'falg'=>rand(0,9999) ) );
				$this->dealSuccess($url);
			}else{
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}

		}

		$this->render( 'procurement',array( 'data'=>$model->attributes ,'serialNumber'=> $productModel->serialNumber ) );
	}



	/**
	* @access 新增语音文件
	*/
	private function step_addvoice(){
		$productId = Yii::app()->request->getQuery('id');
		$productModel = $this->getProductModel( $productId );

		$model  = new tbProductSound();
		$model->productId = $productId;
		$this->saveVoice( $model,$productModel->serialNumber );
	}


	/**
	* @access 编辑语音文件
	*/
	private function step_editvoice(){
		$id = (int)Yii::app()->request->getQuery('id');
		$model  = tbProductSound::model()->findByPk( $id ,'isDel = 0') ;
		if( !$model ){
			$this->redirect( $this->createUrl('list') );
		}

		//查找产品模型
		$productModel = $this->getProductModel( $model->productId );

		$this->saveVoice( $model,$productModel->serialNumber );
	}


	private function saveVoice( $model,$serialNumber ){
		if( Yii::app()->request->isPostRequest ) {
			$data = Yii::app()->request->getPost('data');
			$arr = array('title','sound','isMain','sort');
			foreach ( $arr as $k ){
				if( array_key_exists( $k ,$data ) ){
					$model->$k = $data[$k];
				}
			}

			if( $model->save() ){
				$url = $this->createUrl( 'index',array('step'=>'voices','id'=>$model->productId ) );
				$this->dealSuccess($url);
			}else{
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}
		}

		$data = $model->attributes;
		$data['serialNumber'] = $serialNumber;
		$this->render( 'editvoices',$data );
	}


	/**
	* @access 编辑语音文件
	*/
	private function step_delvoice(){
		$id = (int)Yii::app()->request->getQuery('id');
		$state = tbProductSound::delVoices( $id );
		if( $state ){
			$this->dealSuccess(Yii::app()->request->urlReferrer);
		}else{
			$this->dealError( array('删除失败') );
		}
	}



	/**
	* 批量操作单品信息字段
	* @param string $attribute 要操作的字段
	* @param string $tpl 对应的视图文件,注意调用此方法访问的action名要和视图文件名字一致。
	*/
	private function singleNumberDo( $attribute,$tpl ){
		$productId = Yii::app()->request->getQuery('id');
		$productModel = $this->getProductModel( $productId );

		$model = tbProductStock::model()->findAllByAttributes(  array('productId'=>$productId ,'state'=>'0') );

		if( Yii::app()->request->isPostRequest ) {
			$data = Yii::app()->request->getPost('form');
			foreach ( $model as $val ){
				if( !array_key_exists( $val->stockId,$data ) ) continue;

				$val->$attribute = $data[$val->stockId];
				if ( !$val->save() ){
					$errors = $val->getErrors();
					$this->dealError( $errors );
					goto showPage;
				}
			}

			 $action = $this->getAction()->getId() ;
			 $type = Yii::app()->request->getQuery('type');
			 if( !empty( $type ) ){
				$param['type'] = $type;
			 }
			 $param['id'] = $productId;
			 $url = $this->createUrl( $action,$param );
			$this->dealSuccess($url);
		}

		showPage:
		//规格信息
		$specAttr = tbProductSpec::getSpec ( $productId );

		$modeldata = array();
		foreach($model as $val){
			$spec = json_decode( '{'.preg_replace('/(\w+):/is', '"$1":',  $val['relation']).'}');
			$k = current( $spec  );
			if( !array_key_exists( $k ,$specAttr  ) ){
				$val->delete();
				continue;
			}

			$val = $val->attributes;
			$val['code'] = $specAttr[$k]['code'];
			$val['picture'] = $specAttr[$k]['picture'];
			foreach ( $spec as $_val ){
				if( array_key_exists( $_val ,$specAttr  ) ){
					$val['spec'][] = array( 'title'=>$specAttr[$_val]['title'],'serialNumber'=>$specAttr[$_val]['serialNumber'] );
				}
			}
			$modeldata[] = $val;
		}

		$serialNumber = $productModel->serialNumber;
		$this->render( $tpl,array( 'productId'=>$productId ,'serialNumber'=>$serialNumber,'productType'=>$productModel->type,'model'=>$modeldata ) );
	}


	/**
	* 显示除删除外所有的产品列表
	* @access 价格设置
	*/
	public function actionList() {
		$productId = Yii::app()->request->getQuery('id');
		if( is_numeric ( $productId ) && $productId > 0 ){
			$publish = new ProductForm( 'step2' );
			$publish->getInfo( $productId );
			if( !$publish->productId ){
				$this->redirect( $this->createUrl('list') );
			}
			$this->currentTitle = '产品基本信息';
			$this->saveProduct( $publish );
		}else{
			$this->showlist();
		}
	}

	private function showlist( ){
		$serialNumber = trim(Yii::app()->request->getQuery('serialNumber'));
		$categoryId = trim(Yii::app()->request->getQuery('category'));
		$pageSize = (int) tbConfig::model()->get( 'page_size' );
		$product = Product::getList( array(0,1), array('serialNumber'=>$serialNumber,'categoryId'=>$categoryId),$pageSize);
		$this->render('//product/default/index',array( 'list'=>$product['data'],'pages'=>$product['pages'],'serialNumber'=> $serialNumber,'categoryId'=> $categoryId,'op'=> 'setprice') );
	}


	/**
	* 发布产品-呆滞信息
	* @access 呆滞信息
	*/
	public function actionGlass(){
		$type = Yii::app()->request->getQuery('type');
		if( $type == 'glasstime' ){
			//安全库存设置
			$this->currentTitle = '呆滞时长';
			$this->singleNumberDo( 'glassTime','glasstime' );
		}else{
			$productId = Yii::app()->request->getQuery('id');
			$productModel = $this->getProductModel( $productId );

			//找到整体呆滞级别配置
			$models = tbGlassyLevel::model()->findAll( array(
						'order'=>'conditions asc',
					));
			$list = array();
			foreach ( $models as $val ){
				$list[$val->id] = $val->attributes;
			}

			$pglModel = new tbProductGlassyLevel();

			//查找到个性化设置时长
			$pmodels = $pglModel->findAll( 'productId = :productId',array( ':productId'=>$productId ) );
			foreach ( $pmodels as $key=>$_val ){
				if( array_key_exists($_val->glassLevelId,$list ) ){
					$list[$_val->glassLevelId]['conditions'] = $_val->conditions;
				}else{
					$_val->delete();
					unset( $pmodels[$key] );
				}
			}

			if( Yii::app()->request->isPostRequest ) {
				$data = Yii::app()->request->getPost('form');

				if( !is_array( $data ) ) goto showPage;

				foreach ( $data as $key=>$val ){
					if( array_key_exists( $key,$list ) ){
						$list[$key]['conditions'] = $val;
					}else{
						unset( $data[$key] );
					}
				}

				if( empty( $list ) ) goto showPage;

				foreach ( $list as $val ){
					$_model = $pglModel->find( 'productId = :productId and glassLevelId = :glassLevelId ',
												array( ':productId'=>$productId,':glassLevelId'=>$val['id'], ) );
					if( $_model ){
						if( $_model->conditions == $val['conditions'] ) continue;
					}else{
						$_model = clone $pglModel;
						$_model->productId = $productId;
						$_model->glassLevelId = $val['id'];
					}

					$_model->conditions = $val['conditions'];
					if( !$_model->save() ){
						$errors = $_model->getErrors();
						$this->dealError( $errors );
						goto showPage;
					}
				}

				$url = $this->createUrl( 'glass',array( 'id'=>$productId ) );
				$this->dealSuccess($url);
			}

			showPage:
			$this->render( 'glass',array( 'list'=>$list,'serialNumber'=> $productModel->serialNumber,'productId'=> $productId )  );
		}
	}


	/**
	* 发布产品-调整单比例
	* @access 调整单比例
	*/
	public function actionAdjustratio(){
		$this->singleNumberDo( 'adjustRatio','adjust' );
	}

	/**
	* 发布产品-默认分拣区域
	* @access 默认分拣区域
	*/
	public function actionPackarea(){
		$productId = Yii::app()->request->getQuery('id');
		$productModel = $this->getProductModel( $productId );

		//取得所有普通仓库
		$warehouses = tbWarehouseInfo::model()->getAll( '1' );
		$model = new ProductWarehouseForm();
		if( Yii::app()->request->isPostRequest ) {
			$data = Yii::app()->request->getPost('data');

			if( is_array( $data ) ){
				foreach ( $data as $key=>$val ){
					if( !array_key_exists( $key,$warehouses ) ){
						goto showPage;
					}
				}
			}

			$model->productId = $productModel->productId;

			if ( $model->save( $data ) ){
				$url = $this->createUrl( 'packarea',array( 'id'=>$productId ) );
				$this->dealSuccess($url);
			}else{
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}
		}

		showPage:
		$list = $model->getAll( $productId );
		$position = new tbWarehousePosition();
		foreach ( $list as $key=>&$val ){
			if( !array_key_exists( $val['warehouseId'],$warehouses )){
				unset( $list[$key] );
			}
			$val['wTitle'] = $warehouses[$val['warehouseId']];
			$val['pTitle'] = $position->positionName( $val['positionId'],$wId );
		}

		$this->render( 'packarea',array( 'list'=>$list,'serialNumber'=> $productModel->serialNumber,'productId'=> $productId,'warehouses'=>$warehouses )  );
	}

	/**
	* 查找产品模型
	* @param integer $productId 产品ID
	* @param integer $type		产品类型
	*/
	public function getProductModel( $productId,$type = null ){
		if( is_numeric( $productId ) && $productId >=1 ){

			$conditions = '';
			if( in_array( $type,array('0','1') ) ){
				$conditions .= 'type = '.$type;
			}

			$productModel = tbProduct::model()->findByPk( $productId,$conditions);
			if( $productModel ){
				return $productModel;
			}
		}

		$this->redirect( $this->createUrl('list') );
	}


}
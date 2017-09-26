<?php
/**
 * 产品推荐
 * @access 产品推荐
 * @author liang
 * @package Controller
 * @version 0.1
 *
 */
class RecommendController extends Controller {

	public $currentTitle;

	/**
	 * 产品推荐
	 * @access 产品推荐
	 */
	public function actionIndex() {
		if( Yii::app()->request->getIsAjaxRequest() ) {
			$id = Yii::app()->request->getQuery('recommendId');
			$list = tbRecommend::getByParentId( $id );
			$data = new AjaxData( true, null, $list );
			echo $data->toJson();
			Yii::app()->end(200);
		}
		$list = tbRecommend::getByParentId( 0 );
		$this->render( 'index',array( 'list'=>$list ) );
	}


	/**
	 * 推荐列表
	 * @access 推荐列表
	 */
	public function actionList() {
		$model = $this->getModel();
		$this->currentTitle = $model->page->title.'->'.$model->title;
		$data['list'] = $model->getProducts();
		$data['recommendId'] = $model->recommendId;
		$this->render( 'list',$data );
	}


	/**
	 * 添加推荐产品页面
	 * @access 添加推荐产品页面
	 */
	public function actionProductlist() {
		Yii::import('product.models.Product');

		$model = $this->getModel();
		$this->currentTitle = $model->page->title.'->'.$model->title;
		$recommendProductIds = $model->getProductIds();
		$data['recommendId'] = $model->recommendId;
		$serialNumber = trim(Yii::app()->request->getQuery('serialNumber'));
		$categoryId = trim(Yii::app()->request->getQuery('category'));
		$pageSize = (int) tbConfig::model()->get( 'page_size' );
		$product = Product::getList( 0 , array('serialNumber'=>$serialNumber,'categoryId'=>$categoryId),$pageSize);
		$this->render('productlist',array( 'list'=>$product['data'],'pages'=>$product['pages'],'serialNumber'=> $serialNumber,'categoryId'=> $categoryId,'recommendId'=> $model->recommendId ,'recommendProductIds' =>$recommendProductIds) );
	}


	/**
	 * 编辑推荐位
	 * @access 编辑推荐位
	 * @throws CHttpException
	 */
	public function actionEditposition() {
		$model = $this->getModel();
		$model->scenario = 'editposition';
		if( Yii::app()->request->isPostRequest ) {
			$model->attributes = Yii::app()->request->getPost('data');
			if( $model->save() ) {
				$this->dealSuccess( $this->createUrl('index') );

			}else{
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}
		}
		$this->render( 'editposition' ,$model->attributes );
	}

	/**
	* 查找推荐位
	*
	*/
	private function getModel(){
		$id = Yii::app()->request->getQuery('recommendId');

		if( is_numeric($id) && $id>1 ) {
			$model = tbRecommend::model()->with('page')->findByPk( $id ,'t.state=0 and t.type = 1');
			if( $model ) return $model;
		}

		if( Yii::app()->request->getIsAjaxRequest() ) {
			$data = new AjaxData( false, 'Not found record' );
			echo $data->toJson();
			Yii::app()->end();
		}else{
			throw new CHttpException(404,'Not found record.');
		}
	}


	/**
	 * 推荐位管理--编辑页面或推荐位,树形编辑
	 * @access 编辑推荐位/页面
	 * @throws CHttpException
	 */
	public function actionEdit() {
		$form = Yii::app()->request->getPost('form');
		$model = tbRecommend::model()->findByPk( $form['recommendId'],'state=0' );
		if( !$model ) {
			$error = new AjaxData(false,'Not found record');
			echo $error->toJson();
			Yii::app()->end(200);
		}
		$this->saveData( $model );
	}

	/**
	 * 推荐管理--新增
	 * @access 新增推荐位
	 * @throws CHttpException
	 */
	public function actionAdd() {
		$model = new tbRecommend();
		$this->saveData( $model );
	}

	/**
	 * 推荐位管理--删除,
	 * @access 删除推荐位
	 */
	public function actionDel() {
		$recommendId  =  Yii::app()->request->getPost('recommendId');
		$message = Yii::t('category','Delete failed');;
		$state = tbRecommend::model()->del ( $recommendId,$message );
		$json = new AjaxData($state,$message);
		echo $json->toJson();
		Yii::app()->end();
	}

	/**
	 * 根据推荐位置ID和产品ID进行取消。
	 * @access 取消推荐产品
	 */
	public function actionDelproduct() {
		$state = false;
		$productId  =  Yii::app()->request->getQuery('id');
		$recommendId = Yii::app()->request->getQuery('recommendId');
	//	$productId = explode(',',$productId);
		if( !empty($recommendId) && !empty($productId )){
			$rows = tbRecommendProduct::model()->deleteAllByAttributes( array('recommendId'=>$recommendId,'productId'=>$productId  ) );
			if($rows) $state = true;
		}

		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}


	/**
	 * 根据推荐产品表的PK进行删除。
	 * @access 删除推荐产品
	 */
	public function actionDelrecommend() {
		$id  =  Yii::app()->request->getQuery('id');
		$id = explode(',',$id);

		$state = false;
		if( !empty($id) ){
			$rows = tbRecommendProduct::model()->deleteByPk( $id );
			if($rows) $state = true;
		}

		$json = new AjaxData($state,null);
		echo $json->toJson();
		Yii::app()->end();
	}


	/**
	 * @access 新增推荐产品
	 */
	public function actionAddproduct() {
		$recommendId = Yii::app()->request->getQuery('recommendId');
		$productId = Yii::app()->request->getQuery('id');

		$position = null;
		if( is_numeric($recommendId) && $recommendId>1 ) {
			$position = tbRecommend::model()->findByPk( $recommendId ,'t.state=0 and t.type = 1');
		}

		if( !$position ) {
			$data = new AjaxData( false, 'Not found record' );
			echo $data->toJson();
			Yii::app()->end();
		}

		$model = new tbRecommendProduct();
		$model->recommendId = $position->recommendId;
		$model->productId = $productId;

		if( $model->save() ){
			$state = true;
			$message = 'success';
		}else{
			$state = false;
			$data = $model->getErrors();
			$message = current( current( $data ) ) ;
		}

		$json = new AjaxData($state,$message,null);
		echo $json->toJson();
	}


	/**
	* 保存数据
	*/
	private function saveData( $model ){
		if( Yii::app()->request->isPostRequest ) {
			$form  =  Yii::app()->request->getPost('form');
			unset( $form['recommendId'] );
			$model->attributes = $form;
			if( $model->save() ) {
				$state = true;
				$data = $model->getAttributes(array('recommendId','title','parentId','type','maxNum','num','mark'));
			}else{
				$state = false;
				$data = $model->getErrors();
			}
			$json = new AjaxData($state,null,$data);
			echo $json->toJson();
		}
		Yii::app()->end(200);
	}
}
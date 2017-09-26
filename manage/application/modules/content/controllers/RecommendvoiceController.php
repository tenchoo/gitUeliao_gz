<?php
/**
 * 语音产品推荐
 * @access 语音产品推荐
 * @author liang
 * @package Controller
 * @version 0.1
 *
 */
class RecommendvoiceController extends Controller {

	public $currentTitle;
	
	public $currentLink;

	/**
	 * 语音产品推荐
	 * @access 语音产品推荐
	 */
	public function actionIndex() {
		$state = Yii::app()->request->getQuery('state');
		$state = ( $state !='1' )?  0:1;

		$identity = Yii::app()->request->getQuery('identity');

		$criteria = new CDbCriteria;
		$criteria->compare('state', $state);
		if( !empty( $identity ) ){
			$criteria->compare('identity', $identity);
		}

		$criteria->order = "updateTime DESC";

		$pageSize = (int) tbConfig::model()->get( 'page_size' );
		$model = new CActiveDataProvider('tbRecommendVoice', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));

		$data = $model->getData();
		$list = array_map( function( $i ){
							$info = $i->attributes;
							$info['num'] = $i->getNum();
							return $info;}, $data );
		$pages = $model->getPagination();

		$this->render( 'index',array( 'list'=>$list,'pages'=>$pages,'state'=>$state ,'identity'=>$identity) );
	}


	/**
	 * 推荐列表
	 * @access 推荐列表
	 */
	public function actionList() {
		$model = $this->getModel();
		$this->currentTitle = $model->title;
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
		$this->currentTitle =  $model->title;
		$this->currentLink = $this->createUrl('list',array('recommendId'=>$model->recommendId));
		$recommendProductIds = $model->getProductIds();
		$data['recommendId'] = $model->recommendId;
		$serialNumber = trim(Yii::app()->request->getQuery('serialNumber'));
		$categoryId = trim(Yii::app()->request->getQuery('category'));
		$pageSize = (int) tbConfig::model()->get( 'page_size' );
		$product = Product::getList( 0 , array('serialNumber'=>$serialNumber,'categoryId'=>$categoryId,'hasVoice'=>true ),$pageSize);
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
		$id  =  Yii::app()->request->getQuery('recommendId');
		$model = tbRecommendVoice::model()->findByPk( $id ,'state=0' );
		if( !$model ) {
			$this->redirect( $this->createUrl('index') );
		}

		return $model;
	}


	/**
	 * 推荐位管理--编辑页面或推荐位,树形编辑
	 * @access 编辑推荐位/页面
	 * @throws CHttpException
	 */
	public function actionEdit() {
		$this->saveData( $this->getModel() );
	}

	/**
	 * 推荐管理--新增
	 * @access 新增推荐位
	 * @throws CHttpException
	 */
	public function actionAdd() {
		$model = new tbRecommendVoice();
		$this->saveData( $model );
	}

	/**
	 * 推荐位管理--关闭,
	 * @access 关闭推荐位
	 */
	public function actionClose() {
		$id  =  Yii::app()->request->getQuery('id');
		$state = tbRecommendVoice::model()->updateByPk ( $id ,array('state'=>'1') );
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}

	/**
	 * 推荐位管理--激活,
	 * @access 激活推荐位
	 */
	public function actionActive() {
		$id  =  Yii::app()->request->getQuery('id');
		$state = tbRecommendVoice::model()->updateByPk ( $id ,array('state'=>'0') );
		$this->dealSuccess( Yii::app()->request->urlReferrer );
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
			$rows = tbRecommendVoiceProduct::model()->deleteAllByAttributes( array('recommendId'=>$recommendId,'productId'=>$productId  ) );
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
			$rows = tbRecommendVoiceProduct::model()->deleteByPk( $id );
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
		if( is_numeric($recommendId) && $recommendId >0 ) {
			$position = tbRecommendVoice::model()->findByPk( $recommendId ,'t.state=0');
		}

		if( !$position ) {
			$data = new AjaxData( false, 'Not found record' );
			echo $data->toJson();
			Yii::app()->end();
		}

		$model = new tbRecommendVoiceProduct();
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
			$data = Yii::app()->request->getPost('data');
			if( $model->scenario != 'insert' ){
				unset( $data['identity'] );
			}
			
			$model->attributes = $data;
			if( $model->save() ) {
				$this->dealSuccess( $this->createUrl('index') );
			}else{
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}
		}
		$data = $model->attributes;
		$data['scenario'] = $model->scenario;
		$this->render( 'add' ,$data );
	}
}
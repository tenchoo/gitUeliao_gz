<?php
/*
* ajax
* @access ajax
*/
class ProductController extends Controller {

	public function init() {
		parent::init();
		$this->attachBehavior('error', 'libs.commons.behaviors.Commons');
	}

	public $layout = '/layouts/home';

	/**
	 * 开启访问控制器
	 * @see CController::filters()
	 */
	public function filters() {
		return array();
	}

	/**
	 * 设置访问权限
	 * @see CController::accessRules()
	 */
	public function accessRules() {
		return array();
	}

	public function actionIndex(){
		echo 'ProductController/index';

	}

	public function actionDetail(){
		header("Content-type: text/html; charset=utf-8");
		$productId = Yii::app()->request->getQuery('id');
		if( (int)$productId != $productId ){
			goto not_found_page;
		}
		$this->viewLog( $productId );

		$model   =  new Product();
		$product =	$model->getDetail( $productId );
		if( !$product ){
			goto not_found_page;
		}
		$model->addViews( $productId );//浏览记录

		//属性组
		$setgroups = tbSetGroup::model()->getList( 1 );

		//颜色色系
		$colorgroups = $model->colorGroups( $product['specStock'] );

		$this->render('detail',array( 'product'=>$product,'setgroups'=>$setgroups,'colorgroups'=>$colorgroups));
		Yii::app()->end(200);

		not_found_page:
			throw new CHttpException(404,"the require product has not exists.");
	}

	/**
	* 成交记录--暂时屏蔽@2016-06-17
	*/
	public function actionDeals(){
		$perpage = 10;
		$productId = Yii::app()->request->getQuery('id');
		$unitName = Yii::app()->request->getQuery('unitName','件');
		if( is_numeric( $productId ) && $productId >0 ){
			/* $model = new tbOrderProduct();
			$total = $model->dealCount( $productId );
			$pages = new CPagination( $total );
			$pages->pageSize = $perpage;

			$currentpage = Yii::app()->request->getQuery( 'page', 1 );
			$list = $model->dealList( $productId,$currentpage,$perpage); */
			$pages = new CPagination();
			$list = array();
			$this->renderPartial('_deals',array('list'=>$list,'pages'=>$pages,'unitName'=>$unitName), false, true );
		}
	}

	/**
	* 反馈记录
	*/
	public function actionComment(){
		$perpage = 10;
		$productId = Yii::app()->request->getQuery('id');
		if( is_numeric( $productId ) && $productId >0 ){
			$model = new tbComment();
			$total = $model->commentCount( $productId );
			$pages = new CPagination( $total );
			$pages->pageSize = $perpage;

			$currentpage = Yii::app()->request->getQuery( 'page', 1 );
			$list = $model->productComment( $productId,$currentpage,$perpage);
			$this->renderPartial('_comment',array('list'=>$list,'pages'=>$pages), false, true );
		}
	}

	/**
	* 二维码图片
	*/
	public function actionPrcode(){
		Yii::import('libs.vendors.phpqrcode.qrlib',true);

		$level = Yii::app()->request->getQuery('level','L');
		if ( !in_array($level, array('L','M','Q','H'))){
			$level = 'L';
		}

		$size = Yii::app()->request->getQuery('size','5');
 		$size = min(max((int)$size, 1), 10);

		$data = Yii::app()->request->getQuery('data');
		if (trim($data) == '')  {
			$data = 'empty data';
		}else{
			$data = urldecode( $data );
		}

		QRcode::png($data, false, $level, $size, 2);
	}
}
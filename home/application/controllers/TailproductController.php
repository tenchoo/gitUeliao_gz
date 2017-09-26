<?php

class TailproductController extends Controller {

	public $layout = '/layouts/home';

	public function init() {
		parent::init();
		$this->attachBehavior('error', 'libs.commons.behaviors.Commons');
	}

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

	public function actionDetail( $id ){
		if( (int)$id != $id ){
			goto not_found_page;
		}

		$model   =  new Product();
		$product =	$model->getTailDetail( $id );
		if( !$product ){
			goto not_found_page;
		}

		$this->viewLog( $product['tailId'] );
		if( $product['saleType'] == 'whole' ){
			$product['totalNum'] = array_sum ( array_map( function ($i){ return $i['total'];},$product['specStock'] ) );
			$product['totalPrice'] = bcmul( $product['price'], $product['totalNum'],2 );
		}

		//属性组
		$setgroups = tbSetGroup::model()->getList( 1 );
		$colorgroups = $model->colorGroups( $product['specStock'] );
		$this->render('detail',array( 'product'=>$product,'setgroups'=>$setgroups,'colorgroups'=>$colorgroups));
		Yii::app()->end(200);

		not_found_page:
			throw new CHttpException(404,"the require product has not exists.");
	}

	/**
	* 成交记录
	*/
	public function actionDeals(){
		$perpage = 10;
		$tailId = Yii::app()->request->getQuery('id');
		$unitName = Yii::app()->request->getQuery('unitName','件');
		if( is_numeric( $tailId ) && $tailId >0 ){
			$model = new tbOrderProduct();
			$total = $model->dealCount( '',$tailId );
			$pages = new CPagination( $total );
			$pages->pageSize = $perpage;

			$currentpage = Yii::app()->request->getQuery( 'page', 1 );
			$list = $model->taildealList( $tailId,$currentpage,$perpage);
			$this->renderPartial('//product/_deals',array('list'=>$list,'pages'=>$pages,'unitName'=>$unitName), false, true );
		}
	}

	/**
	* 反馈记录
	*/
	public function actionComment(){
		$perpage = 10;
		$tailId = Yii::app()->request->getQuery('id');
		if( is_numeric( $tailId ) && $tailId >0 ){
			$model = new tbComment();
			$total = $model->commentCount( '',$tailId );
			$pages = new CPagination( $total );
			$pages->pageSize = $perpage;

			$currentpage = Yii::app()->request->getQuery( 'page', 1 );
			$list = $model->commentList( array( 'tailId'=>$tailId ),$currentpage,$perpage);
			$this->renderPartial('//product/_comment',array('list'=>$list,'pages'=>$pages), false, true );
		}
	}
}
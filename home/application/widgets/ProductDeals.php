<?php
/**
 * 显示客服列表
 * @author liang
 * @package CWidget
 * @version 0.1.1
 */
class ProductDeals extends CWidget {
	public $productId;
	
	public function run(){
		ob_start();
		$perpage = 10;
		$productId = Yii::app()->request->getQuery('id');
		if( is_numeric( $productId ) && $productId >0 ){
			$model = new tbOrderProduct();
			$currentpage = Yii::app()->request->getQuery( 'page', 1 );
			$total = $model->count( 'productId = '.$productId );
			$pages = new CPagination( $total );
			$pages->pageSize = $perpage;

			$list = $model->dealList( $productId,$currentpage,$perpage);
			$this->render('_deals',array('list'=>$list,'pages'=>$pages), false, true );
		}
		ob_end_flush();		
	}
	
}
<?php
/**
 * ajax 接口
 * @author liang
 * @version 0.1
 * @package Controller
 */
class AjaxController extends Controller {

	public function init() {
		if(Yii::app()->errorHandler->error) {
			return true;
		}
		parent::init();
	}

	/**
	* 首页内容，按设计稿，第1为广告，第2,3为产品推荐内容。
	*/
	public function actionIndex(){
		$data = array( 'ad'=>array(),'recommend_a'=>array(),'recommend_b'=>array());
		
		//$admark = 'app_a,app_b,app_c,app_d,app_e,app_f';
		$admark = 'app_a';
		$ads = Spread::getAds( $admark,'mark' );
		if( isset( $ads['0']['ads'] ) ){
			$data['ad'] = $ads['0']['ads'];
		}
		
		$Recommends =  array('app_index_a'=>array('size'=>'600','datakey'=>'recommend_a'),'app_index_b'=>array('size'=>'200','datakey'=>'recommend_b'));
		$marks = array_keys($Recommends);
		$model = tbRecommend::model()->findAllByAttributes( array('mark'=>$marks ,'state'=>'0','type'=>'1') );

		foreach ( $model as $val ){
			$list =  $val->getProducts();
			foreach ( $list as &$pval ){
				$pval['title'] = '【'.$pval['serialNumber'].'】'.$pval['title'];
				$pval['mainPic']  = $this->getImageUrl( $pval['mainPic'], $Recommends[$val->mark]['size'] );
				unset( $pval['serialNumber'],$pval['recommendTime'],$pval['id'] );
			}
			$data[$Recommends[$val->mark]['datakey']] = $list;
		}
		

		$this->data = $data;
		if( $this->data ){
			$this->state = true;
		}else{
			$this->message = 'Not found record';
		}
		$this->showJson();

	}
	
	/**
	 * 取得广告信息，传广告标识 mark 进行读取。批量读取
	 */
	public function actionSpread(){
		$arr = Yii::app()->request->getQuery('id'); //广告位ID
		$type = 'id';
		if( empty( $arr ) ){
			$arr = Yii::app()->request->getQuery('mark');//广告位标识
			$type = 'mark';
		}

		$this->data = Spread::getAds( $arr,$type );
		if( $this->data ){
			$this->state = true;
		}else{
			$this->message = 'Not found record';
		}
		$this->showJson();
	}


	/**
	 * 错误显示页面 */
	public function actionError() {
		$error = Yii::app()->errorHandler->error;
		if( $error ) {
			$this->message = $error['message'];
			$this->showJson();
			/* echo "<pre>";
			print_r( $error['trace'] ); */
		}
	}
}
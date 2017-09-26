<?php
/**
* 我的收藏
* @access 我的收藏
*/
class FavController extends Controller {

	/**
	* 我的收藏
	* @access 我的收藏
	*/
	public function actionIndex(){
		$data = call_user_func(array('tbProductCollection','getList'),Yii::app()->user->id,8 );
		$this->render('index',$data );
	}
}
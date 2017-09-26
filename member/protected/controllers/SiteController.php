<?php
/**
 * 会员中心入口页面
 * @author yagas
 * @package member
 */
class SiteController extends Controller {
	public $routeFlag;

	/**
	 * 默认首页
	 */
	public function actionIndex() {
		$this->routeFlag = "member::/site/index";
		$memberId = Yii::app()->user->id;
		$model = new EditForm();
		$model->getInfo( $memberId );

		$this->render('index',array('info'=>$model->attributes));
	}
}
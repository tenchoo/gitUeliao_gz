<?php
/**
 * 管理中心控制器基类
 * @author yagas
 * @package CController
 * @subpackage Controller
 */
class AdminController extends CController {

	/**
	 * @var string 默认的项目布局。
	 */
	public $layout='//layouts/main';

	/**
	 * 初始化控制器
	 * @see CController::init()
	 */
	public function init() {
		parent::init();
		$this->setPageTitle('管理中心');
	}

	/**
	 * 使用控制器权限控制
	 * @see CController::filters()
	 */
	public function filters() {
		return array('accessControl');
	}

	/**
	 * 控制器权限规则
	 * @see CController::accessRules()
	 */
	public function accessRules() {
		return array(
				//阻止未登陆的用户访问
				array('deny', 'users'=>array('?')),
		);
	}

	/**
	 * 触发器:执行前端资源提取优化
	 * @see CController::afterRender()
	 */
	public function afterRender($view, &$output) {
		$op = new sourceOptimize( Yii::app()->params['domain_manage'] );
		$op->context = $output;
// 		$op->dict = 'application.data.resmap';
		$op->run();
		$output = $op->context;
	}


	public function behaviors() {
		 return array_merge(parent::behaviors(),array(
				'oplog'=>'libs.commons.behaviors.OpLogBehavior',
        ));
	}
}
<?php
class NoticeController extends CController {
	
	public $breadcrumb;	//自定义面包屑

	/**
	 * 菜单组编号
	 * @var interge
	 */
	public $index = 1;
	
	//统一显示错误提示信息的页面
	public function actionError() {
	if( $error=Yii::app()->errorHandler->error ) {
			if( Yii::app()->request->isAjaxRequest ) {
				echo $error['message'];
			}
			else {
				$this->render("error", $error);
			}
		}
	}
	
	//无权执行动作时显示的提示信息页面
	public function actionPermission() {
		$this->render( "permission" );
	}

	/**
	 * 获取当前页面路由ID
	 * @return null|string
	 */
	public function getRouteId() {
		return null;
	}
}
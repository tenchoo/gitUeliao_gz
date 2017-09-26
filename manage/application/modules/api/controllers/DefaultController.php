<?php
class DefaultController extends CController {
	
	private $_callback;
	
	public function init() {
		parent::init();
		Yii::import( 'api.interface.*' );
		$this->_callback = Yii::app()->request->getQuery('callback');
	}
	
	public function beforeAction($action) {
		return true;
	}
	
	/**
	 * 访问权限检查
	 * @param string $route
	 */
	public function checkAccess( $route ) {
		return true;
		$userRoles = Yii::app()->user->getState( "roles" );
		$access    = Yii::app()->user->checkAccess( $this->getRoute(), array('roles'=> $userRoles) );

		if( !$access ) {
			$msg = new AjaxData( false, Yii::t('system','not has permission') );
			echo $msg->toJson();
			Yii::app()->end(200);
		}
		return true;
	}
	
	/**
	 * 分事件控制器
	 * @return bool|string
	 */
	private function parseActionName() {
		$action = Yii::app()->request->getQuery('do');
		if( !empty($action) ) {
			$fieds = explode('_', $action);
			$fieds = array_map(function($i){
				$i = strtolower($i);
				return ucfirst($i);
			}, $fieds);
			$actionName = implode('', $fieds);
			$basePath   = Yii::getPathOfAlias('api.interface');
			if( file_exists($basePath.DS.$actionName.'.php') ) {
				return $actionName;
			}
		}
		return false;
	}
	
	public function actionIndex() {
		$action = $this->parseActionName();
		if( !is_bool($action) ) {
			$do = new $action( $this, $this->action->id );
			$result = $do->run();
			echo $result->toJson();
		}
	}

	public function getRouteId() {
		return 0;
	}
}

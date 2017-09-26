<?php
/**
 * 执行用户登陆与注销操作
 * @author yagasx
 * @version 0.2
 * @package CController
 */
class SignController extends CController {

	public $layout=false;

	public function behaviors() {
		 return array_merge(parent::behaviors(),array(
				'oplog'=>'libs.commons.behaviors.OpLogBehavior',
        ));
	}

	protected function beforeAction( $action ){
		$this->writeOpLog();
		 return parent::beforeAction( $action );;
	}

	//处理用户登陆
	public function actionLogin() {
		$params = array();
		if( Yii::app()->request->getIsPostRequest() ) {
			$username = Yii::app()->request->getPost("username");
			$password = Yii::app()->request->getPost("password");
			$auth     = new UserIdentity( $username, $password );
			if( $auth->authenticate() ) {
				Yii::app()->user->login( $auth );

// 				$this->redirect( Yii::app()->user->getReturnUrl() );
				$this->redirect( $this->createUrl('/default/index') );
			}
			else {
				if($auth->errorCode == $auth::ERROR_DUPLICATE_LOGIN) {
					$params["message"] = Yii::t("base", "duplicate login");
				}else if($auth->errorCode == $auth::ERROR_DISABLE_LOGIN) {
					$params["message"] = Yii::t("base", "Your account is frozen, please contact admin manager.");
				} else {
					$params["message"] = Yii::t("base", "Invalid username or password");
				}
			}
		}
		$this->render( 'login', $params );
	}

	//用户注销登陆
	public function actionLogout() {
		@session_start();
		session_unset();
		session_destroy();
		Yii::app()->user->logout();
		$this->redirect( Yii::app()->homeUrl );
	}
}
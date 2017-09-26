<?php

class UserController extends Controller
{
	/**
	 * 模板布局文件
	 * @var string
	 */
	public $layout='libs.commons.views.layouts.main';

	/**
	 * 定义页面访问规则
	 * @see CController::accessRules()
	 */
	public function accessRules() {
		return array();
	}

	public function filters() {
		return array();
	}



	/**
	 * 登陆入口
	 */
	public function actionLogin() {
		if( !Yii::app()->user->getIsGuest() ) {
			$this->redirect('/'); //已经登录跳转到首页
			return false;
		}
		if (!defined('CRYPT_BLOWFISH')||!CRYPT_BLOWFISH)
			throw new CHttpException(500,"This application requires that PHP was compiled with Blowfish support for crypt().");

		// collect user input data
		$LoginForm=Yii::app()->request->getPost('LoginForm');
		if( $LoginForm ) {
			$model=new LoginForm;
			$model->attributes=$LoginForm;
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login()){
				$this->dealSuccess( $this->getRefererUrl() );
			}else{
				$errors = $model->getErrors();
				$this->dealError($errors);
			}
		}
		// display the login form
		$this->render('login');

	}

	/**
	 * 获取返回页面地址
	 * 用于登陆成功后回到用户之前访问的页面
	 */
	private function getRefererUrl() {
		$referfer = Yii::app()->request->getParam('done','/');
		if( is_null( $referfer ) ) {
			$referfer = Yii::app()->request->getUrlReferrer();
			if( strpos($referfer, 'password') ){
				$referfer = $this->createUrl("site/index");
			}
		}

		if( !$this->isSafeLink( $referfer ) ) {
			$referfer = $this->createUrl("site/index");
		}

		return $referfer;
	}

	/**
	 * 检查URL地址是否合法
	 * @param string $url
	 * @return string
	 */
	private function isSafeLink( $url ) {
		if( preg_match("/https?:\/\/([^\/]*)?/", $url, $match) ) {
			$domain   = array_pop( $match );
			$domain   = strstr( $domain, '.' );
			$thisSite = strstr( Yii::app()->request->hostInfo, '.' );
			return strcasecmp($domain,$thisSite) === 0;
		}
		return true;
	}

	/**
	 * 注销登陆
	 */
	public function actionLogout() {
		Yii::app()->user->logout();

		if(session_status() === PHP_SESSION_ACTIVE) {
			@session_unset();
			@session_destroy();
		}

		$cookies = Yii::app()->request->cookies->remove("PHPSESSID");
		$url=Yii::app()->homeUrl;
		$this->dealSuccess($url);
	}

	/**
	 * 弹窗登录
	 */
	public function actionPopuplogin() {
		$done = Yii::app()->request->getQuery('done');
		$this->renderPartial('popuplogin', array('done'=>$done));
	}

	/**
	* Displays the reg page
	*/
	public function actionReg()	{
		if( !Yii::app()->user->getIsGuest() ) {
			$this->redirect('/'); //已经登录跳转到首页
			return false;
		}

		$model=new RegForm();

		// collect user input data
		$RegForm=Yii::app()->request->getPost('RegForm');
		if( $RegForm ) {
			$model=new RegForm();
			$model->attributes = $RegForm;
			if( $model->register()){
				$url=$this->createUrl('regsuccess');
				$this->dealSuccess($url);
			}
			else {
				$errors = $model->getErrors();
				$this->dealError($errors);
			}
		}

		// display the reg form
		$this->render('register');
	}

	/*
	* 注册成功页面
	*/
	public function actionRegsuccess(){
		$this->render('register_2');
		Yii::app()->end();
	}


	/*
	* 重置密码成功页面
	*/
	public function actionResetPasswordSuccess(){
		$this->render('forgetpassword_3');
	}

	/*
	* 重置密码页面
	*/
	public function actionResetpassword() {
		$deadline = Yii::app()->user->getState('forgetDeadline');

		if( $deadline < time() ){  //需做ajax处理
			$url=$this->createUrl('forgetpassword');
			if( Yii::app()->request->getIsAjaxRequest() ) {
				$message = Yii::t('user','Page has expired');
				$json=new AjaxData(false,$message,$url );
				echo $json->toJson();
				Yii::app()->end(200);
			} else {
				$this->redirect($url);
			}
		}
		$passwordForm=Yii::app()->request->getPost('passwordForm');
		if( $passwordForm ) {
			$model=new SetPasswordForm('reset');
			$model->attributes=$passwordForm;
			if( $model->validate() ){
				$model->restetPassword();
				$url=$this->createUrl('resetpasswordsuccess');
				$this->dealSuccess($url);
			}else{
				$errors = $model->getErrors();
				$this->dealError($errors);
			}
		}
		$this->render('forgetpassword_2');
	}

	/*
	* 忘记密码页面
	*/
	public function actionForgetpassword() {
		if( !Yii::app()->user->getIsGuest() ) {
			$this->redirect('/'); //已经登录跳转到首页
			return false;
		}
		$passwordForm=Yii::app()->request->getPost('passwordForm');
		if( $passwordForm ) {
			$model=new SetPasswordForm('forget');
			$model->attributes=$passwordForm;
			if( $model->validate() ){
				$model->forgetStep1();
				$url=$this->createUrl('resetpassword');
				$this->dealSuccess($url);
			}else{
				$errors = $model->getErrors();
				$this->dealError($errors);
			}
		}
		$this->render('forgetpassword');
	}
}

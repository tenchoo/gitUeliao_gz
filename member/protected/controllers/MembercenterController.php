<?php

class MembercenterController extends Controller {

	public function init() {
		parent::init();
		$this->routeFlag = "member::/membercenter/info";
	}
	/**
	 * 控制器权限规则
	 * @see CController::accessRules()
	 */
	public function accessRules() {
		return array(
			//未登陆的会员禁止访问
			array('deny', 'users'=>array('?'), 'expression'=>array($this,'isAjaxLogin'))
		);
	}

	/**
	* 用户未登录时ajax返回
	*/
	public function isAjaxLogin() {
		if( Yii::app()->request->getIsAjaxRequest() ) {
			if( !Yii::app()->user->id ) {
				$json=new AjaxData(false);
				$json->setMessage( 'user', 'You do not log in or log out' );
				echo $json->toJson();
				Yii::app()->end();
			}
		}
		return true;
	}

	/**
	* 手机号码修改step1
	*/
	public function actionPhonecheck(){
		$user = $this->getSecurityInfo();
		if( !$user->phone ) {
			$this->redirect('phonechange');
		}
		$step='1';
		$checkForm=Yii::app()->request->getPost('checkForm');
		if(  $checkForm ){
			$form=new AccountSecurityForm('phoneone');
			$form->attributes=$checkForm;
			$form->phone=$user->phone;
			if( $form->validate() ){
				Yii::app()->user->setState('changePhone',null );
				Yii::app()->user->setState('changeDeadline',null);
				Yii::app()->user->setState('SetNewPhone','1');
				$url=$this->createUrl('phonechange');
				$this->dealSuccess($url);
			} else {
				$errors = $form->getErrors();
				$this->dealError($errors);
			}
		}

		Yii::app()->user->setState('changePhone',$user->phone );
		Yii::app()->user->setState('changeDeadline',300+time());  //有效时间为5分钟
		$phone=RegForm::half_replace( $user->phone );
		$this->render('phonecheck',array('phone'=>$phone,'step'=>$step));
	}

	/**
	* 手机验证和手机号码修改step2
	*/
	public function actionPhonechange(){
		$user =$this->getSecurityInfo();

		$set=Yii::app()->user->getState('SetNewPhone');
		if( !empty( $user->phone ) && $set!=='1' ){
			$this->redirect('phonecheck');
		}

		$checkForm=Yii::app()->request->getPost('checkForm');
		if(  $checkForm ){
			$form=new AccountSecurityForm('phonetwo');
			$form->attributes=$checkForm;
			$form->oldphone=$user->phone;
			if( $form->validate() ){
				$user->phone=$form['phone'];
				$user->save();
				Yii::app()->user->setState('SetNewPhone',null );
				$url=$this->createUrl('accountsecurity');
				$this->dealSuccess($url);
			} else {
				$errors = $form->getErrors();
				$this->dealError($errors);
			}
		}

		$step='2';
		$this->render('phonecheck',array('phone'=>$user->phone,'step'=>$step));
	}


	/**
	* 账户安全页面
	*/
	public function actionAccountsecurity(){
		$model = $this->getSecurityInfo();
		$user=$model->attributes;
		$user['showEmail']=RegForm::half_replace( $user['email'] );
		$user['showphone']=RegForm::half_replace( $user['phone'] );
		$this->render('accountsecurity',array('user'=>$user));
	}

	/**
	* 邮箱验证,邮箱修改step2
	*/
	public function actionEmailcheck(){
		$model = $this->getSecurityInfo();
		$set=Yii::app()->user->getState('SetNewEmail');
		if( !empty( $model->email ) && $set!=='1' ){
			$this->redirect('emailchange');
		}

		$checkForm=Yii::app()->request->getPost('checkForm');
		if(  $checkForm ){
			$form=new AccountSecurityForm('email');
			$form->attributes=$checkForm;
			$form->oldemail=$model->email;

			if( $form->validate() ){
				$model->email=$form['email'];
				$model->save();
				Yii::app()->user->setState('SetNewEmail',null );
				$url=$this->createUrl('accountsecurity');
				$this->dealSuccess($url);
			} else {
				$errors = $form->getErrors();
				$this->dealError($errors);
			}
		}

		$this->render('emailcheck',array('email'=>$model->email));
	}

	/**
	* 邮箱修改step1
	*/
	public function actionEmailchange(){
		$model = $this->getSecurityInfo();
		if( !$model->email ) {
			$this->redirect('emailcheck');
		}
		$checkForm=Yii::app()->request->getPost('checkForm');
		if(  $checkForm ){
			$form=new AccountSecurityForm('emailone');
			$form->attributes=$checkForm;
			$form->email=$model->email;

			if( $form->validate() ){
				Yii::app()->user->setState('changeEmail',null );
				Yii::app()->user->setState('changeDeadline',null);
				Yii::app()->user->setState('SetNewEmail','1');
				$url=$this->createUrl('emailcheck');
				$this->dealSuccess($url);
			} else {
				$errors = $form->getErrors();
				$this->dealError($errors);
			}
		}

		Yii::app()->user->setState('changeEmail',$model->email );
		Yii::app()->user->setState('changeDeadline',300+time());  //有效时间为5分钟
		$email=RegForm::half_replace( $model->email );
		$this->render('emailcheck2',array('email'=>$email));
	}

	/*
	* 账户信息管理
	*/
	public function actionInfo(){
		$memberId = Yii::app()->user->id;
		$model = new EditForm();
		$model->getInfo( $memberId );
		if( !$model->memberId ){
			throw new CHttpException(404,"This member requires that does not exists.");
		}

		$Editinfo=Yii::app()->request->getPost('Editinfo');
		if( $Editinfo ){
			$model->attributes = $Editinfo;
			if( $model->save() ){
				$url=$this->createUrl('info');
				$this->dealSuccess($url);
			}else{
				$errors = $model->getErrors();
				$this->dealError($errors);
			}
		}
		$detailurl = $this->createUrl('detailinfo');
		$this->render('editinfo',array('info'=>$model->attributes,'detailurl'=>$detailurl ));
	}

	/*
	 * 账户详细信息管理
	*/
	public function actionDetailinfo(){
		//业务员没有此页面
		$userType = Yii::app()->user->getState('usertype');
		if( $userType == 'saleman'){
			$this->redirect('info');exit;
		}
		
		$model=tbProfileDetail::model()->findbypk(Yii::app()->user->id);

		if( !$model ) {
			$model = new tbProfileDetail();
			$model -> memberId =Yii::app()->user->id;
		}

		$Editdetailinfo=Yii::app()->request->getPost('Editdetailinfo');

		if( $Editdetailinfo ){
			if( isset($Editdetailinfo['mainproduct']) ){
				$Editdetailinfo['mainproduct']= serialize($Editdetailinfo['mainproduct']);
			}
			$Editdetailinfo['gm'] = serialize($Editdetailinfo['gm']);
			$Editdetailinfo['pdm'] = serialize($Editdetailinfo['pdm']);
			$Editdetailinfo['designers'] = serialize($Editdetailinfo['designers']);
			$Editdetailinfo['cfo'] = serialize($Editdetailinfo['cfo']);
			$model->attributes=$Editdetailinfo;
			$model->scenario = 'modify';
				if ( $model->validate() ){
					$model->save();
					$url=$this->createUrl('detailinfo');
					$this->dealSuccess($url);
				} else {
					$errors = $model->getErrors();
					$this->dealError($errors);
				}
		}

		$info = $model->attributes;

		if( isset( $info['mainproduct'] ) ){
			$info['mainproduct'] = unserialize($info['mainproduct']);
		}
		$info['gm'] = unserialize($info['gm']);
		$info['pdm'] = unserialize($info['pdm']);
		$info['designers'] = unserialize($info['designers']);
		$info['cfo'] = unserialize($info['cfo']);
		$infourl = $this->createUrl('info');
		$this->render('editdetailinfo',array('info'=>$info,'infourl'=>$infourl));
	}

	/**
	* 设置支付密码
	*/
	public function actionSetpaypassword(){
		$user = $this->getSecurityInfo();
		/* if( empty( $user->phone ) ){
			$this->redirect('phonecheck');
		} */

		$set=Yii::app()->user->getState('SetPaypassword');
		if( !empty( $user->paypassword ) && $set!=='1' ){
			$this->redirect('setpaypassword');
		}

		$passwordForm=Yii::app()->request->getPost('passwordForm');
		if( $passwordForm ) {
			$model=new SetPasswordForm('setpay');
			$model->attributes=$passwordForm;
			if( $model->validate() ){
				$user->paypassword=$user->passwordEncode($model->paypassword,2);
				$user->save();
				Yii::app()->user->setState('SetPaypassword',null );
				$url=$this->createUrl('accountsecurity');
				$this->dealSuccess($url);
			}else{
				$errors = $model->getErrors();
				$this->dealError($errors);
			}
		}

		$this->render('setpaypassword',array('phone'=>$user['phone']));
	}

	/**
	* 修改支付密码
	*/
	public function actionChangepaypassword(){
		$user = $this->getSecurityInfo();
		if( empty( $user->phone ) ){
			$this->redirect('phonecheckstep2');
		}

		if( empty( $user->paypassword ) ){
			$this->redirect('setpaypassword');
		}

		$passwordForm=Yii::app()->request->getPost('passwordForm');
		if( $passwordForm ) {
			$model=new SetPasswordForm('changepay');
			$model->attributes=$passwordForm;
			$model->account=$user->phone;
			if( $model->validate() ){
				Yii::app()->user->setState('changePhone',null );
				Yii::app()->user->setState('changeDeadline',null);
				Yii::app()->user->setState('SetPaypassword','1' );
				$url=$this->createUrl('setpaypassword');
				$this->dealSuccess($url);
			}else{
				$errors = $model->getErrors();
				$this->dealError($errors);
			}
		}

		Yii::app()->user->setState('changePhone',$user['phone'] );
		Yii::app()->user->setState('changeDeadline',300+time());  //有效时间为5分钟
		$phone=RegForm::half_replace( $user['phone'] );
		$this->render('changepaypassword',array('phone'=>$phone));

	}

	/**
	* 修改账号密码
	*/
	public function actionChangepassword() {
		$passwordForm=Yii::app()->request->getPost('passwordForm');
		if( $passwordForm ) {
			$model=new SetPasswordForm('change');
			$model->attributes=$passwordForm;
			if( $model->validate() && $model->changePassword() ){
				Yii::app()->user->setState('tologout','1' );
				$url=$this->createUrl('changepasswordsuccess');
				$this->dealSuccess($url);
			}else{
				$errors = $model->getErrors();
				$this->dealError($errors);
			}
		}

		$this->render('changepassword');
	}

	public function actionChangepasswordsuccess(){
		$set = Yii::app()->user->getState('tologout');
		if( $set ){
			Yii::app()->user->setState('tologout',null );
			Yii::app()->user->logout();
			$this->render('changepassword_2');
		}else{
			$this->redirect('accountsecurity');
		}

	}

	private function getSecurityInfo(){
		$criteria = new CDbCriteria;
		$criteria->select = 'memberId,email,paypassword,code,phone';
		$memberId = Yii::app()->user->id;
		$criteria->addCondition("memberId=$memberId");
		$user = tbMember::model()->find( $criteria );
		return $user;
	}

}
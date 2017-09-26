<?php
/**
 * ajax 检查是否已登录,若登录返回登录基本信息
 * @author liang
 * @version 0.1
 * @package Controller
 */
class UserController extends Controller {
	
	/**
	 * 会员注册
	 * @request method post
	 * @request array $form = array('account'=>'aaaa', 'password'=>'123456', 'captcha'=>1111)
	 * @proccess
	 * 对短信校验码进行验证
	 */
	public function actionCreate() {
		$account  = Yii::app()->request->getPost('account');
		$password = Yii::app()->request->getPost('password');
		$captcha  = Yii::app()->request->getPost('captcha');

		$mongoDB   = new CaptchaStorage('captcha');
		$tokenInfo = $mongoDB->getCaptcha($account);
		if($mongoDB->isExpired() || !$tokenInfo || $tokenInfo!==intval($captcha)) {
			$this->showJson(false, Yii::t('restful', 'invalid captcha value'));
			Yii::app()->end(200);
		}
		
		$userRecord = new tbMember;
		$userRecord->code=$userRecord->setrandomCode();
		$userRecord->setAttributes(array(
			'phone'    => $account,
			'password' => $userRecord->passwordEncode($password),
			'nickName' => $userRecord->half_replace($account),
			'state'    => 'Normal',
			'groupId'  => '2',
			'register' => date('Y-m-d H:i:s'),
			'ip'       => Yii::app()->request->userHostAddress,
		));
		
		if(!$userRecord->save()) {
			$errors = $userRecord->getErrors();
			$error = array_shift($errors);
			$this->state = false;
			$this->message = Yii::t('restful', $error[0]);
			$this->showJson();
			Yii::app()->end(200);
		}
		
		$this->release('tbProfile', $userRecord->memberId);
		$this->release('tbProfileDetail', $userRecord->memberId);
		
		$this->state = true;
		$this->message = Yii::t('restful', 'register successful');
		$this->showJson();
	}
	
	
	public function actionUpdate() {
		$openId = Yii::app()->request->getPut('openid');
		/* if(is_null($openId)) {
			return $this->showJson(false, Yii::t('restful','Invalid request'));
		} */
		
		$profile = Yii::app()->openidCache->get($openId);
		if(!$profile) {
			return $this->showJson(false, Yii::t('restful','session expired'));
		}
		
		$password        = Yii::app()->request->getPut('password');
		$newPassword     = Yii::app()->request->getPut('newpassword');
		$confirmPassword = Yii::app()->request->getPut('confirmpassword');
		
		if($newPassword !== $confirmPassword) {
			return $this->showJson(false, Yii::t('restful','confirm password not match'));
		}
		
		$user = tbMember::model()->findByPk($profile['memberId']);
		$password = $user->passwordEncode($password);
		
		if($password!==$user->password) {
			return $this->showJson(false, Yii::t('restful','password not match'));
		}
		
		$user->password = $user->passwordEncode($newPassword);
		if(!$user->save()) {
			$error = $user->getErrors();
			$error = array_shift($error);
			Yii::log($error[0], CLogger::LEVEL_ERROR, 'user change password');
			$this->showJson(false, Yii::t('restful',"failed for update password"));
		}
		$this->showJson(true, Yii::t('restful',"password update successful"));
	}

	public function actionShow() {
		$memberId = Yii::app()->request->getQuery('id');
		if(!$memberId || !is_numeric($memberId)) {
			$this->showJson(false, Yii::t('restful','Invalid member ID'));
			Yii::app()->end();
		}

		$userInfo = tbMember::model()->with('profiledetail','profile')->findByPk($memberId);
		if(!$userInfo) {
			$this->showJson(false, Yii::t('restful','not found account'));
			Yii::app()->end();
		}

		$basic = $userInfo->getAttributes(['memberId','phone','nickName']);

		if($userInfo->profile) {
			$profile = $userInfo->profile->getAttributes(['icon']);
			if(!empty($profile['icon'])) {
				$profile['icon'] = $this->getImageUrl($profile['icon'],null);
			}
		}
		else {
			$profile = ['icon'=>''];
		}

		if($userInfo->profiledetail) {
			$profiledetail = $userInfo->profiledetail->getAttributes(['shortname']);
		}
		else {
			$profiledetail = ['shortname'=>''];
		}

		$data = array_merge($basic, $profile, $profiledetail);
		$this->showJson(true, null, $data);
	}
	
	/*
	 * 短信验证码是否过期
	 * @param int $time
	 * @return bool
	 */
	private function tokenIsExprice($time) {
		$diff = time() - $time;
		if($diff > Yii::app()->params['tokenExpire']) {
			return false;
		}
		return true;
	}
	
	/**
	 * 关联信息存储
	 * @param string $model 模块名称
	 * @param int $memberId 会员编号
	 * @return null|mixed
	 */
	private function release($model, $memberId) {
		$instanc = new $model;
		$instanc->memberId = $memberId;
		return $instanc->save();
	}
}
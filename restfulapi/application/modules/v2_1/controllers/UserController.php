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
		$account       = Yii::app()->request->getPost('account');
		$password      = Yii::app()->request->getPost('password');
		$captcha       = Yii::app()->request->getPost('captcha');
		$company       = Yii::app()->request->getPost('company');
		$area          = Yii::app()->request->getPost('area');
		$address       = Yii::app()->request->getPost('address');
		$contactPerson = Yii::app()->request->getPost('contactPerson');

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
		
		$transaction = Yii::app()->db->beginTransaction();
		if(!$userRecord->save()) {
			$transaction->rollback();
			$errors = $userRecord->getErrors();
			$error = array_shift($errors);
			$this->state = false;
			$this->message = Yii::t('restful', $error[0]);
			$this->showJson();
			Yii::app()->end();
		}

		$profile=new tbProfile();
		$profile->username = '';
		$profile->icon = '';
		$profile->qq = '';
		$profile->memberId = $userRecord->memberId;
		if(!$profile->save()) {
			$transaction->rollback();
			$errors = $profile->getErrors();
			$error = array_shift($errors);
			$this->state = false;
			$this->message = Yii::t('restful', $error[0]);
			$this->showJson();
			Yii::app()->end();
		}

		$Detail=new tbProfileDetail( 'modify' );
		$Detail->tel = $userRecord->phone;
		$Detail->brand = '';
		$Detail->corporate = '';
		$Detail->companyname = $company;
		$Detail->shortname = mb_substr ( $company,0,10,"utf-8" );
		$Detail->mainproduct = '';
		$Detail->gm = '';
		$Detail->pdm = '';
		$Detail->designers = '';
		$Detail->cfo = '';
		$Detail->address = $address;
		$Detail->areaId = $area;
		$Detail->stallsaddress = '';

		$Detail->memberId = $userRecord->memberId;
		if (!$Detail->save()) {
			$transaction->rollback();
			$errors = $Detail->getErrors();
			$error = array_shift($errors);
			$this->state = false;
			$this->message = Yii::t('restful', $error[0]);
			$this->showJson();
			Yii::app()->end();
		}

		//加入到默认地址
		$Address = new tbMemberAddress();
		$Address->memberId = $userRecord->memberId;
		$Address->mobile = $userRecord->phone;
		$Address->areaId = $area;
		$Address->name = $contactPerson;
		$Address->address = $address;
		if (!$Address->save()) {
			$transaction->rollback();
			$errors = $Address->getErrors();
			$error = array_shift($errors);
			$this->state = false;
			$this->message = Yii::t('restful', $error[0]);
			$this->showJson();
			Yii::app()->end();
		}
		
		// $this->release('tbProfile', $userRecord->memberId);
		// $this->release('tbProfileDetail', $userRecord->memberId);
		
		$transaction->commit();
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

	public function actionDelete() {
		$openid = $this->getRequestParams('openid');
		if(is_null($openid)) {
			$this->showJson(false, Yii::t("restful", "Not found {filed}", ['{filed}'=>'openid']));
		}

		$info = Yii::app()->openidCache->get($openid);
		if($info) {
			tbMemberDevice::model()->deleteAll("memberId=:id",[':id'=>$info['memberId']]);
			Yii::app()->openidCache->delete($openid);
			return $this->showJson(true, Yii::t('restful','logout successfully'));
		}
		return $this->showJson(false, Yii::t('restful','faild to logout'));
	}

	/**
	 * 变更接收的消息状态
	 */
	public function actionMsgState() {
		$openId  = Yii::app()->request->getPut('openid');
		$profile = Yii::app()->openidCache->get($openId);
		if(!$profile) {
			return $this->showJson(false, Yii::t('restful','session expired'));
		}

		$messageState = Yii::app()->request->getPut('state');
		$userInfo     = tbMemberDevice::model()->findByAttributes(['memberId'=>$profile['memberId']]);

		if(!$userInfo) {
			return $this->showJson(false, Yii::t('restful','not found account'));
		}
		if($messageState === '0') {
			$userInfo->msgtype = 0;
		}
		elseif($messageState === '1') {
			$userInfo->msgtype = 1;
		}
		else {
			return $this->showJson(false, Yii::t('restful','Invalid {filed} value', ['{filed}'=>'state']));
		}

		//设置接收的消息类型
		if($userInfo->save()) {
			return $this->showJson(true, Yii::t('restful','message type changed successfully'));
		}
		else {
			$errors = $userInfo->getErrors();
			$message = json_encode($errors);
			Yii::log($message, CLogger::LEVEL_ERROR, 'database error');
			return $this->showJson(false, Yii::t('restful','faild message type change'));
		}
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
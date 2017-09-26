<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	const ERROR_DUPLICATE_LOGIN = 3;
	const ERROR_DISABLE_LOGIN = 4;
	private $_id;
	public $scenario;

	public function authenticate()
	{
		$user=AccountFactory::findAccount( $this->username,'all' );
		if( !$user ) {
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		}
		else{
			if( $user->state != 'Normal' ){
				$this->errorCode = self::ERROR_DISABLE_LOGIN;
				return false;
			}

			if($this->scenario == 'register'){
				$pasword = $this->password;
			}
			else{
				$pasword = $user->passwordEncode($this->password);
			}

			if ( $user->password !== $pasword ) {
				$this->errorCode = self::ERROR_PASSWORD_INVALID;
			}
			else {
				if(!$this->hasLogin($user->memberId)) {
					$this->errorCode = self::ERROR_DUPLICATE_LOGIN;
				}
				else {
					$this->setState('memberId', $user->memberId);
					$userType = ($user->groupId == '1')? "saleman" : "member";
					$this->setState("usertype", $userType);

					$this->setState("nickName", $user->nickName);

					$lastLogin = time();
					$this->setState('lastLoginTime', $lastLogin);
					$this->setState('loginTime', time());
					$this->errorCode = self::ERROR_NONE;
				}
			}
		}
		return !$this->errorCode;
	}

	public function getId()	{
		return $this->getState("memberId");
	}

	/**
	 * 用户是否已经登陆
	 * @param $userId
	 * @return bool
	 */
	protected function hasLogin($userId) {
		if(tbConfig::model()->get('lock_login')==='0') {
			return true;
		}

		$container = Yii::app()->getComponent('sessionContainer')->getInstance();
		$result = $container->findOne(["uid"=>$userId,"type"=>"member","device"=>"web"]);
		if(is_null($result)) {
			return true;
		}
		return false;
	}
}
<?php

/**
 * 会员登陆校验
 */
class UserIdentity extends CUserIdentity {
	
	const ERROR_DISABLE_LOGIN = 4;
	
	 public $scenario;

	public function authenticate()
	{
		$user= tbMember::model()->with('profile')->findByAttributes(array('phone'=>$this->username));
		if( $user ) {
			$password = $user->passwordEncode($this->password);

			if ( $user->password !== $password ) {
					$this->errorCode = self::ERROR_PASSWORD_INVALID;
			} else {
				if( $user->state != 'Normal' ){
					$this->errorCode = self::ERROR_DISABLE_LOGIN;
					return false;
				}
				
				$this->setState('memberId', $user->memberId);
				if( $user->groupId =='1' ){
					$this->setState("usertype", "saleman");
				}else{
					$this->setState("usertype", "member");
				}

				$this->setState("nickName", $user->nickName);
				$this->setState('icon', $user->profile->icon);

				$lastLogin=time();
				$this->setState('lastLoginTime', $lastLogin);
				$this->setState('loginTime', time());
				$this->errorCode = self::ERROR_NONE;

			}
		}else{
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		}
		return !$this->errorCode;
	}

	public function getId()	{
		return $this->getState("memberId");
	}
}

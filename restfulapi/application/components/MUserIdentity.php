<?php
/**
 * 执行用户登陆验证
 * @author yagasx
 * @version 0.1
 * @package CUserIdentity
 */
class MUserIdentity extends CUserIdentity {

	const ERROR_DUPLICATE_LOGIN = 3;
	const ERROR_DISABLE_LOGIN = 4;
	public $userId;

	/**
	 * 执行验证
	 * @see CUserIdentity::authenticate()
	 * @return boolean
	 */
	public function authenticate() {
		$user = tbUser::model()->find( 'account=:user', array(':user'=>$this->username) );
		if( is_null( $user ) ) {
			$this->errorCode = UserIdentity::ERROR_USERNAME_INVALID;
		}else if( $user->state != '0' ){
				$this->errorCode = self::ERROR_DISABLE_LOGIN;
		} else {

			$pwd = new ZPassword( ZPassword::AdminPassword );
			if( $pwd->checkPassword( $user->password, $this->password) ) {

				if(!$this->hasLogin($user->userId)) {
					$this->errorCode = self::ERROR_DUPLICATE_LOGIN;
				}
				else {
					$this->errorCode = UserIdentity::ERROR_NONE;
					$this->userId    = $user->userId;
					$this->setState( 'userId', $user->userId );
					$this->setState( 'roles', $this->getRoles( $user->userId ) );
					$this->setState( 'username', $user->username );
					$this->setState( 'isAdmin', $user->isAdmin );
				}
			}
			else {
				$this->errorCode = UserIdentity::ERROR_PASSWORD_INVALID;
			}
		}

		return $this->errorCode == UserIdentity::ERROR_NONE;
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

		$loginState = Yii::app()->mongoDB->collection("session");
		$result     = $loginState->findOne(["uid"=>$userId,"type"=>"user","device"=>"web"]);

		if(is_null($result)) {
			return true;
		}
		return false;
	}

	/**
	 * 获取会员ID
	 * 覆盖CUserIdentity::getId()方法
	 * @see CUserIdentity::getId()
	 * @return integer
	 */
	public function getId() {
		return $this->userId;
	}

	/**
	 * 获取会员所属所有角色组ID列表
	 * @param string $userId
	 * @return array
	 */
	protected function getRoles( $userId ) {
		$roles = tbUserrole::model()->findAllByAttributes(['userId'=>$userId]);
		if( $roles ) {
			$roles = array_map(function($row){
				return $row->roleId;
			}, $roles);
		}
		return $roles;
	}
}

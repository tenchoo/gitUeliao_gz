<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel
{
	public $username;
	public $password;
	public $verifyCode;
	public $rememberMe;

	private $_identity;


	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('username,password', 'required'),
			// password needs to be authenticated
			array('verifyCode', 'checkcaptcha'),
			array('password', 'authenticate'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'username'=>'手机号码',
			'password'=>'密码',
			'verifyCode'=>'验证码',

		);
	}

	/**
	* 检查图文验证码
	*/
	public function checkcaptcha($attribute,$params){
		$verify=$this->$attribute;
		$theverify=Yii::app()->user->getState('VerifyCode');
		if( $verify && strtolower( $verify ) === strtolower( $theverify ) ) {
			Yii::app()->user->setState('VerifyCode',null);
			return true;
		} else {
			$this->addError($attribute, Yii::t('reg','Verification code is not correct'));

		}
	}


	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params) {
		if(!$this->hasErrors()) {
			$this->_identity=new UserIdentity($this->username,$this->password);
			if( !$this->_identity->authenticate() ) {
				if($this->_identity->errorCode == UserIdentity::ERROR_DUPLICATE_LOGIN) {
					$this->addError('password', Yii::t("user", "duplicate login"));
				}else if( $this->_identity->errorCode == UserIdentity::ERROR_DISABLE_LOGIN ){
					$this->addError('password',Yii::t('user','Your account is frozen, please contact customer service.'));
				}
				else {
					$this->addError('password',Yii::t('user','The user name or password is wrong'));
				}
			}
		}
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if($this->_identity===null)
		{
			$this->_identity=new UserIdentity($this->username,$this->password);
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode===UserIdentity::ERROR_NONE)
		{
			$duration=$this->rememberMe ? 3600*24*30 : 0; // 30 days
			Yii::app()->user->login($this->_identity,$duration);
			return true;
		}
		else
			return false;
	}
}

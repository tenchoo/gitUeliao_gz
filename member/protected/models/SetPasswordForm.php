<?php

/**
 * RegForm class. 忘记注册-重置密码,修改密码 *
 * user reg form data. It is used by the 'reg' action of 'UserController'.
 */
class SetPasswordForm extends CFormModel
{
	public $account;
	public $password;
	public $repassword;
	public $verifyCode;
	public $oldpassword;
	public $paypassword;

	/**
	* Declares the validation rules.
	* The rules state that account and password are required,
	* and password needs to be authenticated.
	*/
	public function rules()
	{
		return array(
			array('account', 'required','message'=>Yii::t('reg','accout must be email or phone'),'on'=>'forget'),
			array('oldpassword', 'required','message'=>Yii::t('user','Please fill out the old password'),'on'=>'change'),
			array('password', 'required','on'=>'reset,change'),
			array('password', 'length', 'min'=>6, 'max'=>16,'tooLong'=>Yii::t('reg','Password length of 6-16, must contain data and letters'),'on'=>'reset,change'),
		//	array('password', 'ext.validators.passwordCheck','on'=>'reset'),
			array('repassword', 'required','message'=>Yii::t('reg','Please fill out the repeat password'),'on'=>'reset,change,setpay'),
			array('repassword', 'compare', 'compareAttribute'=>'password','message'=>Yii::t('reg','The two passwords not match'),'on'=>'reset,change'),

			array('verifyCode', 'required','message'=>Yii::t('reg','Please fill out the verification code'),'on'=>'forget,setpay,changepay'),
			array('account','checkAccountExist','on'=>'forget'),
			array('verifyCode','checkCaptcha','on'=>'forget,changepay'),

			array('paypassword', 'required','on'=>'setpay'),
			array('paypassword', 'length', 'min'=>6, 'max'=>16,'tooLong'=>Yii::t('reg','Password length of 6-16, must contain data and letters'),'on'=>'setpay'),
			array('repassword', 'compare', 'compareAttribute'=>'paypassword','message'=>Yii::t('reg','The two passwords not match'),'on'=>'setpay'),
			array('verifyCode','checkCaptcha2','on'=>'setpay'),

		);
	}

	/**
	* 检查图文验证码
	*/
	public function checkcaptcha2($attribute,$params){
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
	* 检查账号是否存在
	*/
	function checkAccountExist($attribute,$params){
		if( !$this->hasErrors() ) {
			$account=$this->$attribute;

			//查找账号是否存在
			if( $_resetmodel = AccountFactory::findAccount( $account,'all' ) ) {
				Yii::app()->user->setState('_resetmodel',$_resetmodel);
				return $account;
			}else{
				$this->addError($attribute,Yii::t('user','Account does not exist'));
			}
		}
	}

	/**
	* 检查验证码 ,为手机或邮箱发送的验证码
	*/
	public function checkCaptcha($attribute,$params){
		if( !$this->hasErrors() ) {
			if( !OpCaptcha::checkCaptcha($this->account,$this->verifyCode) ) {
				$this->addError('verifyCode',Yii::t('reg','Verification code is not correct'));
			}
		}
	}

	/**
	* 忘记密码 step1 把需重置密码的账号保存到会话,有效时间为5分钟
	*/
	public function forgetStep1() {
		Yii::app()->user->setState('forgetDeadline',300+time());  //有效时间为5分钟
	}

	public function restetPassword() {
		$_resetmodel = Yii::app()->user->getState( '_resetmodel' );
		if( !$_resetmodel && $account = Yii::app()->user->getState( 'resetphone' ) ){
			$_resetmodel = AccountFactory::findAccount( $account,'all' ) ;
		}
		if( $_resetmodel ){
			$_resetmodel->password = $_resetmodel->passwordEncode($this->password);
			if ( $_resetmodel->save() ){
				Yii::app()->user->setState('_resetmodel',null);
				Yii::app()->user->setState('forgetDeadline',null);
				return true;
			}
		}

		$this->addError( 'password',Yii::t('user','password restet failure') );
		return false;
	}


	/**
	* 修改密码
	*/
	public function changePassword(){
		$model=tbMember::model()->findbypk( Yii::app()->user->id );
		$pasword = $model->passwordEncode( $this->oldpassword );
		if ( $model->password !== $pasword ) {
			$this->addError('oldpassword',Yii::t('user','the oldpassword is not match'));
			return false;
		} else {
			$model->password=$model->passwordEncode($this->password);
			$model->save();
			return true;
		}
	}


	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'account' => '登录名',
			'password' => '登录密码',
			'repassword' => '确认密码',
			'verifyCode'=>'验证码',
			'paypassword'=>'支付密码',
		);
	}


}
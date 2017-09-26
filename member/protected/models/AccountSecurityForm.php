<?php

/**
 * RegForm class. 账户安全 *
 * user reg form data. It is used by the 'reg' action of 'UserController'.
 */
class AccountSecurityForm extends CFormModel
{
	public $email;
	public $phone;
	public $verifyCode;
	
	public $oldemail;
	public $oldphone;

	private $_id;

	/**
	* Declares the validation rules.
	* The rules state that account and password are required,
	* and password needs to be authenticated.
	*/
	public function rules()
	{
		return array(
			array('email', 'required','on'=>'email'),
			array('email', 'email','on'=>'email'),
			array('email', 'compare','compareAttribute'=>'oldemail','operator'=>'!=','message'=> Yii::t('user','{attribute} can not be the same as the original'),'on'=>'email'),
			array('phone', 'match', 'pattern'=>Regexp::$mobile, 'allowEmpty'=>false, 'message'=>Yii::t('user','Mobile phone number format is not correct'),'on'=>'phonetwo'),
			
			array('phone', 'compare','compareAttribute'=>'oldphone','operator'=>'!=','message'=> Yii::t('user','{attribute} can not be the same as the original'),'on'=>'phonetwo'),
			
			array('verifyCode', 'required','message'=>Yii::t('reg','Please fill out the verification code'),'on'=>'email,emailone,phoneone,phonetwo'),
			array('email', 'unique','caseSensitive'=>false,'className'=>'tbMember','message'=>Yii::t('user','{attribute} already exists'),'on'=>'email'),
			array('phone', 'unique','caseSensitive'=>false,'className'=>'tbMember','message'=>Yii::t('user','{attribute} already exists'),'on'=>'phonetwo'),

			array('verifyCode','checkCaptcha','on'=>'emailone,email,phoneone,phonetwo'),
		);
	}
	
	/**
	* 验证码 rule 规则
	*/
	public function checkCaptcha($attribute,$params){
		if( !$this->hasErrors() ) {
			$account=$this->email?$this->email:$this->phone;
			if( !OpCaptcha::checkCaptcha($account,$this->verifyCode) ) {
				$this->addError('verifyCode',Yii::t('reg','Verification code is not correct'));
			} else {
				return true;
			}
		}
	}
	
	/**
	 * @return array 定制字段的显示标签 (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'email' => '邮箱',
			'phone' => '手机号码',
		);
	}
	
	





}
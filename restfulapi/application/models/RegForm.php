<?php
/**
 * 注册
 * @author liang
 * @version 0.1
 * @package model
 */
class RegForm extends CFormModel
{
	public $account;
	public $phone;
	public $password;
	public $repassword;
	public $verifyCode;
	public $agree;
	public $memberId;

	public $token;

	/**
	* Declares the validation rules.
	* The rules state that account and password are required,
	* and password needs to be authenticated.
	*/
	public function rules()
	{
		if ( $this->scenario == 'addnew' ) {
			return array(
				array('phone,password,repassword','required'),
				array('phone', 'match', 'pattern'=>Regexp::$mobile, 'message'=>Yii::t('base','Mobile phone number format is not correct')),
				array('password', 'length', 'min'=>6, 'max'=>16,'tooLong'=>Yii::t('base','Password length of 6-16, must contain data and letters')),
				array('repassword', 'compare', 'compareAttribute'=>'password','message'=>Yii::t('base','The two passwords not match')),
				array ('phone','unique','className' => 'tbMember','attributeName' => 'phone'),
				array('phone,password,repassword','safe'),
			);
		}else{
			return array(
				array('account', 'required','message'=>Yii::t('reg','accout must be email or phone')),
				array('password', 'length', 'min'=>6, 'max'=>16,'tooLong'=>Yii::t('reg','Password length of 6-16, must contain data and letters')),
				//array('password', 'ext.validators.passwordCheck'),
				array('repassword', 'required','message'=>Yii::t('reg','Please fill out the repeat password')),
				array('repassword', 'compare', 'compareAttribute'=>'password','message'=>Yii::t('reg','The two passwords not match')),
				array('agree', 'required','requiredValue'=>'1','message'=>Yii::t('reg','Must agree to agreement')),
				array('verifyCode', 'required','message'=>Yii::t('reg','Please fill out the verification code')),
				array('account','userNameCheck'),
				array('verifyCode','checkCaptcha'),
			);
		}
	}

	/**
	* 验证码 rule 规则
	*/
	public function checkCaptcha($attribute,$params){
		if( !$this->hasErrors() ) {
			if( !OpCaptcha::checkCaptcha($this->account,$this->verifyCode) ) {
				$this->addError('verifyCode',Yii::t('reg','Verification code is not correct'));
			}
		}
	}

	/**
	* 验证码 rule 规则
	*/
	public function userNameCheck($attribute,$params){
		if( !$this->hasErrors() ) {
			//检查账号是否合法
			if( tbMember::checkAccountValid( $this->$attribute ) ) {
				//查找账号是否存在
				if( tbMember::model()->exists( 'phone =:phone',array(':phone'=>$this->$attribute) ) ){
					$this->addError($attribute,Yii::t('reg', 'The accout already exists'));
				}
			} else {
				$this->addError($attribute,Yii::t('reg', 'accout must be email or phone'));
			}
		}
	}



	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels()
	{
		return array(
			'account' => '登录名',
			'password' => '登录密码',
			'repassword' => '确认密码',
			'verifyCode'=>'验证码',
			'agree'=>'我已认真阅读并同意商场',
			'phone' => '手机号码',
		);
	}

	/**
	* register 注册后自动登录
	* @param boolean  $autologin 注册完成后自动登录
	* @return boolean whether login is successful
	*/
	public function register( $autologin = true ){
		$model=new tbMember();
		if( preg_match(Regexp::$mobile,$this->account) ){
			$model->phone=$this->account;
		}else {
			$model->email=$this->account;
		}

		$model->nickName=$this->half_replace($this->account);
		$model->state='Normal';
		$model->register=date('Y-m-d H:i:s');
		$model->ip=Yii::app()->request->userHostAddress;
		$model->code=$model->setrandomCode(); //此语句必须在密码加密之前
		$model->password=$model->passwordEncode($this->password);
		$model->groupId = 2; //默认为普通客户组
		if ( $this->scenario == 'addnew' ) {
			$model->userId = Yii::app()->user->id;
		}

		if( !$model->save() ){
			return false;
		}
		$this->memberId  = $model->memberId;
		$profile=new tbProfile();
		$profile->memberId = $model->memberId;
		$profile->username = '';
		$profile->icon = '';
		$profile->qq = '';
		$profile->save();

		$Detail=new tbProfileDetail();
		$Detail->memberId = $model->memberId;
		$Detail->tel = '';
		$Detail->brand = '';
		$Detail->corporate = '';
		$Detail->companyname = '';
		$Detail->mainproduct = '';
		$Detail->gm = '';
		$Detail->pdm = '';
		$Detail->designers = '';
		$Detail->cfo = '';
		$Detail->address = '';
		$Detail->stallsaddress = '';
		$Detail->save();

		if( $autologin ){
			//注册后自动登录
			$info = array(
						'memberId'=>$model->memberId,
						'usertype'=>($model->groupId =='1')?'saleman':'member',
						'nickName'=>$model->nickName,
						'icon'=>$profile->icon,
					);

			//生成 token
			$this->token = Token::create( $info );
		}

		return true;
	}


	public static function half_replace($str){
		if( empty($str) ) {
			return $str;
		}
		$len = strlen($str)/2;
		return substr_replace($str,str_repeat('*',$len),ceil(($len)/2),$len);
	}
}
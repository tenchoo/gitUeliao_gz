<?php

/**
 * RegForm class. 注册 *
 * user reg form data. It is used by the 'reg' action of 'UserController'.
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

	private $_identity;

	public $companyname;//公司名称

	public $address;//公司地址

	public $areaId;//所在地区ID

	public $contactPerson;

	/**
	* Declares the validation rules.
	* The rules state that account and password are required,
	* and password needs to be authenticated.
	*/
	public function rules()
	{
		if ( $this->scenario == 'addnew' ) {
			return array(
				array('companyname,areaId,address,contactPerson','required'),
				array('phone,password,repassword','required'),
				array('phone', 'match', 'pattern'=>Regexp::$mobile, 'message'=>Yii::t('base','Mobile phone number format is not correct')),
				array('password', 'length', 'min'=>6, 'max'=>16,'tooLong'=>Yii::t('base','Password length of 6-16, must contain data and letters')),
				array('repassword', 'compare', 'compareAttribute'=>'password','message'=>Yii::t('base','The two passwords not match')),
				array ('phone','unique','className' => 'tbMember','attributeName' => 'phone'),
				array('phone,password,repassword','safe'),
				array('companyname,areaId,address,contactPerson','safe'),
			);
		}else{
			return array(
				array('companyname,areaId,address,contactPerson','required'),
				array('account', 'required','message'=>Yii::t('reg','accout must be email or phone')),
				array('password', 'length', 'min'=>6, 'max'=>16,'tooLong'=>Yii::t('reg','Password length of 6-16, must contain data and letters')),
				//array('password', 'ext.validators.passwordCheck'),
				array('repassword', 'required','message'=>Yii::t('reg','Please fill out the repeat password')),
				array('repassword', 'compare', 'compareAttribute'=>'password','message'=>Yii::t('reg','The two passwords not match')),
				array('agree', 'required','requiredValue'=>'1','message'=>Yii::t('reg','Must agree to agreement')),
				array('verifyCode', 'required','message'=>Yii::t('reg','Please fill out the verification code')),
				array('account','ext.validators.userNameCheck'),
				array('verifyCode','checkCaptcha'),
				array('companyname,areaId,address,contactPerson','safe'),
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
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels()
	{
		return array(
			'companyname' => '公司名称',
			'areaId' => '公司地址',
			'address' => '公司详细地址',
			'contactPerson' => '联系人',
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

		if( !$this->validate() ){
			return false;
		}

		$model = new tbMember();
		if( preg_match(Regexp::$mobile,$this->account) ){
			$model->phone = $this->account;
		}else {
			$model->email = $this->account;
		}

		$model->nickName = $this->half_replace($this->account);
		$model->state = 'Normal';
		$model->register = date('Y-m-d H:i:s');
		$model->ip = Yii::app()->request->userHostAddress;
		$model->code = $model->setrandomCode(); //此语句必须在密码加密之前
		$model->password = $model->passwordEncode($this->password);
		$model->groupId = 2; //默认为普通客户组
		if ( $this->scenario == 'addnew' ) {
			$model->userId = Yii::app()->user->id;
		}

		$profile=new tbProfile();
		$profile->username = '';
		$profile->icon = '';
		$profile->qq = '';

		$Detail=new tbProfileDetail( 'modify' );
		$Detail->tel = $model->phone;
		$Detail->brand = '';
		$Detail->corporate = '';
		$Detail->companyname = $this->companyname;
		$Detail->shortname = mb_substr ( $this->companyname,0,10,"utf-8" );
		$Detail->mainproduct = '';
		$Detail->gm = '';
		$Detail->pdm = '';
		$Detail->designers = '';
		$Detail->cfo = '';
		$Detail->address = $this->address;
		$Detail->areaId = $this->areaId;
		$Detail->stallsaddress = '';

		$transaction = Yii::app()->db->beginTransaction();
		try {
			if( !$model->save() ){
				$this->addErrors ( $Detail->errors );
				return false;
			}

			$this->memberId  = $model->memberId;

			$profile->memberId = $model->memberId;
			if( !$profile->save() ){
				$this->addErrors ( $profile->errors );
				return false;
			}

			$Detail->memberId = $model->memberId;
			if ( !$Detail->save() ) {
				$this->addErrors ( $Detail->errors );
				return false;
			}

			//加入到默认地址
			$Address = new tbMemberAddress();
			$Address->memberId = $model->memberId;
			$Address->mobile = $model->phone;
			$Address->areaId = $this->areaId;
			$Address->name = $this->contactPerson;
			$Address->address = $this->address;
			if ( !$Address->save() ) {
				$this->addErrors ( $Address->errors );
				return false;
			}

			$transaction->commit();
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			throw new CHttpException(500,$e);
			return false;
		}

		if( $autologin ){
			//注册后自动登录
			$this->_identity=new UserIdentity($this->account,$this->password);
			$this->_identity->authenticate();
			if($this->_identity->errorCode===UserIdentity::ERROR_NONE)
			{
				Yii::app()->user->login($this->_identity,'0');
			}
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
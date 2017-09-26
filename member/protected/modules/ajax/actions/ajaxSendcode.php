<?php
/**
 * ajax 发送手机/邮箱验证码
 * @author liang
 * @version 0.1
 * @param string optype 操作类型
 * @param string account 发送对象,用于注册和忘记密码
 * @param string email 发送对象,用于手机修改
 * @param string phone 发送对象,用于手机修改
 * @use 用于注册，修改密码，修改手机，修改邮箱，修改支付密码
 * @package CAction
 */
class ajaxSendcode extends CAction {
	private $_state = false ;
	private $_data = '' ;
	private $_e = 0;
	private $_account = '';

	public function run() {
		$optype = Yii::app()->request->getParam('optype');
		if( method_exists ( $this,$optype ) ) {
			$this->_state = $this->$optype();
		}

		if( $this->_state ){
			$this->_data = OpCaptcha::sendCaptcha($optype,$this->_account);  //发送验证码
		}
		$json=new AjaxData($this->_state,'',$this->_data);
		if( $this->_e ){
			$json->setMessage( 'user', 'Page has expired' );
		}

		echo $json->toJson();
		Yii::app()->end();
	}

	/**
	 * ajax 注册，发送验证码
	 * @param string account 验证码发送对象
	 */
	private function reg(){
		$this->_account = Yii::app()->request->getParam('account');
		//检查账号是否合法
		if( tbMember::checkAccountValid( $this->_account ) ) {
			//查找账号是否存在
			if( AccountFactory::findAccount( $this->_account ) ) {
				$data['account'][0] = Yii::t ( 'reg', 'The accout already exists' );
			} else {
				return true;
			}
		} else {
			$data['account'][0] = Yii::t ( 'reg', 'accout must be phone' );
		}
		$this->_data = $data;
		return false;
	}

	/**
	 * ajax 忘记密码，发送验证码
	 * @param string account 验证码发送对象
	 */
	private function forget(){
		$this->_account = Yii::app()->request->getParam('account');
		//查找账号是否存在
		if( AccountFactory::findAccount( $this->_account ) ) {
			return true;
		}else{
			$data['account'][0] = Yii::t ( 'user', 'Account does not exist' );
			$this->_data = $data;
			return false;
		}
	}

	/**
	 * ajax 修改手机号 -- 新手机验发送证码
	 * @param string phone 新手机号码，验证码发送对象
	 */
	private function setPhone(){
		if( !( Yii::app()->user->id ) ) {
			$this->_e = 1 ;
			return false;
		}
		$this->_account = Yii::app()->request->getParam('phone');
		if ( preg_match( Regexp::$mobile,$this->_account )  ) {
			//查找账号是否存在
			if( AccountFactory::findAccount( $this->_account ) ) {
				$data['phone'][0] = Yii::t ( 'user', 'The phone already exists' );
			} else{
				return true;
			}
		} else {
			$data['phone'][0] = Yii::t ( 'user', 'Mobile phone number format is not correct' );
		}
		$this->_data = $data;
		return false;
	}

	/**
	 * ajax 修改邮箱 -- 新邮箱发送验证码
	 * @param string email 新邮箱，验证码发送对象
	 */
	private function changeEmail2(){
		if( !( Yii::app()->user->id ) ) {
			$this->_e = 1 ;
			return false;
		}
		$this->_account =  Yii::app()->request->getParam('email');

		//判断email是否合法
		$validator = new CEmailValidator;
		$validator->allowEmpty=false;
		if ( $validator->validateValue( $this->_account ) ) {
			//查找账号是否存在
			if( AccountFactory::findAccount( $this->_account ) ) {
				$data['email'][0] = Yii::t ( 'user', 'The email already exists' );
			} else{
				return true;
			}
		} else {
			$data['email'][0] = Yii::t ( 'user', 'Email format is not correct' );
		}

		$this->_data = $data;
		return false;
	}

	/**
	* 检查需发送的对象是否正常
	*/
	private function checkState( $stateName ){
		$this->_account = Yii::app()->user->getState( $stateName );
		$deadline=Yii::app()->user->getState('changeDeadline');
		if( !( Yii::app()->user->id ) || empty( $this->_account ) || $deadline < time() ) {
			$this->_e = 1 ;
			return false;
		} else {
			return true;
		}
	}

	/**
	* 修改手机号 -- 原手机验发送证码
	*/
	private function changePhone(){
		return $this->checkState( 'changePhone' );
	}

	/**
	* 修改邮箱 -- 原邮箱发送验证码
	*/
	private function changeEmail(){
		return $this->checkState( 'changeEmail' );
	}

	/**
	* 修改支付密码
	*/
	private function chagePayPassword(){
		return $this->checkState( 'changePhone' );
	}

	/**
	 * ajax 申请开店
	 * @param string phone 验证码发送对象
	 */
	private function shopapply(){
		if( !( Yii::app()->user->id ) ) {
			$this->_e = 1 ;
			return false;
		}
		$this->_account = Yii::app()->request->getParam('phone');
		if ( preg_match( Regexp::$mobile,$this->_account )  ) {
			return true;
		} else {
			$data['phone'][0] = Yii::t ( 'user', 'Mobile phone number format is not correct' );
			$this->_data = $data;
		}
		return false;
	}

}
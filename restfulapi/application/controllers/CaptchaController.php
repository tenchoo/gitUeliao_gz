<?php
/**
 * 手机验证短信发送
 * @author yagas
 *
 */
class CaptchaController extends Controller {

	//可接受的短信验证码发送请求
	private $_type = ['reg','forget'];

	//随机校验码
	private $_captha;


	private $_mobileExp = '/^1[34578][0-9]{9}$/';

	/**
	 * 获取验证码
	 * 对发送请求类型进行校验
	 * 对会员帐号进行校验
	 */
	public function actionCreate() {
		$msg     = null;
		$type    = Yii::app()->request->getPost('type');
		$account = Yii::app()->request->getPost('account');

		if (empty($account) || empty($type) ){
			$msg = Yii::t('msg', 'Missing parameter');
			goto error_information;
		}

		//判断手机号是否正确
		if( !preg_match( $this->_mobileExp,$account ) ){
			$msg = Yii::t('restful', 'Illegal mobile phone number');
			goto error_information;
		}

		$flag = $this->authenticate($account);
		switch($type){
			case 'forget': //忘记密码，手机号已注册时发送验证码
				if(!$flag){
					$msg = Yii::t('restful', 'not found account');
					goto error_information;
				}
				break;
			case 'reg': //注册，手机号已注册时返回false
				if($flag){
					$msg = Yii::t('restful', 'account has exists');
					goto error_information;
				}
				break;
			default:
				$msg = Yii::t('restful', 'invalid type request');
				goto error_information;
				break;
		}

		if(!$this->sendCaptha($type, $account)) {
			$msg = Yii::t('restful', 'send failed, try again');
			goto error_information;
		}

		$this->state   = true;
		$this->message = Yii::t('restful', 'send captha successful');
		$this->data    = array('captcha'=>$this->randCaptha());
		echo $this->showJson();
		Yii::app()->end();

		error_information:
			$this->state = false;
			$this->message = $msg;
			echo $this->showJson();
	}

	/**
	 * 对帐号进行校验
	 * @param string $account 会员帐号(手机号)
	 * @param string $msg 返回错误信息
	 * @return bool
	 */
	private function authenticate($account, & $msg=null) {
		$userInfo = tbMember::model()->findByAttributes(array('phone'=>$account));
		if(is_null($userInfo)) {
			return false;
		}
		else {
			return true;
		}
	}


	/**
	 * 随机生成检验码 */
	private function randCaptha() {
		if( !$this->_captha) {
			$this->_captha = rand(100000,999999);
		}
		return $this->_captha;
	}


	private function formatter($type){
		$c = new CDbCriteria;
		$c->compare('t.`key`',array('sms_'.$type,'sms_default'));
		$model = tbConfig::model()->findAll( $c );
		foreach ( $model as $item ){
			$bodys[$item->key] = $item->value;
		}

		$body = !empty($bodys['sms_'.$type])?$bodys['sms_'.$type]:$bodys['sms_default'];
		$arr=array(
			'title'=>'操作验证码',
			'body'=>str_replace('{code}', $this->randCaptha(), $body),
			);
		return $arr['body'];
	}

	/**
	 * 发送验证码信息
	 * @param string $type 发送类型
	 * @param string $account 接收帐号
	 * @return bool
	 */
	private function sendCaptha($type, $account) {
		$sms = $this->formatter($type);
		$server = new PhoneCode();
		
		//发送的同时已经存储到数据库记录表中。
		if($server->send($account, $sms)) {

			// 存储验证码
			$captcha = new CaptchaStorage('captcha');
			$captcha->setCaptcha($account, $this->randCaptha());

			// $this->log($account, $sms);
			return true;
		}
		return false;
	}

	/**
	 * 记录已发送的验证码
	 * @param $account
	 * @param $content
	 * @return bool
	 */
	private function log($account, $content) {
		$newRecord = new tbPhoneLog;
		$newRecord->attributes = array(
			'account'    => $account,
			'content'    => $content,
			'createTime' => date('Y-m-d H:i:s'),
			'memberId'   => 0,
			'type'       => 0,
			'state'      => 1
		);
		return $newRecord->save();
	}
}
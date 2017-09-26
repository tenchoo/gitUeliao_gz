<?php
/**
 * 手机验证短信发送
 * @author yagas
 *
 */
class CaptchaController extends Controller {

	//随机校验码
	private $_captha;

	private $_mobileExp = '/^1[34578][0-9]{9}$/';

	//可接受的userType值
	private $_userType = ['member','seller'];

	//可接受的event值
	private $_event = ['reg', 'forget'];

	private static $collections = [];

	//开发者账号校验
	public function init() {
//		$this->authenticateToken();
	}
	/**
	 * 获取验证码
	 * 对发送请求类型进行校验
	 * 对会员帐号进行校验
	 */
	public function actionCreate() {

		$this->checkParam("usertype", $this->_userType);
		$this->checkParam("event",    $this->_event);

		$userType = $this->getRequestParams('usertype', 'member');
		$event    = $this->getRequestParams('event');
		$account  = $this->getRequestParams('phone');

		//判断手机号是否正确
		if( !preg_match( $this->_mobileExp, $account ) ){
			$this->showJson(false, Yii::t('restful', 'Illegal mobile phone number'));
		}

		$member = $this->fetchMember($userType)->findByPhone($account);

		switch ($event) {
			case 'reg':
				if($userType === 'seller') {
					$this->showJson(false, Yii::t('restful', 'not found action'));
				}
				if($member instanceof CActiveRecord) {
					$this->showJson(false, Yii::t("restful", "account has exists"));
				}
				break;

			case 'forget':
				if(is_null($member)) {
					$this->showJson(false, Yii::t("restful", "not found account"));
				}
				break;
		}

		if(!$this->sendCaptha($event, $account)) {
			$this->showJson(false, Yii::t('restful', 'send failed, try again'));
		}

		$this->state   = true;
		$this->message = Yii::t('restful', 'send captha successful');

		//为测试环境输出验证码
		if(YII_DEBUG) {
			$this->data    = array('captcha'=>$this->randCaptha());
		}
		echo $this->showJson();
		Yii::app()->end();
	}

	/**
	 * 通过手机号获取短信校验码进行匹配测试
	 */
	public function actionIndex() {
		$phone = $this->getRequestParams('phone');
		$code  = $this->getRequestParams('code');
		if(!$phone) {
			$this->showJson(false, Yii::t('seller', 'Illegal mobile phone number'));
		}
		if(!$code) {
			$this->showJson(false, Yii::t('seller', 'invalid captcha value'));
		}

		$captcha = $this->getCaptcha($phone);
		if(!$captcha || strnatcmp($captcha['captcha'], $code)!==0) {
			$this->showJson(false, Yii::t('restful', 'captcha no match'));
		}

		$this->showJson(true, Yii::t('restful', 'captcha is match'));
	}

	/**
	 * 创建用户对象
	 * @param $userType
	 * @return null|static
	 */
	private function fetchMember($userType) {
		switch ($userType) {
			case 'member':
				return tbMember::model();
				break;

			case 'seller':
				return tbSupplierAccount::model();
				break;

			default:
				return null;
				break;
		}
	}

	/**
	 * 参数数据检验
	 * @param $param
	 * @param $validate
	 * @return bool
	 */
	private function checkParam($param, $validate=null) {
		$field = $this->getRequestParams($param);

		if(is_null($field)) {
			$this->showJson(false, Yii::t('restful', 'Not found {filed}', ['{filed}'=>$param]));
			return false;
		}
		elseif(!is_null($validate) && !in_array($field, $validate)) {
			$this->showJson(false, Yii::t('restful', 'Invalid {filed} value', ['{filed}'=>$param]));
			return false;
		}
		return true;
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

	/**
	 * 获取mongoDB数据实例
	 * @param string $collection 数据集合
	 * @param string $hostInfo 主机信息array('mongodb://localhost:27017', 'dbname')
	 * @return CMongoDB
	 */
	public function & mongoDB($collection, $hostInfo=null) {
		if(!array_key_exists($collection, self::$collections)) {
			$mongoDB = Yii::app()->mongoDB->getMongoDB();
			self::$collections[$collection] = $mongoDB->selectCollection(Yii::app()->mongoDB->dbname, $collection);
		}

		return self::$collections[$collection];
	}

	/**
	 * 读取openid中包含的账号信息
	 */
	private function readOpenid() {
		$openId = $this->getRequestParams('openid');

		if(!$openId) return false;

		$openId = base64_decode($openId);
		if(!$openId) return false;

		$openId = Yii::app()->securityManager->decrypt($openId, SECURITY_MASK);
		$info = array_combine(['uid','account','type','supplierId','time'], explode('.', $openId));

		return $info;
	}
}

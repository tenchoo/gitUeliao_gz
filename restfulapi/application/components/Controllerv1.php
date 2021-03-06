<?php
class Controllerv1 extends CController {

	/**
	*@var int 登录memberId
	*/
	public $memberId;

	/**
	*@var str 登录用户类型，用于判断是业务员还是普通客户
	*/
	public $userType;

	public $state = false;

	public $message = '';

	public $data = null;

	/**
	* @var str 访问来源，'web', 'wx', 'ios', 'android','unknow'
	* @use 用于下单时记录订单来源
	*/
	protected $_source;

	public function init() {
		$this->authenticateToken();
		
		//须初始化值
		$this->_source = 'android';
		parent::init();
	}

	/**
	* 取得访问来源
	*/
	public function getSource(){
		return $this->_source;
	}


	/**
	 * 输出json信息
	 * @param bool $state
	 * @param string $message
	 * @param mixed $data
	 */
	final public function showJson($state=null, $message=null, $data=null){
		$state   = is_null($state)? $this->state : $state;
		$message = is_null($message)? $this->message : $message;
		$data    = is_null($data)? $this->data : $data;

		$json = new AjaxData($state, $message, $data);
		$data = $json->toArray();
		echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

		//echo $json->toJson();

		//Notice:
		//to JAVA recomment use JSON_UNESCAPED_SLASHES and JSON_UNESCAPED_UNICODE
		//@exmaple
		//$data = $json->toArray();
		//echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

		Yii::app()->end();
	}

	final public function notFound(){
		$this->message = 'not found';
		$this->showJson();
	}


	/**
	 * 获取图片相对于网站的路径
	 * @param string $url  存储路径
	 * @param number $size 缩略图大小
	 * @return string
	 */
	final public function getImageUrl( $url, $size=100 ) {
		if(is_null($size)) {
			return Yii::app()->params['domain_images'] . $url;
		}
		return Yii::app()->params['domain_images'] . $url . '_' . $size;
	}

	/**
	 * 验证会员登陆状态 */
	final public function authenticateMemberInfo() {

		//排除不需要进行登陆验证的方法
		$controllerName = get_called_class();
		$controllerName = strtolower(substr($controllerName,0,-10));
		if(in_array($controllerName, Yii::app()->params['no_need_login'])) {
			return true;
		}

		$openId = $this->getRequestParams('openid');
		$profile = Yii::app()->openidCache->get($openId);

		if(!$profile) {
			$this->showJson(false, Yii::t('restful','please login again'));
		}

		$this->memberId = $profile['memberId'];
		$this->userType = $profile['usertype'];
	}

	/**
	 * 会员验证码
	 * @param $memberId
	 * @param $device
	 * @param $code
	 * @return string
	 */
	private function memberSign($memberId, $device, $code) {
		//$code value: sign_rand
		$codeInfo = explode('_', $code);
		if(count($codeInfo)!==2) {
			$this->showJson(false, Yii::t('restful','invalid member code'));
		}

		$sign = sha1($memberId.$device.$codeInfo[1]);
		return substr($sign, 0, 20).'_'.$codeInfo[1];
	}

	/**
	 * 获取mongoDB数据实例
	 * @param string $collection 数据集合
	 * @param string $hostInfo 主机信息array('mongodb://localhost:27017', 'dbname')
	 * @return CMongoDB
	 */
	public function & mongoDB($collection, $hostInfo=null) {
		if(is_null($hostInfo)) {
			$hostInfo = Yii::app()->params['mongoConf'];
		}

		list($host, $dbname) = $hostInfo;
		$conf = array(
				'class'         => 'CMongoDB',
				'connectString' => $host,
				'dbname'        => $dbname,
				'collection'    => $collection
		);
		$mongoDB = Yii::createComponent($conf);
		$mongoDB->init();
		return $mongoDB;
	}

	final public function getCaptcha($account) {
		$mongoDB = $this->mongoDB('captcha');
		return $mongoDB->findOne(array('account'=>$account));
	}

	/**
	 * 验证接口请求安全性
	 * @get string account 开发者账号
	 * @get string token   身份验证码
	 * @get int    salt    时间戳
	 */
	final public function authenticateToken() {
		$sign     = $this->getRequestParams('sign');
		$signInfo = explode('_', $sign);

		if(count($signInfo)!==3) {
			$this->showJson(false, Yii::t('restful','Invalid sign value'));
		}

		//$signInfo value is array(token, account, rand)
		$developer = tbRestfulAccount::model()->findByAccount($signInfo[1]);
		if(is_null($developer) || !$developer->checkToken($sign, $signInfo[2])) {
			$this->showJson(false, Yii::t('restful','Illegal develop account'));
		}

		if(strtotime($developer->expires) < time()) {
			$this->showJson(false, Yii::t('restful','account has been expired'));
		}
	}

	/**
	 * 分别提取不同的请求参数
	 * @param string $filed 参数名
	 * @return mixed
	 */
	final public function getRequestParams($filed,$defaultValue=null) {
		switch(Yii::app()->request->getRequestType()) {
			case 'POST':
				return Yii::app()->request->getPost($filed,$defaultValue);
				break;

			case 'PUT':
				return Yii::app()->request->getPut($filed,$defaultValue);
				break;

			case 'DELETE':
				return Yii::app()->request->getDelete($filed,$defaultValue);
				break;

			default:
				return Yii::app()->request->getQuery($filed,$defaultValue);
		}
	}

	/**
	 * 读取openid中包含的账号信息
	 */
	public function readOpenid() {
		$openId = $this->getRequestParams('openid');
		$userInfo = Yii::app()->openidCache->get($openId);
		if($userInfo) {
			return unserialize($userInfo);
		}
		return false;
	}
//	public function readOpenid() {
//		$openId = $this->getRequestParams('openid');
//		$openId = str_replace(['-','_','*'], ['+','/','='], $openId);
////		$openId = urldecode($openId);
//
//		if(!$openId) return false;
//
//		$openId = base64_decode($openId);
//		if(!$openId) return false;
//
//		$openId = Yii::app()->securityManager->decrypt($openId, SECURITY_MASK);
//		$info = array_combine(['uid','account','type','supplierId','time'], explode('.', $openId));
//
//		return $info;
//	}

	public function createOpenid($data) {
		$key = md5(SECURITY_MASK.time());
		Yii::app()->openidCache->add($key, serialize($data));
		return $key;
	}
}
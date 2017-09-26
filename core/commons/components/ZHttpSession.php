<?php
/**
 * 会话管理
 * 将会话写入mongoDB库中
 * @author yagas
 * @package CHttpSession
 * @version 2.0
 * @modify 2015-08-05
 * @modify 2016-04-22
 */

class ZHttpSession extends CHttpSession {

	/**
	 * 存储会话容器的ID
	 * @var string
	 */
	public $CacheId;

	/**
	 * 键名前缀
	 * @var string
	 */
	public $keyPrefix = 'session.';

	/**
	 * 是否启用mongoDB存储会话数据
	 * @var boolean
	 */
	public $custom    = false;

	/**
	 * 是否仅在安全链接上使用cookie
	 * @var bool
	 */
	public $onlySSL   = false;

	public $collection = "session";

	/**
	 * 初始化获取mongo存储接口
	 * @see CHttpSession::init()
	 */
	public function init() {
		$SessionHandler = Yii::app()->getComponent($this->CacheId)->collection($this->collection);
		session_set_cookie_params(ini_get('session.gc_maxlifetime'), '/', DOMAIN, $this->onlySSL, true);
		if($this->getUseCustomStorage()) {
			$seesion = new Sessionable($SessionHandler);
			session_set_save_handler($seesion, true);
		}

		if($this->autoStart) {
			$cookies = Yii::app()->request->getCookies();
			if(isset($cookies['PHPSESSID'])) {
				session_id( $cookies['PHPSESSID']->value );
			}
			@session_start();
		}
	}

	/**
	 * 是否开启自定义session存储
	 * @see CHttpSession::getUseCustomStorage()
	 */
	public function getUseCustomStorage() {
		return $this->custom;
	}
}

/**
 * 自定义session存储机制
 */
class Sessionable extends SessionHandler implements SessionHandlerInterface {

	private static $handle;
	private $gc_maxlifetime;

	public function __construct($session_handler) {
		self::$handle = $session_handler;
		$this->gc_maxlifetime = (int)ini_get('session.gc_maxlifetime');
	}

	public function write($session_id, $session_data) {
		$session = self::$handle->findOne(array('key'=>$session_id));
		if(!is_null($session)) {
			$session = $this->decode($session);
		}
		else {
			$session = ['key'=>$session_id];
		}

		$session["value"]  = $session_data;
		$session["device"] = Yii::app()->user->getState('device', 'web');

		$isAdmin = Yii::app()->user->getState('isAdmin');
		if(is_null($isAdmin)) {
			$session['type'] = 'member';
			$session['id'] = Yii::app()->user->getState('memberId',0);
			$session['userType'] = Yii::app()->user->getState('usertype','guest');
		}
		else {
			$session['type'] = 'user';
			$session['id'] = Yii::app()->user->getState('userId',0);
			$session['userType'] = intval($isAdmin)===1? 'administrator' : 'worker';
		}

		$expire = time() + $this->gc_maxlifetime;
		return self::$handle->save($session);
	}

	public function read($session_id) {
		$result = self::$handle->findOne(array('key'=>$session_id));
		if($result) {
			return $result->value;
		}
	}

	public function open($save_path, $session_id) {
		return true;
	}

	public function close() {
		return true;
	}

	public function gc($maxlifetime) {
		self::$handle->delete(array('expire'=>["\$lt"=>time()+$maxlifetime]));
	}

	public function destroy($session_id) {
		self::$handle->delete(array('key'=>$session_id));
	}

	private function decode($stdClass) {
		$data = array();
		foreach($stdClass as $key => $val) {
			$data[$key] = $val;
		}
		return $data;
	}
}

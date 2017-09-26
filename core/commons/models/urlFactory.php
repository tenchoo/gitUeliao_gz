<?php
/**
 * url路径工厂
 * @author yagas
 *
 */
class urlFactory extends CModel {
	private static $_instance;
	
	public function attributeNames() {
		return false;
	}
	
	/**
	 * 单例返回对象
	 */
	static public function model() {
		if( !self::$_instance ) {
			self::$_instance = new urlFactory();
		}
		return self::$_instance;
	}
	
	/**
	 * 按不同项目的路由规则创建链接地址
	 * @param string $route
	 * @param array $params
	 * @return string
	 */
	public function createUrl( $route, $params=array() ) {
		$point  = strpos( $route, '::' );
		$domain = substr( $route, 0, $point );
		$link   = substr( $route, $point+2 );
		$interf = $this->interf( $domain, 'createUrl');
		$params['route'] = $link;
		return self::fetchUrl( $interf, $params );
	}
	
	/**
	 * 发送post请求
	 * @param string $url
	 * @param array $data
	 * @return string
	 */
	final static public function fetchUrl( $url, $data=array() ) {
		$data['sign'] = ZService::signKey( $data );
	
		try{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT, Yii::app()->request->userAgent);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1); //链接超时时间
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_COOKIE, self::model()->cookies() );
			$content = curl_exec($ch);
			curl_close($ch);
			return $content;
		}
		catch(Exception $error) {
			return "";
		}
	}
	
	/**
	 * 提取cookie信息
	 * @return string
	 */
	protected function cookies() {
		$cookieStr = '';
		$cookies   = Yii::app()->request->cookies;
		foreach ( $cookies as $item ) {
			$cookieStr .= ";" . $item->name . "=" . $item->value;
		}
		return substr( $cookieStr, 1 );
	}
	
	/**
	 * 拼接请求地址
	 * @param string $domain
	 * @param string $action
	 * @return string
	 */
	protected function interf( $domain, $action ) {
		$point  = strpos( $_SERVER['HTTP_HOST'], '.' );
		$domain = $domain . substr( $_SERVER['HTTP_HOST'], $point );		
		$serivce = "http://" . $domain . '/service/' . $action;
		return $serivce;
	}
}

class ZClient extends CModel {
	private $_server;
	
	public function attributeNames() {
		$ref = new ReflectionClass( $this );
		return $ref->getProperties();
	}
	
	public function __construct( $projectName ) {
		$main = Yii::app()->request->hostInfo;
		$main = preg_replace("/(^https?:\/\/)[^\.]*(.*)/", "\\1$projectName\\2", $main);
	}
	
	public function __call( $action, $arguments=array() ) {
		
	}
}
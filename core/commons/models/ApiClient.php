<?php
/**
 * 内部调用接口客户端模型
 * @author yagasx
 * @version 0.1.2
 * @package CModel
 * 
 * @modify 2015/09/15 by yagas@zeeeda.com
 * @comment
 * +构造函数增加控制器的定义及是否为安全请求
 * +添加方法createUrl，对请求提供缓存支持
 * +fetchUrl方法增加是否为POST请求的控制
 */
class ApiClient extends CModel {
	private $_server;
	private $_data = array();
	private $_controller = 'service';
	private $_scheme;
	private $_project;
	private $_securty = false;

	/**
	 * 返回对象属性
	 * @see CModel::attributeNames()
	 */
	public function attributeNames() {
		$propertys = array();
		$ref = new ReflectionClass( $this );
		$result = $ref->getProperties();
		foreach ( $result as $item ) {
			$key = $item->name;
			$propertys[ $key ] = $this->$key;
		}
		return $propertys;
	}

	public static function & model( $projectName, $controller='service', $securty=null ) {
		$inc = new ApiClient( $projectName,$controller,$securty );
		return $inc;
	}
	
	/**
	* 取得域名
	*
	*/
	public function getDomain(){
		return $this->_server;
	}

	/**
	 * 构造请求接口路径
	 * @param string  $projectName 项目二级域名前缀
	 * @param string  $controller  讲求的控制器，默认值：service
	 * @param boolean $securty     是否为安全请求，默认值为null(自动判断)
	 */
	public function __construct( $projectName, $controller='service', $securty=null ) {
		
		$securty = false; //暂时屏蔽ssl @2016-02-26
		
		//解析URL路径
		$main              = parse_url( Yii::app()->request->hostInfo );
		
		//请求协议
		$this->_scheme     = $main['scheme'];

		//是否为安全讲求
		if( is_null($securty) ) {
			if( $this->_scheme === 'https' ) {
				$this->_securty = true;
			}
			
		}
		else {
			$this->_securty = $securty;
			$this->_scheme  = $securty? 'https' : 'http';
		}
		
		//主域名
		$domain            = substr( $main['host'], strpos($main['host'],'.') );
		
		//服务请求地址
		$this->_server     = $this->_scheme . '://'. $projectName . $domain;
		
		//服务请求控制器
		$this->_controller = $controller;
		
		//请求项目二级域名前缀
		$this->_project    = $projectName;
	}

	/**
	 * 调用远程方法
	 * @see CComponent::__call()
	 */
	public function __call( $action, $arguments ) {
		$arguments = array( "params"=>$arguments );
		$url    = $this->_server . '/'.$this->_controller.'/' . $action;
		$result = $this->fetchUrl( $url, $arguments );
		return $result;
	}

	/**
	 * 向接口请求数据
	 * @param string $url        请求地址
	 * @param array  $arguments  请求参数
	 * @param bool   $isPost     是否为POST请求
	 * @return string
	 */
	public function fetchUrl( $url, $arguments, $isPost=true ) {		
		if( $this->checkUrlStatus( $url ) ) {
			try{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_USERAGENT, Yii::app()->request->userAgent);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1); //链接超时时间
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_COOKIE, $this->cookies() );
				
				//是否为post请求
				if( $isPost ) {
					$arguments['sign'] = ApiServer::signKey( $arguments );
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($arguments,'','&'));
				}

				//是否为安全请求
				if( $this->_scheme=='https' ) {
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
				}

				$content = curl_exec( $ch );
				if( curl_errno($ch) !== 0 ) {
					throw new CException( curl_error($ch) );
				}
				curl_close($ch);
				return $content;
			}
			catch(Exception $error) {
				Yii::log( $error->getMessage(), CLogger::LEVEL_ERROR, __CLASS__);
				return "";
			}
		}
		return "";
	}

	/**
	 * 获取当前访问者的cookie
	 * @return string
	 */
	private function cookies() {
		$cookieStr = "";
		$cookies   = Yii::app()->request->cookies;
		foreach ( $cookies as $item ) {
			$cookieStr .= "; {$item->name}={$item->value}";
		}
		return substr( $cookieStr, 2 );
	}

	/**
	 * 检查链接是否可以访问
	 * @param string $url
	 * @return boolean
	 * @version 0.1.1
	 * @modify 2015/08/30 yagas@zeeeda.com
	 */
	private function checkUrlStatus( $url ) {
		$parse = parse_url( $url );
		$ch    = curl_init( $url );
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		
		if( $parse['scheme'] == 'https' ) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
		}
		$result = curl_exec($ch);
		if( curl_errno($ch) === 0 ) {
			$status = true;
		}
		else {
			$status = false;
		}
		curl_close($ch);
		return $status;
	}
	
	/**
	 * 跨项目生成URL地址
	 * @param string $route
	 * @param array $params
	 * @return string
	 */
	public function createUrl( $route, $params=array() ) {
		//缓存键名
		$routeKey = md5($this->_project.':'.$route.http_build_query($params));
		
		//生成缓存
		if( !Yii::app()->cache->offsetExists($routeKey) ) {
			$arguments = array("route"=>$route, "params"=>$params );
			$url       = $this->_server . '/'.$this->_controller.'/' . 'createUrl';
			$result    = $this->fetchUrl( $url, $arguments );
			if( substr($result, 0, 5)==='https' ) {
				$path = substr( $result, 6 );
			}
			else {
				$path = substr( $result, 5 );
			}
			Yii::app()->cache->set( $routeKey, $path, 300 );
			goto show;
		}
		
		$path = Yii::app()->cache->get( $routeKey );
		
		//返回URL路由地址
		show:
		return $this->_scheme . ':' . $path;
	}
}
<?php
/**
 * 继承CController实现权限触发式校验
 * @author yagasx
 * @package CController
 * @version 0.1
 *
 */
class Controller extends AdminController {

	private   $_errors = array();
	protected $fields  = array();
	private $routeInfo;
	public $breadcrumb;

	/**
	 * 菜单组编号
	 * @var interge
	 */
	public $index = 0;

	/**
	 * 执行动作之前触发beforeAction方法
	 * 我们在此对操作是否有权限进行校验
	 * @see CController::beforeAction()
	 * @return boolean
	 */
	public function beforeAction( $action ) {
		$this->routeInfo = tbSysmenu::model()->find('route=:r', [':r'=>'/'.$this->getRoute()]);

		$userRoles = Yii::app()->user->getState( "roles" );
		$access    = Yii::app()->user->checkAccess( $this->getRoute(), array('roles'=> $userRoles) );

		if( !$access ) {
			//返回ajax格式无权限提示信息
			if( Yii::app()->request->isAjaxRequest ) {
				$msg = new AjaxData( false, Yii::t('system','not has permission') );
				echo $msg->toJson();
				Yii::app()->end();
			}else{
				$this->forward( "/notice/permission" );
			}
			return false;
		}

		if($this->routeInfo) {
			$this->setPageTitle($this->routeInfo->title);
		}

		//写入访问日志
		$this->writeOpLog();
		return true;
	}

	/**
	 * 设置错误信息
	 * @param array $errors
	 */
	public function setError( $errors ) {
		$this->_errors = $errors;
	}

	/**
	 * 获取所有错误信息
	 * @return array
	 */
	public function getErrors() {
		return $this->_errors;
	}

	/**
	 * 是否存在错误信息
	 * @return boolean
	 */
	public function hasError() {
		return ( $this->_errors )? true : false;
	}

	/**
	 * 获取第一个错误信息
	 * @return string
	 */
	public function getError() {
		if( $this->_errors ) {
			$result = array_shift( $this->_errors );
			if( is_array($result) ) {
				$result = array_shift( $result );
			}
			return $result;
		}
		return null;
	}

	/**
	 * 按字段名称提取错误信息
	 * @param string $fiedName
	 * @return string
	 */
	public function showError( $fiedName ) {
		if( array_key_exists( $fiedName, $this->_errors ) ) {
			$error = $this->_errors[ $fiedName ];
			echo array_shift( $error );
		}
		return null;
	}

	/**
	 * 表单字段值
	 * @param string $filedName
	 * @return string
	 */
	public function val( $filedName ) {
		if( isset( $this->fields[$filedName] ) ) {
			echo $this->fields[$filedName];
			return true;
		}
	}

	/**
	* post结果处理 success
	* @param $url 跳转到的地址
	*/
	public function dealSuccess( $url=null ){
		Yii::app()->session['alertSuccess'] = '1';
		if( Yii::app()->request->getIsAjaxRequest() ) {
			$json=new AjaxData(true, null, $url);
			echo $json->toJson();
			Yii::app()->end();
		} else {
			if( $url ){
				$this->redirect($url);
			}

		}
	}

	/**
	* post结果处理 Error
	* @param $errors 错误信息
	*/
	public function dealError( $errors ){
		if( Yii::app()->request->getIsAjaxRequest() ) {
			$message = current($errors);
			if( is_array($message) ) $message =current($message);

			$json=new AjaxData(false, $message, $errors);

			echo $json->toJson();
			Yii::app()->end(200);
		}else{
			$this->setError( $errors );
		}
	}

	/**
	 * 直接错误信息
	 * @param string $lang 语言包
	 * @param string $msg 错误信息
	 */
	public function dealMessage($lang='msg',$msg=null){
		if( Yii::app()->request->getIsAjaxRequest() ) {
			$json=new AjaxData(false);
			$json->setMessage( $lang, $msg );
			echo $json->toJson();
			Yii::app()->end(200);
		}else{
			throw new CHttpException(500,Yii::t( $lang, $msg ));
		}
	}

	/**
	 * 构建图片服务器URL地址
	 * @param string $output
	 * @return null|string
	 * @etail $output true:直接输出(无返回) false:返回字符串
	 */
	public function img( $output=true ) {
		$domain  = strstr( Yii::app()->params['domain_images'], '.', true );
		$domain .= strstr( Yii::app()->request->hostInfo, '.' );
		if( $output ) {
			echo $domain;
		}
		else {
			return $domain;
		}
	}

	/**
	 * 返回图片URL地址
	 * @param string $url
	 * @param int $scale
	 * @return string
	 */
	public function showImage($url, $scale=null) {
		$domain  = strstr( Yii::app()->params['domain_images'], '.', true );
		$domain .= strstr( Yii::app()->request->hostInfo, '.' );
		$imageUrl = $domain.$url;
		if(!is_null($scale)) {
			$imageUrl .= '_'.$scale;
		}
		return $imageUrl;
	}

	/**
	 * 角色权限认证
	 * @param string $route 路由地址
	 */
	public final function checkAccess( $route ) {
		$userRoles = Yii::app()->user->getState( "roles" );
		return Yii::app()->user->checkAccess( $route, array('roles'=> $userRoles) );
	}

	/**
	 * 获取当前页面路由ID
	 * @return null|string
	 */
	public function getRouteId() {
		if($this->routeInfo) {
			$id = strval($this->routeInfo->id);
		}
		else {
			return 0;
			Yii::log("not sets route:".$this->getRoute(), CLogger::LEVEL_WARNING, "getRouteId");
			$this->forward("/notice/Permission");
		}
		return $id;
	}
}

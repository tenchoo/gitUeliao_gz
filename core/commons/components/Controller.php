<?php
/**
 * 会员中心控制器基础类库
 * @author yagas
 * @package CController
 * @subpackage Controller
 */
abstract class Controller extends CController {
	/**
	 * @var string 会员中主布局文件路径 '//layouts/main',
	 * 具体查看 '/theme/views/layouts/main.php'.
	 */
	public $layout='libs.commons.views.layouts.member';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

	public $routeFlag;

	public $menuGroup='会员中心';

	public $pageTitle;

	private static $_resUrl;

	public $homeUrl;
	
	public $memberUrl;


	/**
	 * 初始化控制器
	 * @see CController::init()
	 */
	public function init() {
		parent::init();

		//Yii行为绑定，将FormError的方法绑定为本控制器的方法
		$this->attachBehavior('error', 'libs.commons.behaviors.FormError');
		$this->pageTitle = Yii::app()->params['seo']['pageTitle'];

		$this->homeUrl	 = 'http://www'.DOMAIN;
		$this->memberUrl = 'http://member'.DOMAIN;
	}

	/**
	 * 开启访问控制器
	 * @see CController::filters()
	 */
	public function filters() {
		return array('accessControl');
	}

	/**
	 * 设置访问权限
	 * @see CController::accessRules()
	 */
	public function accessRules() {
		return array(
			array('deny','users'=>array('?')),
		);
	}

	/**
	 * 输出前端资源库域名
	 * 自动适应当前浏览器所使用的网络请求协议(HTTP或HTTPS)
	 */
	public function res() {
		if( is_null(self::$_resUrl) ) {
			$domains = parse_url( Yii::app()->params['domain_res'] );
			if( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ) {
				$resUrl = "https://".$domains['host'];
			}
			else {
				$resUrl = "http://".$domains['host'];
			}
			self::$_resUrl = $resUrl;
		}
		else {
			echo self::$_resUrl;
		}
	}

	/**
	 * 输出前端网页keywords
	 */
	public function pageKeywords() {
		echo Yii::app()->params['seo']['keywords'];;
	}

	/**
	 * 输出前端网页description
	 */
	public function pageDescription() {
		echo Yii::app()->params['seo']['description'];;
	}

	/**
	 * 输出图片服务器域名
	 */
	public function img( $type=true ) {
		if($type==true){
			echo Yii::app()->params['domain_images'];
		}
		else{
			return  Yii::app()->params['domain_images'];
		}
	}

	/**
	 * 输出图片路径
	 * @param string $suffixUrl
	 * @param bool $output
	 * @return string
	 */
	public function imageUrl( $suffixUrl, $scale=null, $output=true ) {
		$source = Yii::app()->params['domain_images'] . $suffixUrl;
		if( !is_null($scale) ) {
			$source .= '_' . $scale;
		}

		if( preg_match('/^https/',Yii::app()->request->hostInfo) ) {
			$source = str_replace('http://','https://',$source);
		}

		if( $output ) {
			echo $source;
		}
		else {
			return $source;
		}
	}

	/**
	 * 输出资源路径
	 * @param unknown $suffixUrl
	 * @param string $output
	 * @return string
	 */
	public function resUrl( $suffixUrl, $output=true ) {
		$source = Yii::app()->params['domain_res'] . $suffixUrl;
		if( preg_match('/^https/',Yii::app()->request->hostInfo) ) {
			$source = str_replace('http://','https://',$source);
		}

		if( $output ) {
			echo $source;
		}
		else {
			return $source;
		}
	}

	/**
	 * 触发器:执行前端资源提取优化
	 * @see CController::afterRender()
	 */
	public function afterRender($view, &$output) {
		$op = new sourceOptimize();
		$op->prefix = Yii::app()->params['domains']['res'];
		$op->context = $output;
// 		$op->dict = 'application.data.resmap';
		$op->run();
		$output = $op->context;
	}

	/**
	* post结果处理 success
	* @param $url 跳转到的地址
	*/
	public function dealSuccess($url=null){
		if( Yii::app()->request->getIsAjaxRequest() ) {
			$json=new AjaxData(true,null,$url);
			echo $json->toJson();
			Yii::app()->end(200);
		} else {
			if($url)
				$this->redirect($url);
		}
	}

	/**
	* post结果处理 Error
	* @param $errors 错误信息
	*/
	public function dealError($errors){
		if( Yii::app()->request->getIsAjaxRequest() ) {
			$message = current($errors);
			if( is_array($message) ) $message =current($message);
			$json=new AjaxData(false,$message,$errors);
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
		$json=new AjaxData(false);
		$json->setMessage( $lang, $msg );
		echo $json->toJson();
		Yii::app()->end(200);
	}

	/**
	 * 返回当前项目域名路径
	 * @param string $prefix
	 */
	public function siteUrl( $prefix=null ) {
		if( !is_null( $prefix ) ) {
			return preg_replace("/^(https?:\/\/)([^\.]*)(.*)/","\\1${prefix}\\3", Yii::app()->request->hostInfo);
		}
		return Yii::app()->request->hostInfo;
	}
	
	public function behaviors() {
		 return array_merge(parent::behaviors(),array(
				'oplog'=>'libs.commons.behaviors.OpLogBehavior',
        ));
	}

	protected function beforeAction( $action ){
		$this->writeOpLog();
		return parent::beforeAction( $action );;
	}
}
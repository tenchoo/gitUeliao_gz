<?php
/**
 * 错误显示基础类
 * @author liang
 * @package CController
 * @subpackage Controller
 */
abstract class EController extends CController {
	/**
	 * @var string 会员中主布局文件路径 '//layouts/main',
	 * 具体查看 '/theme/views/layouts/main.php'.
	 */
	public $layout='libs.commons.views.layouts.main';
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

	public $menuGroup='错误提示';

	public $memberUrl;

	public $pageTitle;

	private static $_resUrl;

	public $homeUrl;


	/**
	 * 初始化控制器
	 * @see CController::init()
	 */
	public function init() {
		parent::init();
		
		$this->homeUrl	 = 'http://www'.DOMAIN;
		$this->memberUrl = 'http://member'.DOMAIN;

		$this->pageTitle = Yii::app()->params['seo']['pageTitle'];
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
		$op->run();
		$output = $op->context;
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
}
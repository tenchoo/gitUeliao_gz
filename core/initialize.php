<?php

/**
 * 目录间隔符  */
define( 'DS', DIRECTORY_SEPARATOR );

/** 
 * Yii调试模式  */
defined('YII_DEBUG') or define('YII_DEBUG',false);
if( isset($_GET['devel']) && $_GET['devel']==='xdebug' ) {
	define('XDEBUG',True);
}
else {
	define('XDEBUG',False);
}

/**
 * Yii跟踪代码深度 */
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',5);

/**
 * 加密掩码字符串，主要用于加密/解密字符  */
defined('SECURITY_MASK') or define('SECURITY_MASK', 'c8ecb26d30e6b39aa032b2355c9ca5b3');

/**
 * 是否启用安全请求校验 */
defined('SECURITY_ENABLE') or define('SECURITY_ENABLE', true);

if( YII_DEBUG || XDEBUG ) {
	/**
	 * 开发模式下显示所有的错误信息  */
	error_reporting( E_ALL );
	ini_set('display_errors','On');
	ini_set('display_startup_errors','On');
	ini_set('track_errors','On');
}
else {
	/** 
	 * 生产环境下不将错误信息进行显示  */
	error_reporting( 0 );
	ini_set('display_errors', 'Off');
	ini_set('display_startup_errors','Off');
	ini_set('track_errors','Off');
}

/** 
 * 加载Yii框架 */
if(YII_DEBUG || XDEBUG) {
	require_once 'Yii/yii.php';
}
else {
	require_once 'Yii/yiilite.php';
}


/** 
 * 设置开发库别名 */
Yii::setPathOfAlias('libs', dirname(__FILE__));
Yii::setPathOfAlias('widgets', dirname(__FILE__).DS.'commons'.DS.'widgets');
Yii::setPathOfAlias('vendors', dirname(__FILE__).DS.'vendors');

/**
 * 网站默认域名  */
if( !defined('DOMAIN') ) {
	define('DOMAIN',strstr($_SERVER['HTTP_HOST'],'.'));
}

/** 浮点运算保留小数精度 */
bcscale(2);

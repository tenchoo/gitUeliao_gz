<?php
/**
 * 应用程序环境，可选：development,main,
 */
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Cred:true");
header("Access-Control-Allow-Methods:GET,POST,OPTIONS,HEAD,PUT");

if( $_SERVER['REQUEST_METHOD'] === 'OPTIONS' ) {
	header("HTTP/1.1 204 No Content");
	exit;
}


/**
 * 项目设置文件路径  */
$config=dirname(__FILE__).'/protected/config/main.php';

/**
 * 开启调试模式
 * 在生产环境中请将设置删除
 */
if(file_exists('./.debug')) {
	define('YII_DEBUG',true);
}
else {
	define('YII_DEBUG',false);
}



/**
 * 加载开发框架  */
$library = getenv('ZYD_LIB')? getenv('ZYD_LIB') : dirname(__FILE__).'/../core';
require_once( $library . '/initialize.php' );

Yii::createWebApplication( $config )->run();

<?php
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Credentials:true");
header("Access-Control-Allow-Methods:GET,POST,PUT,DELETE,OPTIONS,HEAD");
header("Access-Control-Allow-Headers:ACCEPT,CONTENT-TYPE");
header("Content-Type:application/json; charset=UTF-8");

if( $_SERVER['REQUEST_METHOD'] === 'OPTIONS' ) {
	header("HTTP/1.1 204 No Content");
	exit;
}

/** develop config **/
if(is_file('./.debug')) {
	define('YII_DEBUG', true);
}
//关闭xdebug调试模式
ini_set('xdebug.default_enable', 0);
/** develop config **/

$libs = dirname(__FILE__).'/../core';
//设置时区
date_default_timezone_set('PRC');

require $libs.'/initialize.php';
Yii::createWebApplication( "application/config/main.php" )->run();

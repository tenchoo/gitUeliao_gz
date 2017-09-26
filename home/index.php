<?php
if(is_file('/.debug')) {
	define('YII_DEBUG', true);
}

$libs = getenv('LIBS');
if( $libs === false ) {
	$libs = dirname(__FILE__).'/../core';
}

if( !preg_match('/^www\./', $_SERVER['HTTP_HOST']) ) {
	header('Location:http://www.'.$_SERVER['HTTP_HOST']);
	exit;
}

require $libs.'/initialize.php';
Yii::createWebApplication( "application/config/main.php" )->run();
<?php
if(is_file('/.debug') && !defined('YII_DEBUG')) {
	define('YII_DEBUG', true);
}

$libs = dirname(__FILE__).'/../core';
require $libs.'/initialize.php';
Yii::createWebApplication( "application/config/main.php" )->run();
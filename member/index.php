<?php
/**
 * 会员中心项目入口
 */

/**
 * 项目设置文件路径  */
$config = dirname(__FILE__).'/protected/config/main.php';

/**
 * 开启调试模式
 * 在生产环境中请将设置删除
 */
if(is_file('/.debug')) {
	define('YII_DEBUG', true);
	ini_set('xdebug.profiler_enable', 1);
}

/**
 * 加载开发框架  */
$libs = dirname(__FILE__).'/../core';
require $libs.'/initialize.php';

Yii::createWebApplication( $config )->run();

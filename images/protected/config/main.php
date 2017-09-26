<?php
$global = require(Yii::getPathOfAlias('libs').'/config.php');
unset( $global['components']['urlManager']['rules']);

return CMap::mergeArray(
	$global,
	array(

	'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',//当前应用根目录的绝对物理路径
	'name'     => '资源中心',//当前应用的名称
    'homeUrl' => 'http://images.yagas.leather168.com',//网站地址
    'language' => 'zh_cn',//语言包
    'defaultController'=>'restful', //设置默认控制器类
	'modules'=>array(
	),

	// 当前应用的组件配置。更多可供配置的组件详见下面的"核心应用组件"

	'components'=>array(
		//URL路由管理器
		'urlManager'=>require(dirname(__FILE__) . '/urlreles.php'),
		'errorHandler'=>array(
			'errorAction'=>'restful/error',
		),

	),

	'params'=>require(dirname(__FILE__) . '/params.php'),

)
);

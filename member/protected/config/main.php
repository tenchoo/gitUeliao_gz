<?php
$public = require (Yii::getPathOfAlias ( 'libs' ) . '/config.php');

return CMap::mergeArray ( $public, array (
		'basePath' => dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..',
		'name' => 'member center',
		'theme' => 'classic',
		'defaultController' => 'site',
		'language' => 'zh_cn',

		'import' => array (
				'libs.commons.components.*',
				'libs.commons.models.*',
				'application.widgets.*',
				'application.models.*',
				'application.components.*'
		),

		'modules' => array (
				'cart',
				'order',
				'ajax',
				'factory','applyprice'
		),

		'components' => array (
				'urlManager' => array (
						'rules' => array (
								'index' => 'site/index',
								'<action:(login|logout)>' => 'user/<action>',
								'product/<action:(publish)>/setp<setp:\d>' => 'product/default/<action>',
								'product/<action:(publish)>' => 'product/default/<action>',
								'<controller:\w+>/<id:\d+>' => '<controller>/view',
								'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
								'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
								'<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>'
						)
				),
		),

		'params' => array (
				/**
				 * 前端样式库地址
				 */
				'domain_res' => 'https://res'.DOMAIN,
				'domain_images' => 'http://images'.DOMAIN,
				'domain_manage' => 'https://manage'.DOMAIN,
				'captha_length' => 4, // 验证码显示长度
				'captha_font' => 'artistico.ttf', // 验证码字体
				'SLD_member'=>'member', //second-level domain  二级域名
		)
) );


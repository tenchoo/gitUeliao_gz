<?php
$global = require (Yii::getPathOfAlias ( 'libs' ) . '/config.php');

return CMap::mergeArray ( $global,
		array (
		'basePath' => dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..',
		'name' => 'manage',
		'theme' => 'classic',
		'defaultController' => 'default',
		'language' => 'zh_cn',
		'homeUrl' => 'http://manage'.DOMAIN,

		'import' => array (
				'application.widgets.*',
				'application.models.*',
				'application.components.*'
		),

		'modules' => array (
			'category',
			'product',
			'member',
			'ajax',
			'order',
			'role',
			'content',
			'purchase',
			'warehouse',
			'interface',
			'api',
			'factory',
			'inquiry',
			'push','tailgoods',
			'statistic','finance','gou'
		),

		'components' => array (
				'user'=>array(
					'allowAutoLogin'=>true,
					'loginUrl'=>'/sign/login',
					'stateKeyPrefix' => 'manage',
				),

				'urlManager' => array (
					'rules' => array (
						'index' => 'default/index',
						'warehouse/api/<do:\w+>' => 'warehouse/api',
						'api/<do:\w+>' => 'api/default',
						'<action:(login|logout)>' => 'sign/<action>',
						'<module:\w+>/<controller:\w+>/<action:(view|edit|delete)>/<id:\d+>' => '<module>/<controller>/<action>',
						'<controller:\w+>/<action:\w+>' => '<controller>/<action>'
					)
				),

				'authManager' => array(
					'class' => 'AuthValidate'
				),
		),

		'params' => array (
				/**
				 * 前端样式库地址
				 */
				'domain_res' => 'http://res'.DOMAIN,
				'domain_images' => 'http://images'.DOMAIN,
				'domain_manage' => 'http://manage'.DOMAIN,
				'captha_length' => 4, // 验证码显示长度
				'captha_font' => 'artistico.ttf',

				/** 允许访问前端的角色ID */
				'front_roles' => array(3),
				'SLD_member'=>'member', //second-level domain  二级域名
		)
) ); // 验证码字体

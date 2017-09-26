<?php
$global = require (Yii::getPathOfAlias ( 'libs' ) . '/config.php');

return CMap::mergeArray ( $global,
		array (
		'basePath' => dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..',
		'name' => 'manage',
		'theme' => 'classic',
		'defaultController' => 'default',
		'language' => 'zh_cn',
		'homeUrl' => 'http://www'.DOMAIN,

		'import' => array (
				'application.widgets.*',
				'application.models.*',
				'application.components.*'
		),
		'modules' => array (
			'ajax',
		),

		'components' => array (
			'urlManager' => array (
				'rules' => array (
					'/' => 'default/product',
					'index' => 'default/product',
					'<controller:\w+>/detail-<id:\d+>' => '<controller>/detail',
					'<controller:\w+>/<action:\w+>' => '<controller>/<action>'
				)
			),
			'sphinx' => $global['params']['sphinx'],
		),

		'params' => array (
				/**
				 * 前端样式库地址
				 */
				'domain_res' => 'http://res'.DOMAIN,
				'domain_images' => 'http://images'.DOMAIN,
				'domain_mobile'=>'http://m'.DOMAIN, // 手机端域名

				//默认产品分类
				'default_category_id' => 2,

				//产品列表每页产品数量
				'default_page_size' => 20,
				'SLD_member'=>'member', //second-level domain  二级域名
				'upload_dir' => '/tmp/upload/',
		)
) );


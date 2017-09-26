<?php
return CMap::mergeArray ( require (Yii::getPathOfAlias ( 'libs' ) . '/config.php'),
		array(
		'basePath' => dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . '..',
		'name'  => 'restfulapi',
		'theme' => null,
		'defaultController' => 'default',
		'language' => 'zh_cn',
		'homeUrl'  => 'http://api'.DOMAIN,

		'import' => array(
				'application.components.*',
				'application.models.*',
		),
		
		'modules' => array (
			'v1',
			'v2',
			'v2_1'=> array('class'=>'application.modules.v2_1.MyModule'),
			'warehouse01'
		),

		'components' => array(
			'urlManager' => array(
				'urlFormat' => 'path',
				'showScriptName' => false,
				'rules'  => array(
					array('<module>/SellerPwd/update', 'pattern'=>'<module:\w+>/seller/password', 'verb'=>'PUT'),
					array('<module>/SellerSales/index', 'pattern'=>'<module:\w+>/seller/sales', 'verb'=>'GET'),

					array('<controller>/index', 'pattern' => '<controller:\w+>', 'verb' => 'GET'),
					array('<controller>/show', 'pattern' => '<controller:\w+>/<id:\d+>', 'verb' => 'GET'),
					array('<controller>/create', 'pattern' => '<controller:\w+>', 'verb' => 'POST'),
					array('<controller>/update', 'pattern' => '<controller:\w+>/<id:\d+>', 'verb' => 'PUT'),
					array('<controller>/update', 'pattern' => '<controller:\w+>', 'verb' => 'PUT'),
					array('<controller>/delete', 'pattern' => '<controller:\w+>/<id:\d+>', 'verb' => 'DELETE'),
					array('<controller>/show', 'pattern' => '<controller:\w+>/<id:\d+>/<event:\w+>', 'verb' => 'GET'),
					array('<controller>/show', 'pattern' => '<controller:\w+>/<id:\d+>', 'verb' => 'GET'),
					array('order/logistics', 'pattern' => 'order/logistics/<id:\d+>', 'verb' => 'GET'),

					array('<module>/<controller>/index', 'pattern' => '<module:\w+>/<controller:\w+>', 'verb' => 'GET'),
					array('<module>/<controller>/create', 'pattern' => '<module:\w+>/<controller:\w+>', 'verb' => 'POST'),
					array('<module>/<controller>/show', 'pattern' => '<module:\w+>/<controller:\w+>/<id:\d+>', 'verb' => 'GET'),
					array('<module>/<controller>/update', 'pattern' => '<module:\w+>/<controller:\w+>', 'verb' => 'PUT'),
					array('<module>/<controller>/show', 'pattern' => '<module:\w+>/<controller:\w+>/<id:\d+>/<event:\w+>', 'verb' => 'GET'),
					array('<module>/<controller>/delete', 'pattern' => '<module:\w+>/<controller:\w+>/<id:\d+>', 'verb' => 'DELETE'),
					array('<module>/<controller>/update', 'pattern' => '<module:\w+>/<controller:\w+>/<id:\d+>', 'verb' => 'PUT'),
					array('<module>/order/logistics', 'pattern' => '<module:\w+>/order/logistics/<id:\d+>', 'verb' => 'GET'),


				//	array('productinquiry/create', 'pattern' => 'productinquiry/<id:\d+>', 'verb' => 'POST'),
				),
			),

			'errorHandler'=>array(
				'errorAction'=>'ajax/error',
			),

//			//会员登陆信息
//			'openid' => array(
//				'class'         => 'libs.commons.components.CMongoDB',
//				'connectString' => 'mongodb://mongodb.localhost:27017/leather',
//				'dbname'        => 'leather',
//				'collection'    => 'openid',
//				'username'      => 'develop',
//				'password'      => 'develop',
//				'auth' => true
//			),

			'openidCache' => array(
					'class' => 'libs.commons.components.MongoCache',
					'CacheId' => 'mongoDB',
					'collection' => 'openid'
			),
		),

		'params' => array(
				// 前端样式库地址
				'domain_images' => 'http://images'.DOMAIN,

				//产品列表每页产品数量
				'default_page_size' => 6,

				//生成接口请求token盐值
				'securtyCode' => 'abcdefg',

				//token的有效时间为2小时(2*60*60)
				'tokenExpire' => '7200',

				//mongodb仓库设置
				'mongoConf' => ['mongodb://mongodb.localhost:27017', 'restfulapi'],

				'no_need_login' => ['login','user','ajax','captcha','category','token','product','password','wxpay','version','device','seller','area'],

				//声音文件存储服务器
				'mongoVoice' => [
					'host'       => 'mongodb://mongodb.localhost:27017',
					'dbname'     => 'source',
					'collection' => 'voice'
				],
		)
) );

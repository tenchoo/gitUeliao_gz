<?php
//外部设置文件路径
$settings = __DIR__.DS.'..'.DS.'config.php';

/**
 * 默认设置参数
 * 可以通过加载外部config.php文件的方式进行替换
 */
$config = [
	//数据库
	'db' => array (
		'connectionString' => 'mysql:host=mysql.localhost;dbname=new_leather', // 主数据库 写
		'emulatePrepare'   => true,
		'username'         => 'leather168',
		'password'         => 'admin888',
		'charset'          => 'utf8',
		'tablePrefix'      => 'db_'
	),

	'newsphinx'=> 'http://www'.DOMAIN.':8130/sphinx/?',

	//全文索引
	'sphinx' => array(
		'class' =>'ZSphinxSearcher',
		'host'=>'120.24.68.214',
		'port'=>'9312'
	),

	//图片搜索服务
	// 'opencv' => array("192.168.1.230", 12306),
	'opencv' => array("127.0.0.1", 12306),

	//全局缓存
	'mongoDB' => array(
		'class' => 'libs.commons.components.CMongoDB',
		'connectionString' => 'mongodb://develop:develop@mongodb.localhost:27017/leather',
		'dbname' => 'leather',
		'collection' => 'global',
	),

	//日志设置
	'logger' => [
		array(
			'class'=>'CWebLogRoute',
			'levels'=>'error,warning'
		),

		array(
			'class'=>'CFileLogRoute',
			'levels'=>'info',
			'categories'=>'wxpay.*',
			'logFile'=> 'wxpay.log',
		),

		array(
			'class'=>'CFileLogRoute',
			'levels'=>'info',
			'filter'=>'CLogFilter',
		)
	]
];

//加载外部设置信息
if(file_exists($settings)) {
	$settingsValue = include $settings;
	foreach($settingsValue as $k => $v) {
		$config[$k] = $v;
	}
}

//全局设置文件
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Web Application',

	'preload'=>array('log'),

	'import'=>array(
// 		'libs.vendors.payment.*',
		'libs.commons.tables.*',
		'libs.commons.models.*',
		'libs.commons.components.*',
		'application.models.*',
		'application.components.*',
	),

	'components'=>array(
		'user'=>array(
			'loginUrl'=>array('user/login'),
			'stateKeyPrefix' => 'base',
		),

		'db' => $config['db'],

		'request' => array(
			'class' => 'ZHttpRequest',
		),

		'urlManager'=>array(
			'urlFormat' => 'path',
			'showScriptName' => false,
			'urlSuffix' => '.html',
		),

		'mongoDB' => $config['mongoDB'],

		'cache' => array(
			'class' => 'libs.commons.components.MongoCache',
			'CacheId' => 'mongoDB',
			'collection' => 'cache'
		),

		'session' => array(
			'class' => 'ZHttpSession',
			'CacheId' => 'mongoDB',
			'collection' => 'session',
			'custom' => true,
		),

		'cart'=>array(
			'class'=>'application.extensions.Cart',
		),

		'errorHandler'=>array(
			'errorAction'=>'notice/error',
		),

		'mailer'=>array(
			'class' => 'libs.vendors.mailer.EMailer',
		),

		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>$config['logger'],
		),

	),

	'params'=>array(
		'newsphinx' => $config['newsphinx'],
		'adminEmail'=>'webmaster@example.com',
		'mailType'=>'smtp',//可设置为smtp或phpmail
		'mailconfig'=>array(
					'Host'=>'smtp.exmail.qq.com',
					'Port'=>25,
					'SMTPAuth'=>false,
					'From'=>'devel@zeeeda.com',
					'FromName'=>'指易达电商平台',
					'Username'=>'devel@zeeeda.com',
					'SMTPSecure'=>'',
					'Password'=>'admin746122',
					'CharSet'=>'UTF-8',
					'ContentType'=>'text/html',
		),
		'mobconfig'=>array(
					'Host'=>'http://106.ihuyi.cn/webservice/sms.php?method=Submit',
					'Port'=>'80',
					'username'=>'cf_kaiyipg',
					'password'=>'zhang123456',
					'md5'=>false,
		),
		'mobilesms'=>'3',//手机接口商(1为商翼通,2为商脉,3互亿无线)
		'domains' => array(
			'res'    => 'http://res.leather168.com'
		),
		'sphinx' => $config['sphinx'],
		'seo'=>array(
			'pageTitle'=>'皮革商城',
			'keywords'=>'皮革',
			'description'=>'皮革电子商城，批发零售',
		),
		'opencv' => $config['opencv']
	),
);

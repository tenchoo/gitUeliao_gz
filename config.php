<?php
return array(
'mongoDB' => array(
		'class' => 'libs.commons.components.CMongoDB',
		'connectionString' => 'mongodb://develop:develop@120.76.166.81:27017/leather',
		'dbname' => 'leather',
		'collection' => 'global'
	),

//数据库
	'db' => array (
	//	'connectionString' => 'mysql:host=120.76.166.81;dbname=ueliao',
		'connectionString' => 'mysql:host=120.76.166.81;dbname=new_leather', // 主数据库 写
		'emulatePrepare'   => true,
		'username'         => 'leather168',
		'password'         => 'admin888',
		'charset'          => 'utf8',
		'tablePrefix'      => 'db_'
	),

//日志设置
	'logger' => [
		array(
			'class'=>'CFileLogRoute',
			'levels'=>'error,warning,info'
		),
	]
);

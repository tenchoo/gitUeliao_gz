<?php
/**
*  定时任务--连接数据库
*
*/
	set_time_limit( 0 );

	/* $conn = mysql_connect( "mysql.localhost","leather168","admin888" );
	if ( !$conn ) {
	  die( 'Could not connect: ' . mysql_error() );
	}

	mysql_select_db("ueliao", $conn);
	mysql_query("set names utf8"); */


	$settings = dirname( dirname(__DIR__) ).'/config.php';

	//加载外部设置信息
	if( !file_exists( $settings) ) {
		die( 'Could not find the config. ');
	}

	$settingsValue = include $settings;

	if( !array_key_exists('db',$settingsValue) ){
		die( 'the config is worng. ');
	}

	$db = explode(';',$settingsValue['db']['connectionString']);
	$host = explode('=',$db['0']);
	$dbName = explode('=',$db['1']);

	$host =   $host['1'];
	$dbName = $dbName['1'];
	$userName = $settingsValue['db']['username'];
	$password = $settingsValue['db']['password'];

	$conn = mysql_connect( $host,$userName,$password );
	if ( !$conn ) {
	  die( 'Could not connect: ' . mysql_error() );
	}

	mysql_select_db( $dbName, $conn );
	mysql_query("set names utf8");
?>
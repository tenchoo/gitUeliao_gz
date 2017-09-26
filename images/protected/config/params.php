<?php
/**
 * 配置说明
 * collection  string   MongoDB数据库集合名称
 * allow       integer  允许上传的文件后缀名称
 * thumb       integer  生成缩略图的规格
 */
return array(

	// 方法对应MongoDB数据集合名称
	'collection_map' => [
		'product' => [
			'collection'=>'product',
			'allow'=>['jpg','png','png'],
			'thumb'=>['50','80','100','160','200','600','800']
		],

		'face' => [
			'collection'=>'face',
			'allow'=>['jpg','png','png'],
			'thumb'=>['100','256']
		],

		'res' => [
			'collection'=>'res',
			'allow'=>['jpg','png','png']
		],

		'adv' => [
			'collection'=>'adv',
			'allow'=>['jpg','png','png','swf']
		],

		'news' => [
			'collection'=>'news',
			'allow'=>['jpg','png','png','swf']
		],

		'sound' => [
			'collection'=>'sound',
			'allow'=>['acc','amr']
		],

		//圈子头像
		'qicon' => [
			'collection'=>'qicon',
			'allow'=>['jpg','png','png'],
			'thumb'=>['100','256']
		],

		// 圈子声音
		'qsound' => [
			'collection'=>'qsound',
			'allow'=>['acc','amr']
		],
	],
);

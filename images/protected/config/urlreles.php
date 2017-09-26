<?php

//URL重写
return array(
	'urlFormat'=>'path',
	'showScriptName' => false,
	'urlSuffix'=>'',
	'rules'=>array(
		array('restful/create', 'pattern' => 'api/upfile', 'verb'=>'POST'),
		array('restful/index', 'pattern'=>'<resource:\w+>/<key:\w+>.amr', 'verb'=>'GET'),
		array('restful/index', 'pattern'=>'<resource:\w+>/<key:\w+>_<thumb:\d+>', 'verb'=>'GET'),
		array('restful/index', 'pattern'=>'<resource:\w+>/<key:\w+>', 'verb'=>'GET'),
	),
);

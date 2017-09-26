<!doctype html>
<html lang="zh-cn">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="renderer" content="webkit|ie-stand|ie-comp">
		<title><?php echo $this->pageTitle;?></title>
		<meta name="keywords" content="" />
		<meta name="description" content="" />
		<link rel="stylesheet" href="/libs/normalize/3.0.3/normalize.min.css" />
		<link rel="stylesheet" href="/modules/topbar/css/style.css"/>
		<link rel="stylesheet" href="/modules/foot/css/style.css"/>
		<link rel="stylesheet" href="/common/style.css" />
		<script src="/libs/seajs/2.3.0/sea.js"></script>
		<script src="/libs/seajs/2.3.0/seajs-css.js"></script>
		<script src="/libs/jquery/1.11.3/jquery.min.js"></script>
		<script src="/common/init.js"></script>
		
		<link rel="stylesheet" href="/modules/button/css/style.css" />
		<link rel="stylesheet" href="/modules/form/css/style.css" />
		<link rel="stylesheet" href="/app/member/frame/css/style.css" />
		<link rel="stylesheet" href="/app/help/css/style.css" />
	</head>
<body>
<?php $this->beginContent('libs.commons.views.layouts._topbar'); //顶部?>
<?php $this->endContent(); ?>
<?php $this->beginContent('/layouts/_helpnav'); //导航?>
<?php $this->endContent(); ?>

<div class="container frame-body">
<?php $this->beginContent('/layouts/_helpmenu'); //导航?>
<?php $this->endContent(); ?>
<?php echo $content; ?>
</div>
<?php $this->beginContent('libs.commons.views.layouts._footer'); //底部?>
<?php $this->endContent(); ?>
</body>
</html>	
<!doctype html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="renderer" content="webkit|ie-stand|ie-comp">
  <title><?php echo $this->pageTitle;?></title>
  <meta name="keywords" content=""/>
  <meta name="description" content=""/>
  <link rel="stylesheet" href="/libs/normalize/3.0.3/normalize.min.css"/>
  <link rel="stylesheet" href="/modules/topbar/css/style.css"/>
  <link rel="stylesheet" href="/common/style.css"/>
  <link rel="stylesheet" href="/modules/foot/css/style.css"/>
  <script src="/libs/seajs/2.3.0/sea.js"></script>
  <script src="/libs/seajs/2.3.0/seajs-css.js"></script>
  <script src="/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="/common/init.js"></script>
</head>
<body>
<?php $this->beginContent('libs.commons.views.layouts._topbar'); //顶部?>
<?php $this->endContent(); ?>
<?php echo $content; ?>
<?php $this->beginContent('libs.commons.views.layouts._footer'); //顶部?>
<?php $this->endContent(); ?>
</body>
</html>
<!doctype html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="renderer" content="webkit|ie-stand|ie-comp">
  <title><?php echo $this->pageTitle;?></title>
  <meta name="keywords" content="<?php $this->pageKeywords();?>"/>
  <meta name="description" content="<?php $this->pageDescription();?>"/>
  <link rel="stylesheet" href="/libs/normalize/3.0.3/normalize.min.css"/>
  <link rel="stylesheet" href="/common/style.css"/>
  <link rel="stylesheet" href="/modules/button/css/style.css"/>
  <link rel="stylesheet" href="/modules/icon/css/style.css"/>
  <link rel="stylesheet" href="/modules/form/css/style.css"/>
  <link rel="stylesheet" href="/modules/topbar/css/style.css"/>
  <link rel="stylesheet" href="/modules/foot/css/style.css"/>
  <link rel="stylesheet" href="/app/member/frame/css/style.css"/>
  <script src="/libs/seajs/2.3.0/sea.js"></script>
  <script src="/libs/seajs/2.3.0/seajs-css.js"></script>
  <script src="/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="/common/init.js"></script>
</head>
<body>
<?php $this->beginContent('libs.commons.views.layouts._topbar'); //顶部?>
<?php $this->endContent(); ?>
<div class="frame-head">
  <div class="container">
    <h1 class="pull-left"><a href="<?php echo $this->homeUrl;?>"><img src="/app/member/frame/image/logo.png" width="164" height="45" alt="优易料"/></a></h1>
    <?php $this->widget('widgets.userNavbar')?>
    <div class="pull-right frame-search">
      <form action="<?php echo $this->homeUrl.'/default/product';?>" method="get">
        <input type="text" placeholder="请输入产品名称" name="q"/>
        <button><span class="hide">搜索</span></button>
      </form>
    </div>
  </div>
</div>
<div class="container frame-body">
  <?php $this->widget("widgets.userMenubar",array('title'=>$this->menuGroup))?>
  <script>seajs.use('app/member/frame/js/menu.js');</script>
  <?php echo $content; ?>
</div>
<?php $this->beginContent('libs.commons.views.layouts._footer'); //底部?>
<?php $this->endContent(); ?>
</body>
</html>
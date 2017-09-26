<!doctype html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="renderer" content="webkit|ie-stand|ie-comp">
  <title>登录</title>
  <meta name="keywords" content=""/>
  <meta name="description" content=""/>
  <link rel="stylesheet" href="<?php $this->res();?>/libs/normalize/3.0.3/normalize.min.css"/>
  <link rel="stylesheet" href="<?php $this->res();?>/common/style.css"/>
  <script src="<?php $this->res();?>/libs/seajs/2.3.0/sea.js"></script>
  <script src="<?php $this->res();?>/libs/seajs/2.3.0/seajs-css.js"></script>
  <script src="<?php $this->res();?>/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="<?php $this->res();?>/common/init.js"></script>
</head>
<body>
  <link rel="stylesheet" href="<?php $this->res();?>/modules/button/css/style.css"/>
  <link rel="stylesheet" href="<?php $this->res();?>/modules/form/css/style.css"/>
  <link rel="stylesheet" href="<?php $this->res();?>/modules/login/css/style.css"/>
  <div class="login-wrap">
  <form action="<?php echo $this->createUrl('user/login');?>" method="post">
    <input type="hidden" name="done" value="<?php echo $done;?>">
    <div class="message-box"></div>
    <div class="form-group">
      <input type="text" class="form-control username no-success-help" autofocus name="LoginForm[username]" placeholder="手机号码" autocomplete="off">
    </div>
    <div class="form-group">
      <input type="password" class="form-control password no-success-help" placeholder="密码" name="LoginForm[password]">
    </div>
    <div class="clearfix form-group code-wrap">
      <input type="text" class="form-control code pull-left no-success-help" name="LoginForm[verifyCode]" placeholder="验证码">
      <img src="<?php echo Yii::app()->request->hostinfo;?>/ajax/default/index/action/captcha" width="80" height="30" alt="点击刷新验证码" class="refresh pull-left" title="点击刷新验证码"/>
      <span class="pull-left refresh">看不清换一张</span>
    </div>
    <button class="btn btn-sm btn-block btn-warning" type="submit" data-loading="正在登录…">登 录</button>
  </form>
  </div>
  <script>
    seajs.use('app/member/account/js/login.js');
  </script>
</body>
</html>

<link rel="stylesheet" href="/modules/button/css/style.css"/>
<link rel="stylesheet" href="/modules/form/css/style.css"/>
<link rel="stylesheet" href="/app/member/account/css/login.css"/>
<h1 class="container logo">
  <a href="<?php echo $this->homeUrl?>"><img src="/app/home/image/logo.png" width="213" height="58" alt="优易料"/></a>
</h1>
<div class="container">
  <div class="pull-left image" data-ad="/ajax?action=spread&mark=login_left"></div>
  <div class="pull-right login">
    <h2 class="login-title">登录</h2>
    <div class="login-body">
      <form action="" method="post">
        <div class="message-box"></div>
        <div class="form-group">
          <input type="hidden" name="done" value='<?php echo Yii::app()->request->urlReferrer; ?>' />
          <input type="text" class="form-control username no-success-help" autofocus placeholder="手机号码" name="LoginForm[username]" data-error="<?php $this->showError('username');?>" autocomplete="off"/>
        </div>
        <div class="form-group">
          <input type="password" class="form-control password no-success-help" name="LoginForm[password]" data-error="<?php $this->showError('password');?>"/>
        </div>
        <div class="clearfix form-group code-wrap">
          <input type="text" class="form-control code pull-left no-success-help" name="LoginForm[verifyCode]" data-error="<?php $this->showError('verifyCode');?>"/>
          <img src="<?php echo Yii::app()->request->hostinfo;?>/ajax/default/index/action/captcha" width="80" height="30" alt="点击刷新验证码" class="pull-left refresh" title="点击刷新验证码"/>
          <span class="pull-left refresh">看不清换一张</span>
        </div>
        <button class="btn btn-warning btn-block" type="submit" data-loading="正在登录…">登 录</button>
      </form>
      <div class="clearfix">
        <a href="/user/forgetpassword" class="pull-left">忘记登录密码？</a>
        <a href="/user/reg" class="pull-right">免费注册</a>
      </div>
    </div>
    <div class="login-foot">其它帐号登录：</div>
  </div>
</div>
<script>
  seajs.use('app/member/account/js/login.js');
</script>
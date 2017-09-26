<link rel="stylesheet" href="/modules/button/css/style.css"/>
<link rel="stylesheet" href="/modules/icon/css/style.css"/>
<link rel="stylesheet" href="/modules/form/css/style.css"/>
<link rel="stylesheet" href="/app/member/account/css/style.css"/>
<div class="container head">
    <h1 class="logo pull-left">
      <a href="<?php echo $this->homeUrl;?>"><img src="/app/home/image/logo.png" width="213" height="58" alt="优易料"/></a>
    </h1>
    <ol class="pull-left step list-inline text-bold">
      <li class="active">
        <i>1</i>验证用户信息
      </li>
      <li>
        <i>2</i>重置密码
      </li>
      <li>
        <i>3</i>完成
      </li>
    </ol>
</div>
<div class="container">
  <div class="form-horizontal">
	<form action="" method="post">
		<div class="form-group">
          <label class="control-label" for="account">登录名：</label>
          <input type="text" name="passwordForm[account]" class="form-control username no-success-help" id="account" autofocus placeholder="请输入注册时的手机号码" data-error="<?php $this->showError('account');?>"/>
        </div>
        <div class="form-group">
          <label class="control-label" for="verifyCode">验证码：</label>
          <input type="text" name="passwordForm[verifyCode]" class="form-control code append-help no-success-help" id="verifyCode" data-error="<?php $this->showError('verifyCode');?>"/>
          <button class="btn btn-cancel btn-xs send-code" type="button">免费发送验证码</button>
        </div>
        <div class="form-group form-group-offset">
          <button class="btn btn-warning">下一步</button>
        </div>
	</form>
  </div>
</div>
<script>
  seajs.use('app/member/account/js/findpassword.js');
</script>
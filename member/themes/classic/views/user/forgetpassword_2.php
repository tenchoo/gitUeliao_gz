<link rel="stylesheet" href="/modules/button/css/style.css"/>
<link rel="stylesheet" href="/modules/icon/css/style.css"/>
<link rel="stylesheet" href="/modules/form/css/style.css"/>
<link rel="stylesheet" href="/app/member/account/css/style.css"/>
<div class="container head">
    <h1 class="logo pull-left">
      <a href="<?php echo $this->homeUrl;?>"><img src="/app/home/image/logo.png" width="213" height="58" alt="优易料"/></a>
    </h1>
    <ol class="pull-left step list-inline text-bold">
      <li>
        <i>1</i>验证用户信息
      </li>
      <li class="active">
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
          <label class="control-label" for="password">新密码：</label>
          <input type="password" name="passwordForm[password]" class="form-control" id="password" data-help="请输入6-16个字符，密码需字母和数字组合"/>
        </div>
        <div class="form-group">
          <label class="control-label" for="confirmpassword">确认密码：</label>
          <input type="password" name="passwordForm[repassword]" class="form-control" id="confirmpassword" data-help="请再次输入密码"/>
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
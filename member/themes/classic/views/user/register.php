<link rel="stylesheet" href="/modules/button/css/style.css"/>
<link rel="stylesheet" href="/modules/icon/css/style.css"/>
<link rel="stylesheet" href="/modules/form/css/style.css"/>
<link rel="stylesheet" href="/modules/area/css/style.css">
<link rel="stylesheet" href="/app/member/account/css/style.css"/>
<div class="container head">
  <h1 class="logo pull-left">
    <a href="<?php echo $this->homeUrl;?>"><img src="/app/home/image/logo.png" width="213" height="58" alt="优易料"/></a>
  </h1>
  <ol class="pull-left step list-inline text-bold">
    <li class="active">
      <i>1</i>填写注册信息
    </li>
    <li>
      <i>2</i>注册成功
    </li>
  </ol>
</div>
<div class="container reg">
  <div class="required">（说明：以下均为必填项）</div>
  <br>
  <div class="form-horizontal">
    <form action="" method="post">
      <div class="form-group">
        <label class="control-label" for="companyname">公司名称：</label>
        <input type="text" name="RegForm[companyname]" class="form-control username" id="companyname" data-ajax="/ajax/?action=checkcompname" data-error="<?php $this->showError('companyname');?>"/>
      </div>
      <div class="form-group area">
        <label class="control-label">公司地址：</label>
        <div class="inline-block area-select">
          <select name="" class="form-control province">
            <option value="default">请选择省份</option>
          </select>
          <select name="" class="form-control city">
            <option value="default">请选择市</option>
          </select>
          <select name="" class="form-control county">
            <option value="default">请选择区/县</option>
          </select>
          <input type="text" name="RegForm[areaId]" />
        </div>
      </div>
      <div class="form-group">
        <label class="control-label" for="address"></label>
        <input type="text" name="RegForm[address]" class="form-control username" placeholder="详细地址" id="address"/>
      </div>
      <div class="form-group">
        <label class="control-label" for="contactPerson">联系人：</label>
        <input type="text" name="RegForm[contactPerson]" class="form-control username" id="contactPerson"/>
      </div>
      <div class="form-group">
        <label class="control-label" for="account">手机号码：</label>
        <input type="text" name="RegForm[account]" class="form-control username" id="account" data-ajax="/ajax?action=CheckAccount" data-error="<?php $this->showError('account');?>"/>
      </div>
      <div class="form-group">
        <label class="control-label" for="password">登录密码：</label>
        <input type="password" name="RegForm[password]" data-help="请输入6-16个字符，密码需字母和数字组合" data-error="<?php $this->showError('password');?>" class="form-control" id="password"/>
        <ol class="clearfix password-strength list-unstyled form-group-offset text-center text-minor">
          <li class="level1"><span></span>弱</li>
          <li class="level2"><span></span>中</li>
          <li class="level3"><span></span>强</li>
        </ol>
      </div>
      <div class="form-group">
        <label class="control-label" for="repassword">确认密码：</label>
        <input type="password" name="RegForm[repassword]" data-help="请再次输入密码" class="form-control" id="repassword"/>
      </div>
      <div class="form-group">
        <label class="control-label" for="verifyCode">验证码：</label>
        <input type="text" name="RegForm[verifyCode]" class="form-control code append-help no-success-help" id="verifyCode" data-error="<?php $this->showError('verifyCode');?>"/>
        <button class="btn btn-cancel btn-xs send-code" type="button">免费发送验证码</button>
      </div>
      <div class="form-group form-group-offset">
        <label class="checkbox-inline">
          <input type="checkbox" name="RegForm[agree]" value="1" checked/>我已认真阅读并同意商城 <a href="javascript:" class="text-link">《用户注册协议》</a>
        </label>
      </div>
      <div class="form-group form-group-offset">
        <button class="btn btn-warning" type="submit" data-loading="正在注册…">同意协议并注册</button>
      </div>
    </form>
  </div>
</div>
<script>
  seajs.use('app/member/account/js/register.js');
</script>
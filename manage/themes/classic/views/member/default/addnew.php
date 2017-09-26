<h2 class="h3">添加会员</h2>
<form class="form-horizontal" method="post">
  <div class="form-group">
    <label class="control-label col-md-2" for="">电话：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm" name="phone" value="" /><?php $this->showError("phone");?>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">邮箱：</label>
    <div class="col-md-4">
      <input class="form-control input-sm" type="text"  name="email" value="" /><?php $this->showError("email");?>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">密码：</label>
    <div class="col-md-4">
      <input class="form-control input-sm" name="password" value="" type="password" /><?php $this->showError("password");?>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">确认密码：</label>
    <div class="col-md-4">
      <input class="form-control input-sm" name="repassword" value="" type="password" />
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">等级：</label>
    <div class="col-md-4">
      <select name="level" class="form-control input-sm"></select>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">头像：</label>
    <div class="col-md-4">
      <input type="hidden" name="face" value="" />
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">匿称：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm" name="nickname" value="" /><?php $this->showError("nickname");?>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">QQ：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm" name="qq" value="" /><?php $this->showError("qq");?>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">姓名：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm" name="username" value="" />
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">性别：</label>
    <div class="col-md-4">
      <label class="radio-inline"><input type="radio" name="sex" value="1" checked />男</label>
      <label class="radio-inline"><input type="radio" name="sex" value="2" />女</label>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">生日：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm" name="birthdate" value="" />
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-10">
      <button class="btn btn-success">保存</button>
    </div>
  </div>
</form>
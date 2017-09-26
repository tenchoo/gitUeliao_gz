<h2 class="h3">修改密码</h2>
<?php $this->beginContent('//layouts/_success');$this->endContent();?>
<form method="post" class="form-horizontal">
<div class="form-group">
  <label class="control-label col-md-2"><span class="text-danger">*</span>原密码：</label>
  <div class="col-md-4">
   <input type="password" class="form-control input-sm" name="form[oldpassword]"/>
  </div>
</div>
<div class="form-group">
  <label class="control-label col-md-2"><span class="text-danger">*</span>新密码：</label>
  <div class="col-md-4">
    <input type="password" class="form-control input-sm" name="form[password]"/>
  </div>
</div>
<div class="form-group">
  <label class="control-label col-md-2"><span class="text-danger">*</span>确认密码：</label>
  <div class="col-md-4">
    <input type="password" class="form-control input-sm" name="form[repassword]" />
  </div>
</div>
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<div class="form-group">
  <div class="col-md-offset-2 col-md-10">
    <button class="btn btn-success">保存</button>
  </div>
</div>
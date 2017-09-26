<link rel="stylesheet" href="/themes/classic/statics/app/role/css/style.css">
<form method="post" class="form-horizontal">
<div class="form-group">
  <label class="control-label col-md-2" for="">部门名称：</label>
  <div class="col-md-4">
    <input type="text" class="form-control input-sm" name="departmentName" value="<?php echo $departmentName;?>">
  </div>
</div>
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<div class="form-group">
  <div class="col-md-offset-2 col-md-10">
    <button class="btn btn-success">保存</button>
  </div>
</div>
</form>
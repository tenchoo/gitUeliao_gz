<link rel="stylesheet" href="/themes/classic/statics/app/data/css/style.css">
<form method="post" class="form-horizontal">
<div class="form-group">
  <label class="control-label col-md-2" for=""><span class="text-danger">*</span>工厂编号：</label>
  <div class="col-md-4">
    <input type="text" class="form-control input-sm" name="data[factoryNumber]" value="<?php echo $factoryNumber;?>">
  </div>
</div>
<div class="form-group">
  <label class="control-label col-md-2" for=""><span class="text-danger">*</span>工厂名称：</label>
  <div class="col-md-4">
    <input type="text" class="form-control input-sm" name="data[shortname]" value="<?php echo $shortname;?>">
  </div>
</div>
<div class="form-group">
  <label class="control-label col-md-2" for=""><span class="text-danger">*</span>联系人：</label>
  <div class="col-md-4">
    <input type="text" class="form-control input-sm" name="data[contact]" value="<?php echo $contact;?>">
  </div>
</div>
<div class="form-group">
  <label class="control-label col-md-2" for=""><span class="text-danger">*</span>联系电话：</label>
  <div class="col-md-4">
    <input type="text" class="form-control input-sm" name="data[phone]" value="<?php echo $phone;?>">
  </div>
</div>
<div class="form-group">
  <label class="control-label col-md-2" for="">联系地址：</label>
  <div class="col-md-4">
    <input type="text" class="form-control input-sm" name="data[adddress]" value="<?php echo $adddress;?>">
  </div>
</div>
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<div class="form-group">
    <div class="col-md-offset-2 col-md-10">
        <button type="submit" class="btn btn-success">保存</button>
    </div>
</div>
</form>
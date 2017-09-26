<link rel="stylesheet" href="/themes/classic/statics/app/role/css/style.css">
 <form method="post" class="form-horizontal">
<div class="form-group">
  <label class="control-label col-md-2" for=""><span class="text-danger">*</span>打印机编号：</label>
  <div class="col-md-4">
    <input type="text" class="form-control input-sm" name="data[printerSerial]" value="<?php echo $printerSerial;?>"/>
  </div>
</div>
<div class="form-group">
  <label class="control-label col-md-2" for=""><span class="text-danger">*</span>说明：</label>
  <div class="col-md-4">
    <input type="text" class="form-control input-sm" name="data[mark]" value="<?php echo $mark;?>"/>
  </div>
</div>
<div class="form-group">
  <label class="control-label col-md-2" for="">状态：</label>
  <div class="col-md-4">
    <label class="radio-inline">
   <input type="radio" name="data[state]" value="0" <?php echo ($state =='0')?'checked':''?>/>启用</label>
   <label class="radio-inline">
	<input type="radio" name="data[state]" value="1" <?php echo ($state !='0')?'checked':''?>/>禁用</label>
  </div>
</div>
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<div class="form-group">
  <div class="col-md-offset-2 col-md-10">
    <button class="btn btn-success">保存</button>
  </div>
</div>
</form>
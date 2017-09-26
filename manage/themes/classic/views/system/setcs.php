<link rel="stylesheet" href="/themes/classic/statics/app/role/css/style.css">
 <form method="post" class="form-horizontal" id="myForm">
<div class="form-group">
  <label class="control-label col-md-2" for=""><span class="text-danger">*</span>名称：</label>
  <div class="col-md-4">
    <input type="text" class="form-control input-sm" name="data[csName]" value="<?php echo $csName;?>"/>
  </div>
</div>
<div class="form-group">
  <label class="control-label col-md-2" for=""><span class="text-danger">*</span>号码：</label>
  <div class="col-md-4">
    <input type="text" class="form-control input-sm" name="data[csAccount]" value="<?php echo $csAccount;?>"/>
  </div>
</div>
<div class="form-group">
  <label class="control-label col-md-2" for="">类型：</label>
  <div class="col-md-4">
    <label class="radio-inline">
      <input type="radio" name="data[type]" value="1" <?php echo ($type!='2')?'checked':''?>/>QQ&nbsp;
    </label>
    <label class="radio-inline">
	   <input type="radio" name="data[type]" value="2" <?php echo ($type=='2')?'checked':''?>/>旺旺</label>
  </div>
</div>
<div class="form-group">
  <label class="control-label col-md-2" for="">状态：</label>
  <div class="col-md-4">
    <label class="radio-inline">
   <input type="radio" name="data[state]" value="1" <?php echo ($state!='0')?'checked':''?>/>启用</label>
   <label class="radio-inline">
	<input type="radio" name="data[state]" value="0" <?php echo ($state=='0')?'checked':''?>/>禁用</label>
  </div>
</div>
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<div class="form-group">
  <div class="col-md-offset-2 col-md-10">
    <button class="btn btn-success">保存</button>
  </div>
</div>
</form>
<style type="text/css">  
label.error {  
    color: orange;  
}  
</style> 
<script>seajs.use('statics/libs/jquery-validation/1.13.1/jquery.validate.js');</script>
<script>seajs.use('statics/libs/jquery-validation/1.13.1/localization/messages_zh.js');</script>
<script>seajs.use('statics/app/member/js/setcs.js');</script>

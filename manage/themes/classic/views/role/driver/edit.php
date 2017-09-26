<link rel="stylesheet" href="/themes/classic/statics/app/role/css/style.css">
<form method="post" class="form-horizontal" id="myForm">
<div class="form-group">
  <label class="control-label col-md-2" for=""><span class="text-danger">*</span>姓名：</label>
  <div class="col-md-4">
    <input type="text" class="form-control input-sm" name="data[driverName]" value="<?php echo $driverName;?>">
  </div>
</div>
<div class="form-group">
  <label class="control-label col-md-2" for=""><span class="text-danger">*</span>手机号码：</label>
  <div class="col-md-4">
    <input type="text" class="form-control input-sm" name="data[phone]" value="<?php echo $phone;?>">
  </div>
</div>
<div class="form-group">
    <label class="control-label col-md-2">性别：</label>
    <div class="col-md-4">
      <label class="radio-inline"><input type="radio" checked="checked" value="0" name="data[gender]">男</label>
      <label class="radio-inline"><input type="radio" value="1" name="data[gender]">女</label>
    </div>
  </div>
<div class="form-group">
  <label class="control-label col-md-2" for="">身份证号码：</label>
  <div class="col-md-4">
    <input type="text" class="form-control input-sm" name="data[idcard]" value="<?php echo $idcard;?>">
  </div>
</div>

<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<div class="form-group">
  <div class="col-md-offset-2 col-md-10">
    <button class="btn btn-success">保存</button>
  </div>
</div>
</form>
<style>
label.error {  
    color: orange;  
}  
</style> 
<script>seajs.use('statics/libs/jquery-validation/1.13.1/jquery.validate.js');</script>
<script>seajs.use('statics/libs/jquery-validation/1.13.1/localization/messages_zh.js');</script>
<script>seajs.use('statics/app/member/js/driver.js');</script>
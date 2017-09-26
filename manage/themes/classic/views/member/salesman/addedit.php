<form class="form-horizontal" method="post" id="myForm">
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>手机：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm" name="data[phone]" value="<?php echo $data['phone']?>" />
    </div>
  </div>
  <?php if(empty($memberId)){ ?>
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>密码：</label>
    <div class="col-md-4">
      <input class="form-control input-sm" name="data[password]" type="password" />
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>确认密码：</label>
    <div class="col-md-4">
      <input class="form-control input-sm" name="data[repassword]" type="password" />
    </div>
  </div>
  <?php }?>
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>真实姓名：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm" name="data[username]" value="<?php echo $data['username']?>" />
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
<script>seajs.use('statics/app/member/js/addedit.js');</script>
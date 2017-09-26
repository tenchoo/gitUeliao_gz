<link rel="stylesheet" href="/themes/classic/statics/modules/uploader/css/style.css"/>
<form class="form-horizontal" method="post" action="">
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span> 支付名称：</label>
    <div class="col-md-4">
      <input type="text" name="pay[paymentTitle]" value="<?php echo $data['paymentTitle']; ?>" class="form-control input-sm" />
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span> 所属类型：</label>
    <div class="col-md-4">
      <?php echo CHtml::dropDownList('pay[type]',$data['type'],$type,array('class'=>'form-control input-sm'))?>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">接口类：</label>
    <div class="col-md-4">
      <input type="text" name="pay[paymentSet][class_name]" value="<?php echo isset($data['paymentSet']['class_name'])?$data['paymentSet']['class_name']:''; ?>" class="form-control input-sm" />
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">账户名称：</label>
    <div class="col-md-4">
      <input type="text" name="pay[paymentSet][payment_user]" value="<?php echo isset($data['paymentSet']['payment_user'])?$data['paymentSet']['payment_user']:''; ?>" class="form-control input-sm" />
    </div>
  </div>
    <div class="form-group">
    <label class="control-label col-md-2">账户ID：</label>
    <div class="col-md-4">
      <input type="text" name="pay[paymentSet][payment_id]" value="<?php echo isset($data['paymentSet']['payment_id'])?$data['paymentSet']['payment_id']:''; ?>" class="form-control input-sm" />
    </div>
  </div>
    <div class="form-group">
    <label class="control-label col-md-2">交易安全校验码：</label>
    <div class="col-md-4">
      <input type="text" name="pay[paymentSet][payment_key]" value="<?php echo isset($data['paymentSet']['payment_key'])?$data['paymentSet']['payment_key']:''; ?>" class="form-control input-sm" />
    </div>
  </div>
    <div class="form-group face-group">
    <label class="control-label col-md-2">图标：</label>
    <div class="col-md-4">
	    <span class="uploader uploader-image">
	      <button type="button" class="image-wrap">
	        <?php if( !empty($data['logo']) ):?>
	          <img src="<?php echo $this->img().$data['logo'];?>" alt="" width="80" height="80">
	          <span class="bg"></span>
	          <span>重新上传</span>
	        <?php endif;?>
	      </button>
	      <input type="hidden" name="pay[logo]" value="<?php echo $data['logo'];?>"/>
	    </span>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">是否启用：</label>
    <div class="col-md-4">
      <label class="radio-inline">
		<input type="radio" name="pay[available]" value="0" <?php if ($data['available'] == 0) {echo 'checked';}?>/>停用
	 </label>
      <label class="radio-inline">
		<input type="radio" name="pay[available]" value="1" <?php if ($data['available'] == 1) {echo 'checked';}?>/>启用
	  </label>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">使用终端：</label>
    <div class="col-md-4">
      <label class="checkbox-inline">
		<input type="checkbox" name="pay[termType][]" value="1" <?php if (in_array($data['termType'],array('1','3'))) {echo 'checked';}?>/>PC端
	 </label>
      <label class="checkbox-inline">
		<input type="checkbox" name="pay[termType][]" value="2" <?php if (in_array($data['termType'],array('2','3'))) {echo 'checked';}?>/>微信端
	  </label>
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
<script>seajs.use('statics/app/member/js/payment-addeit.js');</script>
<script>
  seajs.use('statics/app/order/js/paymentadd.js');
</script>
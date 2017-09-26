<br>
<form class="form-horizontal" method="post" action="">
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>  支付类型：</label>
    <div class="col-md-4">
      <input type="text" name="paymentTitle" value="<?php echo $paymentTitle; ?>" class="form-control input-sm" />
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">使用终端：</label>
    <div class="col-md-4">
      <label class="checkbox-inline">
		<input type="checkbox" name="termType[]" value="1" <?php if (in_array($termType,array('1','3'))) {echo 'checked';}?>/>PC端
	 </label>
      <label class="checkbox-inline">
		<input type="checkbox" name="termType[]" value="2" <?php if (in_array($termType,array('2','3'))) {echo 'checked';}?>/>微信端
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
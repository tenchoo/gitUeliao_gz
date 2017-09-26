<form class="form-horizontal" method="post" action="">
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span> 物流名称：</label>
    <div class="col-md-4">
      <input type="text" name="data[title]" value="<?php echo $data['title']; ?>" class="form-control input-sm" />
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>物流标识：</label>
    <div class="col-md-4">
      <input type="text" name="data[mark]" value="<?php echo $data['mark']; ?>" class="form-control input-sm" />
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">是否货到付款：</label>
    <div class="col-md-4">
      <label class="radio-inline">
		<input type="radio" name="data[isCOD]" value="0" <?php if ($data['isCOD'] != 1) {echo 'checked';}?>/>否
	 </label>
      <label class="radio-inline">
		<input type="radio" name="data[isCOD]" value="1" <?php if ($data['isCOD'] == 1) {echo 'checked';}?>/>是
	  </label>
    </div>
  </div>
  <!--div class="form-group">
    <label class="control-label col-md-2">是否启用：</label>
    <div class="col-md-4">
      <label class="radio-inline">
		<input type="radio" name="data[isDel]" value="0" <?php if ($data['isDel'] != 1) {echo 'checked';}?>/>否
	 </label>
      <label class="radio-inline">
		<input type="radio" name="data[isDel]" value="1" <?php if ($data['isDel'] == 1) {echo 'checked';}?>/>是
	  </label>
    </div>
  </div-->
 <?php $this->beginContent('//layouts/_error');$this->endContent();?>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-10">
      <button class="btn btn-success">保存</button>
    </div>
  </div>
</form>

<form class="form-horizontal" method="post" action="">
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span> 接口地址：</label>
    <div class="col-md-4">
      <input type="text" name="data[Host]" value="<?php echo $data['Host']; ?>" class="form-control input-sm" />
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span> 端口：</label>
    <div class="col-md-4">
      <input type="text" name="data[Port]" value="<?php echo $data['Port']; ?>" class="form-control input-sm" />
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>账号：</label>
    <div class="col-md-4">
      <input type="text" name="data[username]" value="<?php echo $data['username']; ?>" class="form-control input-sm" />
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>密码：</label>
    <div class="col-md-4">
      <input type="password" name="data[password]" value="<?php echo $data['password']; ?>" class="form-control input-sm" />
    </div>
  </div>
    <div class="form-group">
    <label class="control-label col-md-2">md5：</label>
	 <div class="col-md-4">
      <label class="radio-inline">
		<input type="radio" name="data[md5]" value="1" <?php if ( $data['md5'] ) {echo 'checked';}?>/>是
	 </label>
      <label class="radio-inline">
		<input type="radio" name="data[md5]" value="0" <?php if ( empty($data['md5'])) {echo 'checked';}?>/>否
	  </label>
    </div>
  </div>
   <div class="form-group">
    <label class="control-label col-md-2">手机接口商：</label>
	 <div class="col-md-4">
      <label class="radio-inline">
		<input type="radio" name="data[type]" value="1" <?php if ( $data['type'] != '2' && $data['type'] != '3') {echo 'checked';}?>/>商翼通
	 </label>
      <label class="radio-inline">
		<input type="radio" name="data[type]" value="2" <?php if ($data['type'] == 2) {echo 'checked';}?>/>商脉
	  </label>
	  <label class="radio-inline">
		<input type="radio" name="data[type]" value="3" <?php if ($data['type'] == 3) {echo 'checked';}?>/>互亿无线
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

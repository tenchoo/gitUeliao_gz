<link rel="stylesheet" href="/themes/classic/statics/modules/uploader/css/style.css"/>
<link rel="stylesheet" href="/themes/classic/statics/app/member/css/style.css"/>
<?php $this->beginContent('_tabs');$this->endContent();?>
<br>
<form class="form-horizontal" method="post" action="">
  <div class="form-group">
    <label class="control-label col-md-2" for="">电话：</label>
    <div class="col-md-4">
      <input type="text" disabled="disabled" class="form-control input-sm" name="phone" value="<?php echo $infos->phone;?>" /><?php $this->showError("phone");?>
    </div>
  </div>
  <div class="form-group face-group">
    <label class="control-label col-md-2">当前头像：</label>
    <div class="col-md-4">
	    <span class="uploader uploader-image">
	      <button type="button" class="image-wrap" disabled="disabled">
	        <?php if($infos->profile->icon) {?>
	          <img src="<?php echo $this->img(false).$infos->profile->icon;?>" alt="" width="80" height="80">
	          <!-- <span class="bg"></span>
	          <span>重新上传</span> -->
	        <?php }?>
	      </button>
	    </span>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>昵称：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm" name="nickName" value="<?php echo $infos->nickName;?>" disabled="disabled"/><?php $this->showError("nickname");?>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>QQ：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm" name="qq" value="<?php echo $infos->profile->qq;?>" disabled="disabled"/><?php $this->showError("qq");?>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">姓名：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm" name="username" value="<?php echo $infos->profile->username;?>" disabled="disabled"/>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">性别：</label>
    <div class="col-md-4">
      <label class="radio-inline"><input type="radio" name="sex" value="0" <?php if ($infos->profile->sex == 0) {echo 'checked="checked"';}?> disabled="disabled"/>男</label>
      <label class="radio-inline"><input type="radio" name="sex" value="1" <?php if ($infos->profile->sex == 1) {echo 'checked="checked"';}?> disabled="disabled"/>女</label>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">生日：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm input-date" name="birthdate" id="birthday" readonly value="<?php echo ($infos->profile->birthdate=='0000-00-00')?'':$infos->profile->birthdate;?>" disabled="disabled"/>
    </div>
  </div>
</form>
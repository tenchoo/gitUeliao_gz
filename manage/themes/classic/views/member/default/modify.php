<link rel="stylesheet" href="/themes/classic/statics/modules/uploader/css/style.css"/>
<link rel="stylesheet" href="/themes/classic/statics/app/member/css/style.css"/>
<?php $this->beginContent('_tabs');$this->endContent();?>
<br>
<form class="form-horizontal" method="post" action="">
<input name="memberId" type="hidden" value="<?php echo $infos->memberId;?>" />
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
	      <button type="button" class="image-wrap">
	        <?php if( !empty($infos->profile->icon) ):?>
	          <img src="<?php echo $this->img().$infos->profile->icon;?>" alt="" width="80" height="80">
	          <span class="bg"></span>
	          <span>重新上传</span>
	        <?php endif;?>
	      </button>
	    </span>
	    <input type="hidden" name="face" value="<?php echo $infos->profile->icon?>"/>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>昵称：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm" name="nickName" value="<?php echo $infos->nickName;?>" maxlength='15'/><?php $this->showError("nickname");?>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>QQ：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm" name="qq" value="<?php echo $infos->profile->qq;?>" maxlength='20'/><?php $this->showError("qq");?>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">姓名：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm" name="username" value="<?php echo $infos->profile->username;?>" maxlength='15'/>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">性别：</label>
    <div class="col-md-4">
      <label class="radio-inline"><input type="radio" name="sex" value="0" <?php if ($infos->profile->sex == 0) {echo 'checked="checked"';}?>/>男</label>
      <label class="radio-inline"><input type="radio" name="sex" value="1" <?php if ($infos->profile->sex == 1) {echo 'checked="checked"';}?>/>女</label>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">生日：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm input-date" name="birthdate" id="birthday" readonly value="<?php echo ($infos->profile->birthdate=='0000-00-00')?'':$infos->profile->birthdate;?>" />
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-md-2" for="">分拣仓库：</label>
    <div class="col-md-4">
    <?php echo CHtml::dropDownList('sortingWarehouseId',$infos->profile->sortingWarehouseId,$type,array('class'=>'form-control  input-sm','empty'=>'请选择分类'))?>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-offset-2 col-md-10">
      <button class="btn btn-success">保存</button>
      <button class="btn btn-default" type="button" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $infos->memberId;?>" data-rel="<?php echo $this->createUrl('resetpassword');?>">重置密码</button>
    </div>
  </div>
</form>
<script>
  seajs.use('statics/app/member/js/modify.js');
</script>

<div class="modal fade del-confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">重置密码</h4>
      </div>
      <div class="modal-body">
        <p>您确定要重置密码吗？</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" class="btn btn-success">确定</button>
      </div>
    </div>
  </div>
</div>
<script>seajs.use('statics/common/common.js');</script>
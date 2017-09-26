<link rel="stylesheet" href="/themes/classic/statics/modules/uploader/css/style.css"/>
<form class="form-horizontal"  method="post">
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>呆滞级别：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="title" value="<?php echo $title;?>" maxlength="8">
    </div>
  </div>

  <div class="form-group">
    <label class="control-label col-md-2" for="conditions"><span class="text-danger">*</span>默认时长：</label>
    <div class="col-md-4">
	<div class="input-group">
		<input type="text" class="form-control input-sm" name="conditions" value="<?php echo $conditions;?>"> <span class="input-group-addon">小时</span>
		 </div>
    </div>
  </div>

  <div class="form-group face-group">
    <label class="control-label col-md-2">等级图标：</label>
    <div class="col-md-4">
      <span class="uploader uploader-image">
        <button type="button" class="image-wrap">
          <?php if( !empty($logo) ):?>
            <img src="<?php echo $this->img().$logo;?>" alt="" width="80" height="80">
            <span class="bg"></span>
            <span>重新上传</span>
          <?php endif;?>
        </button>
        <input type="hidden" name="logo" value="<?php echo $logo;?>"/>
      </span>
    </div>
  </div>

  <?php $this->beginContent('//layouts/_error');$this->endContent();?>
  <br/>
  <div class="col-md-offset-2">
    <button class="btn btn-success" type="submit">保存</button>
    <a href="<?php echo $this->createUrl( 'index' );?>" class="btn btn-default">取消</a>
  </div>
</form>
<script>
seajs.use('statics/app/tailgoods/js/level.js');
</script>
<link rel="stylesheet" href="/themes/classic/statics/modules/uploader/css/style.css"/>
<form class="form-horizontal" method="post">
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>  等级名称：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm" name="title" value="<?php echo $title?>"/>
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
  <div class="form-group">
    <div class="col-md-offset-2 col-md-10">
      <button class="btn btn-success">保存</button>
    </div>
  </div>
</form>
<script>
seajs.use('statics/app/member/js/level.js');
</script>
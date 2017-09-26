<link rel="stylesheet" href="/themes/classic/statics/modules/uploader/css/style.css"/>
<form class="form-horizontal"  method="post">
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>工艺名称：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="title" value="<?php echo $data['title'];?>" maxlength="10">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>工艺编号：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="craftCode" value="<?php echo $data['craftCode'];?>" maxlength="3">
    </div>
  </div>
  <?php if( $parentCode ){ ?>
  <div class="form-group">
          <label class="control-label col-md-2" for=""><span class="text-danger">*</span>上级编号：</label>
		   <div class="col-md-4">
		<input type="text" class="form-control input-sm"  value="<?php echo $parentCode;?>" disabled >
		</div>
  </div>
  <?php }else{ ?>
  <div class="form-group">
    <label class="control-label col-md-2">是否分等级：</label>
	 <div class="col-md-4">
      <label class="radio-inline">
		<input type="radio" name="hasLevel" value="0" <?php if ( $data['hasLevel'] != '1') {echo 'checked';}?>  <?php if ( $hasLevel) {echo 'disabled';}?>/>否
	 </label>
      <label class="radio-inline">
		<input type="radio" name="hasLevel" value="1" <?php if ($data['hasLevel'] == 1 ) {echo 'checked';}?> <?php if ( $hasLevel) {echo 'disabled';}?>/>是
	  </label>
    </div>
  </div>
  <?php }?>
  <div class="form-group face-group">
    <label class="control-label col-md-2">图标：</label>
    <div class="col-md-4">
	    <span class="uploader uploader-image">
	      <button type="button" class="image-wrap">
	        <?php if( !empty($data['icon']) ):?>
	          <img src="<?php echo $this->img().$data['icon'];?>" alt="" width="80" height="80">
	          <span class="bg"></span>
	          <span>重新上传</span>
	        <?php endif;?>
	      </button>
	      <input type="hidden" name="icon" value="<?php echo $data['icon'];?>"/>
	    </span>
    </div>
  </div>
  <?php $this->beginContent('//layouts/_error');$this->endContent();?>
  <br/>
  <div class="col-md-offset-2 col-md-10">
    <button class="btn btn-success" type="submit">保存</button>
    <a href="<?php echo $this->createUrl( 'index' );?>" class="btn btn-default">取消</a>
  </div>
</form>
<script>
seajs.use('statics/app/product/create/js/craftadd.js');
</script>

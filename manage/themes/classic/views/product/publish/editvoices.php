<link rel="stylesheet" href="/themes/classic/statics/modules/uploader/css/style.css"/>
<?php $this->beginContent('_tab',array('active'=>'voices','productId'=>$productId));$this->endContent();?>
<div class="clearfix alert alert-warning">
  <a href="<?php echo $this->createUrl('index',array('step'=>'voices','id'=>$productId));?>" class="pull-right"><span class="glyphicon glyphicon-share-alt" style="-moz-transform:scaleX(-1);
    -webkit-transform:scaleX(-1);
    -o-transform:scaleX(-1);
    transform:scaleX(-1);
    filter:FlipH;"></span>取消</a>
  产品编号：<strong class="text-warning"><?php echo $serialNumber;?></strong>
</div>
<form class="form-horizontal" method="post" action="">
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span> 标题：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm" name="data[title]" value="<?php echo $title;?>"/>
    </div>
	</div>
	<div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span> 上传音频： readonly</label>
    <div>
  	 <div class="col-md-3">
        <input type="text" class="form-control input-sm" name="data[sound]" value="<?php echo $sound;?>"/>
      </div>
      <span class="col-md-2 uploader uploader-button">
        <button type="button" class="btn btn-sm btn-primary"><?php if($sound){ ?>重新上传<?php }else{ ?>上传音频<?php } ?></button>
      </span>
    </div>
    <div class="col-md-offset-2 col-md-4">
      上传 说明：只支持 amr 音频格式
    </div>
  </div>
	<div class="form-group">
    <label class="control-label col-md-2">设为主音频：</label>
    <div class="col-md-4">
      <label class="checkbox-inline">
    		<input type="checkbox" name="data[isMain]" value="1" <?php if( $isMain == '1' ){ echo 'checked';}?>/>是
    	 </label>
    </div>
  </div>
  <div class="form-group face-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span> 排序：</label>
  	 <div class="col-md-4">
     <input type="text" class="form-control input-sm" name="data[sort]" value="<?php echo $sort;?>" min="0"/>
	  说明：数字越大，排在越后，主音频排在最前面。
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
  seajs.use('statics/app/product/create/js/voice.js');
</script>
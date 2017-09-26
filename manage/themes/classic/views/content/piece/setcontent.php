<link rel="stylesheet" href="/themes/classic/statics/app/product/create/css/style.css">
<form class="form-horizontal"  method="post">
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span> 碎片名称：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm"  name="data[title]" value="<?php echo $title;?>">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span> 碎片标识：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="data[mark]" value="<?php  echo $mark;?>" maxlength="16">
    </div>
  </div> 
  <div class="form-group details">
  <label class="control-label col-md-2" for="">碎片内容：</label>
  <div class="col-md-4">
    <textarea name="data[content]" class="content"><?php echo $content ?></textarea>
  </div>
</div>
  <?php $this->beginContent('//layouts/_error');$this->endContent();?>
  <br/>
  <div class="col-md-offset-2 col-md-10">
    <button class="btn btn-sm btn-success" type="submit">保存</button>
  </div>
</form>
<script>
  seajs.use('statics/app/help/js/create.js');
</script>

<link rel="stylesheet" href="/themes/classic/statics/app/product/create/css/style.css">
<form action="" class="form-horizontal" method="post">

<div class="form-group">
      <label class="control-label col-md-2">分类：</label>
      <div class="inline-block warehousechoose col-md-4">
        <input type="text" name="title" class="form-control input-sm" value="<?php echo $title;?>" />
      </div>
    </div>
<div class="form-group details">
  <label class="control-label col-md-2" for="">信息内容：</label>
  <div class="col-md-4">
    <textarea name="content" class="content"><?php echo $content ?></textarea>
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
  seajs.use('statics/app/help/js/create.js');
</script>
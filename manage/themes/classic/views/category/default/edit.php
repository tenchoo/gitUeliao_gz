<link rel="stylesheet" href="/themes/classic/statics/app/product/category/css/style.css">
<form class="form-horizontal" method="post">
  <div class="form-group">
    <label class="control-label col-md-2" for="">标题：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm" name="form[seoTitle]" value="<?php $this->val('seoTitle');?>">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for="">关键字：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm" name="form[seoKeywords]" value="<?php $this->val('seoKeywords');?>">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for="">描述：</label>
    <div class="col-md-4">
      <textarea name="form[seoDesc]" class="form-control"><?php $this->val('seoDesc');?></textarea>
    </div>
  </div>
  <?php if($this->hasError()):?>
  <div class="col-md-5 col-md-offset-1 alert alert-danger">
    <?php echo $this->getError();?>
  </div>
  <?php endif;?>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-10">
      <button class="btn btn-success" type="submit">保存</button>
      <a href="/category" class="btn btn-default">取消</a>
    </div>
  </div>
</form>
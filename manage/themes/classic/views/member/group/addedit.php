<form class="form-horizontal" method="post">
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>  客户组名称：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm" name="title" value="<?php echo $title?>" />
    </div>
  </div>
  <?php $this->beginContent('//layouts/_error');$this->endContent();?>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-10">
      <button class="btn btn-success">保存</button>
    </div>
  </div>
</form>

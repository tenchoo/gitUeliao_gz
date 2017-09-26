<form class="form-horizontal"  method="post">
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span> 名称：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm"  name="data[title]" value="<?php echo $title;?>">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span> 标识：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="data[mark]" value="<?php  echo $mark;?>" maxlength="15">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span> 推荐数量：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="data[maxNum]" value="<?php echo $maxNum;?>" maxlength="2">
    </div>
  </div>
  <?php $this->beginContent('//layouts/_error');$this->endContent();?>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-10">
      <button class="btn btn-sm btn-success" type="submit">保存</button>
    </div>
  </div>
</form>

<form class="form-horizontal"  method="post">
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span> 名称：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm"  name="data[title]" value="<?php echo $title;?>" maxlength="20">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span> 标识：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="data[identity]" value="<?php  echo $identity;?>" maxlength="15" <?php if($scenario !='insert' ){ echo 'disabled';}?>>
    </div>
	标识建立后不可编辑
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span> 推荐条数：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="data[maxNum]" value="<?php echo $maxNum;?>" maxlength="2">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for=""> 备注：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm"  name="data[remark]" value="<?php echo $remark;?>">
    </div>
  </div>
  <?php $this->beginContent('//layouts/_error');$this->endContent();?>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-10">
      <button class="btn btn-sm btn-success" type="submit">保存</button>
    </div>
  </div>
</form>

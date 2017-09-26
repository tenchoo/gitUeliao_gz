<form method="post" class="form-horizontal">
<div class="form-group">
  <label class="control-label col-md-2" for=""><span class="text-danger">*</span>角色组：</label>
  <div class="col-md-4">
    <?php echo CHtml::dropDownList('roleId',$roleId,$roles,array('class'=>'form-control input-sm'))?>
  </div>
</div>
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<div class="form-group">
  <div class="col-md-offset-2 col-md-10">
    <button class="btn btn-success">保存</button>
  </div>
</div>
</form>


<form method="post" class="form-horizontal">
<div class="form-group">
  <label class="control-label col-md-2" for=""><span class="text-danger">*</span>姓名：</label>
  <div class="col-md-4">
    <input type="text" class="form-control input-sm" disabled value="<?php echo $username;?>">
  </div>
</div>
<div class="form-group">
  <label class="control-label col-md-2" for=""><span class="text-danger">*</span>服务仓库：</label>
  <div class="col-md-4">
    <?php echo CHtml::dropDownList('warehouseId',$warehouseId,$warehouse,array('class'=>'form-control input-sm'))?>
  </div>
</div>
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<div class="form-group">
  <div class="col-md-offset-2 col-md-10">
    <button class="btn btn-success">保存</button>
  </div>
</div>
</form>
<link rel="stylesheet" href="/themes/classic/statics/app/role/css/style.css">
<form method="post" class="form-horizontal">
    <input type="hidden" name="callback" value="<?php echo Yii::app()->request->urlReferrer;?>" />
 <div class="form-group">
    <label class="control-label col-md-2" for="sale-region">所属部门：</label>
    <div class="col-md-4">
		<input type="text" class="form-control input-sm" disabled value="<?php echo $departmentName;?>">
      <?php //echo CHtml::dropDownList('departmentId',$departmentId,$departments,array('class'=>'form-control input-sm','empty'=>'请选择部门'))?>
    </div>
  </div>
<div class="form-group">
  <label class="control-label col-md-2" for="">职位名称：</label>
  <div class="col-md-4">
    <input type="text" class="form-control input-sm" name="positionName" value="<?php echo $positionName;?>">
  </div>
</div>

<div class="form-group">
    <label class="control-label col-md-2" for="">角色：</label>
    <div class="col-md-4">
        <?php echo CHtml::checkBoxList('roleId[]', $selected, $roles, ['separator'=>'&nbsp;']); ?>
    </div>
</div>
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<div class="form-group">
  <div class="col-md-offset-2 col-md-10">
    <button class="btn btn-success">保存</button>
  </div>
</div>
</form>
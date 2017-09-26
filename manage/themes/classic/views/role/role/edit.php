<link rel="stylesheet" href="/themes/classic/statics/app/role/css/style.css">
<form method="post" class="form-horizontal">
<!--div class="form-group">
    <label class="control-label col-md-2" for="sale-region">所属部门：</label>
    <div class="col-md-4">
      <?php //echo CHtml::dropDownList('departmentId',$role['departmentId'],$departments,array('class'=>'form-control input-sm','empty'=>'请选择部门'))?>
    </div>
  </div-->
<div class="form-group">
  <label class="control-label col-md-2" for=""><span class="text-danger">*</span>角色名称：</label>
  <div class="col-md-4">
    <input type="text" class="form-control input-sm" name="roleName" value="<?php echo $role['roleName'];?>">
  </div>
</div>
<div class="form-group">
  <label class="control-label col-md-2" for="description" >角色描述：</label>
  <div class="col-md-4">
    <textarea name="description" class="form-control"><?php echo $role['description'];?></textarea>
  </div>
</div>
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<div class="form-group">
  <div class="col-md-offset-2 col-md-10">
    <button class="btn btn-success">保存</button>
  </div>
</div>
</form>
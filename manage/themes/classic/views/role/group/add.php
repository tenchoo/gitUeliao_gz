<link rel="stylesheet" href="/themes/classic/statics/app/role/css/style.css">
<form method="post" class="form-horizontal">
<div class="form-group">
    <label class="control-label col-md-2" for="sale-region">所属部门：</label>
    <div class="col-md-4">
      <?php echo CHtml::dropDownList('departmentId',$data['departmentId'],$departments,array('class'=>'form-control input-sm','empty'=>'请选择部门'))?>
    </div>
  </div>
<div class="form-group">
  <label class="control-label col-md-2" for="">角色组名称：</label>
  <div class="col-md-4">
    <input type="text" class="form-control input-sm" name="name" value="<?php echo $data['name'];?>">
  </div>
</div>
<div class="form-group">
  <label class="control-label col-md-2" for="">权限设置：</label>
  <div class="col-md-4">
	<?php foreach( $roles as $role ):?>
	<input type="checkbox" name="roleId[]" value="<?php echo $role['roleId']?>" <?php if($role['ischoose']){ echo 'checked';}?>/><?php echo $role['roleName'];?>
    <?php endforeach; ?>
  </div>
</div>
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<div class="form-group">
  <div class="col-md-offset-2 col-md-10">
    <button class="btn btn-success">保存</button>
  </div>
</div>
</form>
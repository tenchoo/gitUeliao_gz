<link rel="stylesheet" href="/themes/classic/statics/app/role/css/style.css">

<form method="post" class="form-horizontal">
<?php if(!empty($title ) ){ ?>
<div class="form-group">
  <label class="control-label col-md-2">父级菜单：</label>
  <div class="col-md-4"> <input type="text" class="form-control input-sm" value="<?php echo implode('>',$title);?>" disabled /></div>
</div>
<?php }?>
<?php if(!empty( $data['id'] ) ){ ?>
<div class="form-group">
  <label class="control-label col-md-2">菜单ID</label>
  <div class="col-md-4">
    <input type="text" class="form-control input-sm" value="<?php echo $data['id'];?>" disabled />
  </div>
</div>
<?php }?>
<div class="form-group">
  <label class="control-label col-md-2">菜单类型</label>
  <div class="col-md-4">
    <input type="text" class="form-control input-sm" value="<?php echo $data['type'];?>" disabled />
  </div>
</div>
<div class="form-group">
  <label class="control-label col-md-2"><span class="text-danger">*</span>菜单名称</label>
  <div class="col-md-4">
    <input type="text" class="form-control input-sm" name="form[title]" value="<?php echo $data['title'];?>">
  </div>
</div>
<div class="form-group">
    <label class="control-label col-md-2">路由：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm" name="form[route]" value="<?php echo $data['route'];?>">
   </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">url：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm" name="form[url]" value="<?php echo $data['url'];?>">
   </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">显示/隐藏：</label>
	 <div class="col-md-4">
	<label class="radio-inline">
		<input type="radio" name="form[hidden]" value="0" <?php if ($data['hidden'] != '1'){echo 'checked="checked"';}?>/>显示
	 </label>
	 <label class="radio-inline">
		<input type="radio" name="form[hidden]" value="1" <?php if ($data['hidden'] == '1'){echo 'checked="checked"';}?>/>隐藏
	 </label>
	 </div>
  </div>
 <div class="form-group">
    <label class="control-label col-md-2">sort排序值：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm" name="form[sortNum]" value="<?php echo $data['sortNum'];?>">
   </div>
  </div>
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<div class="form-group">
  <div class="col-md-offset-2 col-md-10">
    <button class="btn btn-success">保存</button>
  </div>
</div>
</form>
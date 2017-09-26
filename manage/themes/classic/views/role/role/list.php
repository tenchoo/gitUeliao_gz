<link rel="stylesheet" href="/themes/classic/statics/app/role/css/style.css">
<div class="panel panel-default search-panel">
  <div class="panel-body">
  <form action="<?php echo $this->createUrl('index');?>" class="pull-left form-inline">
	<input type="text" value="<?php echo $roleName;?>" name="name" class="form-control input-sm" placeholder="输入角色名称"/>
	<button class="btn btn-sm btn-default">查找</button>
	</form>
   <div class="pull-right">
    <a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('add');?>">添加角色</a>
	</div>
  </div>
</div>
<br>
<table class="table table-condensed table-bordered">
  <colgroup><col width="100"><col width="30%"><col></colgroup>
  <thead>
    <tr>
	  <td>roleId</td>
      <td>角色</td>
	  <!--td>所属部门</td-->
      <td>管理</td>
    </tr>
  </thead>
</table>
<br>
<table class="table table-condensed table-bordered  table-hover">
   <colgroup><col width="100"><col width="30%"><col></colgroup>
  <tbody>
    <?php foreach( $list as $item ):?>
    <tr>
	   <td><?php echo $item['roleId'];?></td>
      <td><?php echo $item['roleName'];?></td>
	  <!--td><?php //echo (isset($departments[$item['departmentId']]))?$departments[$item['departmentId']]:'';?></td-->
      <td>
	  <?php if($item->roleId>1){ ?>
	  <a href="<?php echo $this->createUrl('assign',array('id'=>$item->roleId) );?>">权限设置</a>
	   <?php } ?>
	  <?php if($item->roleId>1){ ?>
      <a href="<?php echo $this->createUrl('edit',array('id'=>$item->roleId) );?>">编辑</a>
	  <a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $item->roleId;?>" data-rel="<?php echo $this->createUrl('del');?>">删除</a>
	   <?php } ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php $this->beginContent('//layouts/_del');$this->endContent();?>
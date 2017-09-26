<link rel="stylesheet" href="/themes/classic/statics/app/role/css/style.css">
<div class="panel panel-default search-panel">
  <div class="panel-body">
  <form action="<?php echo $this->createUrl('index');?>" class="pull-left form-inline">
	<input type="text" value="<?php echo $name;?>" name="name" class="form-control input-sm" placeholder="输入角色组名称"/>
	<button class="btn btn-sm btn-default">查找</button>
	</form>
   <div class="pull-right">
    <a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('add');?>">添加角色组</a>
	</div>
  </div>
</div>
<br>
<table class="table table-striped">
  <thead>
    <tr>
      <td width="20%">角色组</td>
	  <td>所属部门</td>
      <td>管理</td>
    </tr>
  </thead>
  <tbody>
    <?php foreach( $list as $item ): ?>
    <tr>
      <td><?php echo $item->name;?></td>
	  <td><?php echo $departments[$item['departmentId']];?></td>
      <td>
      <a href="<?php echo $this->createUrl('edit',array('id'=>$item->groupId) );?>">编辑</a>
	  <a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $item->groupId;?>" data-rel="<?php echo $this->createUrl('del');?>">删除</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php $this->beginContent('//layouts/_del');$this->endContent();?>
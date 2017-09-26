<link rel="stylesheet" href="/themes/classic/statics/app/product/category/css/style.css">
<div class="panel panel-default search-panel">
  <div class="panel-body">
   <?php
  if( $this->checkAccess('/warehouse/warehouse/add') ){
  ?>
   <div class="pull-right">
    <a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('add');?>">添加仓库</a>
	</div>
  <?php } ?>
  </div>
</div>
<table class="table table-condensed table-bordered  table-hover">
  <thead>
    <tr>
      <td >仓库名称</td>
	  <td >仓库类型</td>
	  <td width="15%">所在地区</td>
	  <td width="15%">默认打印机</td>
	  <td width="15%">新建时间</td>
      <td width="30%">管理</td>
    </tr>
  </thead>
  <tbody>
    <?php foreach( $list as $item ): ?>
    <tr>
      <td><?php echo $item['title'];?></td>
	  <td><?php echo $item['type'];?></td>
	  <td><?php echo $item['areaId'];?></td>
	  <td><?php echo $item['printer'];?></td>
	  <td><?php echo $item['createTime'];?></td>
      <td>
		<a href="<?php echo $this->createUrl('position',array('warehouseId'=>$item['warehouseId']) );?>">分区管理</a>
		&nbsp;
		<a href="<?php echo $this->createUrl('printer',array('warehouseId'=>$item['warehouseId']) );?>">打印机设置</a>
		&nbsp;
		<a href="<?php echo $this->createUrl('manager',array('warehouseId'=>$item['warehouseId']) );?>">仓库管理员</a>
		&nbsp;
        <a href="<?php echo $this->createUrl('edit',array('id'=>$item['warehouseId']) );?>">编辑</a>
		&nbsp;
		<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $item['warehouseId'] ?>" data-rel="<?php echo $this->createUrl('del');?>">删除</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php $this->beginContent('//layouts/_del');$this->endContent();?>
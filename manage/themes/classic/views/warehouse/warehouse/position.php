<?php if( $this->checkAccess('warehouse/warehouse/padd') ):?>
    <div class="panel panel-default search-panel">
        <div class="panel-body">

        <div class="pull-right">
		<?php if( $paramName != 'warehouseId' ){?>
		<a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('position',array('warehouseId' => $wid));?>">返回分区列表</a>
	 <?php  }?>
        <a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('padd',array($paramName => $id));?>">新建<?php echo $name;?></a>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
<table class="table table-condensed table-bordered">
  <thead>
    <tr>
      <td width="15%"><?php echo $name;?>名称</td>
	  <?php if( $paramName == 'warehouseId' ){?>
	   <td width="15%">分区类型</td>
	   <td width="15%">默认打印机</td>
	  <?php }?>	  
	  <td width="15%">新建时间</td>
      <td>管理</td>
    </tr>
  </thead>
  <tbody>
    <?php foreach( $list as $item ): ?>
    <tr>
      <td><?php echo $item['title'];?></td>
	   <?php if( $paramName == 'warehouseId' ){?>
	   <td><?php echo $item['type'];?></td>
	   <td><?php echo $item['printerId'];?></td>
	   <?php }?>	  
	  <td><?php echo $item['createTime'];?></td>
      <td>
	  <?php if( $paramName == 'warehouseId' ){?>
		<a href="<?php echo $this->createUrl('position',array('parentId'=>$item['positionId']) );?>">仓位管理</a>
		&nbsp;
		<a href="<?php echo $this->createUrl('userlist',array('positionId'=>$item['positionId']) );?>">分拣员设置</a>
		&nbsp;
	 <?php  }?>
        <a href="<?php echo $this->createUrl('pedit',array('id'=>$item['positionId']) );?>">编辑</a>
		&nbsp;
		<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $item['positionId'] ?>" data-rel="<?php echo $this->createUrl('pdel');?>">删除</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
<?php $this->beginContent('//layouts/_del');$this->endContent();?>

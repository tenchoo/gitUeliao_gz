<?php if( $isadd ):?>
<div class="panel panel-default search-panel">
  <div class="panel-body">
   <div class="pull-right">
    <a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('setcs');?>">新建客服</a>
	</div>
  </div>
</div>
<?php endif; ?>
<table class="table table-condensed table-bordered table-hover">
  <thead>
    <tr>
    <td>客服名称</td>
	  <td width="20%">号码</td>
	  <td width="20%">类型</td>
	  <td width="20%">状态</td>
	  <td width="10%">操作</td>
    </tr>
  </thead>
  <tbody>
   <?php foreach( $data as $val ):?>
	<tr>
	 <td><?php echo $val['csName'];?></td>
      <td><?php echo $val['csAccount'];?></td>
	  <td><?php echo $val['type'];?></td>
	  <td><?php echo $val['state'];?></td>
	  <td>
		<a href="<?php echo $this->createUrl('setcs',array('id' => $val['csId']));?>">编辑</a>
		<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $val['csId'] ?>" data-rel="<?php echo $this->createUrl('delcs');?>">删除</a>
	  </td>
    </tr>
	<?php endforeach; ?>
	</tbody>
  </table><br/>
 <?php $this->beginContent('//layouts/_del');$this->endContent();?>
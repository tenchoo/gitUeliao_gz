<div class="panel panel-default search-panel">
  <div class="panel-body">
  <form action="<?php echo $this->createUrl('index');?>" class="pull-left form-inline">
	<input type="text" value="<?php echo $stocktakingId;?>" name="stocktakingId" class="form-control input-sm" placeholder="盘点单号"/>
	<input type="text" value="<?php echo $userName;?>" name="userName" class="form-control input-sm" placeholder="盘点人"/>
	<input type="text" value="<?php echo $createTime;?>" name="createTime" class="form-control input-sm" placeholder="盘点时间"/>
	<button class="btn btn-sm btn-default">查找</button>
	</form>
   <div class="pull-right">
    <a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('add');?>">新建盘点单</a>
	</div>
  </div>
</div>
<div class="clearfix well well-sm list-well">
<?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
<table class="table table-condensed table-bordered">
 <colgroup><col width="10%"><col width="10%"><col><col width="16%"><col width="16%"><col width="16%"><col width="16%"></colgroup>
  <thead>
    <tr>
    <td>盘点单号</td>
	<td>产品编码</td>
	  <td>制单人</td>
	  <td>确认人</td>
	  <td>制单时间</td>
	  <td>状态</td>
    <td>操作</td>
    </tr>
  </thead>
</table>
<br>
<table class="table table-condensed table-bordered table-hover">
  <colgroup><col width="10%"><col width="10%"><col><col width="16%"><col width="16%"><col width="16%"><col width="16%"></colgroup>
  <tbody>
    <?php foreach( $list as $item ): ?>
    <tr>
	  <td><?php echo $item->stocktakingId;?></td>
	  <td><?php echo $item->serialNumber;?></td>
      <td><?php echo $item->userName;?></td>
	  <td><?php echo $item->checkUser;?></td>
	  <td><?php echo $item->createTime;?></td>
	  <td><?php if($item->state == '0'){ ?>未确认<?php }else if($item->state == '2'){ ?>已保存<?php }else{ ?>已取消<?php } ?>
	  </td>
      <td>
		<?php if($item->state == '0'){ ?>
		<a href="<?php echo $this->createUrl('add',array('id'=>$item->stocktakingId) );?>">盘点确认</a>
		<?php } ?>
		<a href="<?php echo $this->createUrl('view',array('id'=>$item->stocktakingId) );?>">查看</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<br>
<div class="clearfix well well-sm list-well">
<?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
<?php $this->beginContent('//layouts/_del');$this->endContent();?>
<div class="panel panel-default search-panel">
  <div class="panel-body">
  <form action="<?php echo $this->createUrl('index');?>" class="pull-left form-inline">
	<input type="text" value="<?php echo $plateNumber;?>" name="plateNumber" class="form-control input-sm" placeholder="车牌号"/>
	<button class="btn btn-sm btn-default">查找</button>
	</form>
  <div class="pull-right">
    <a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('add');?>">添加新车辆</a>
	</div>
 </div>
</div>
<div class="clearfix well well-sm list-well">
<?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
<table class="table table-condensed table-bordered">
  <colgroup><col width="50%"><col width="30%"><col width="20%"></colgroup>
  <thead>
    <tr>
      <td>车牌号</td>
	  <td width="20%">添加时间</td>
	   <td width="10%">操作</td>
    </tr>
  </thead>
</table>
<br>
<table class="table table-condensed table-bordered  table-hover">
  <colgroup><col width="50%"><col width="30%"><col width="20%"></colgroup>
  <tbody>
   <?php foreach( $list as $val ):?>
	<tr>
      <td><?php echo $val['plateNumber'];?></td>
	  <td width="10%"><?php echo $val['createTime'];?></td>
	  <td>
		<a href="<?php echo $this->createUrl('edit',array('id' => $val['vehicleId']));?>">编辑</a>
		<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $val['vehicleId'] ?>" data-rel="<?php echo $this->createUrl('del');?>">删除</a>
	  </td>
    </tr>
	<?php endforeach; ?>
	</tbody>
  </table><br/>
<div class="clearfix well well-sm list-well">
<?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>

 <?php $this->beginContent('//layouts/_del');$this->endContent();?>
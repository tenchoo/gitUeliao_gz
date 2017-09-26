
<div class="panel panel-default search-panel">
  <div class="panel-body">
   <div class="pull-right">
    <a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('setprinter');?>">新增打印机</a>
	</div>
  </div>
</div>

<table class="table table-condensed table-bordered">
  <thead>
    <tr>
    <td width="20%">打印机编号</td>
	<td >说明</td>
	<td width="20%">状态</td>
	<td width="10%">操作</td>
    </tr>
  </thead>
  <tbody>
   <?php foreach( $data as $val ):?>
	<tr>
	 <td><?php echo $val['printerSerial'];?></td>
      <td><?php echo $val['mark'];?></td>	  
	  <td><?php echo $val['state'];?></td>
	  <td>
		<a href="<?php echo $this->createUrl('setprinter',array('id' => $val['printerId']));?>">编辑</a>
	  </td>
    </tr>
	<?php endforeach; ?>
	</tbody>
  </table><br/>
<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<div class="panel panel-default">
	<ul class="list-group">
		<li class="list-group-item clearfix">
			<span class="col-md-4">盘点单号：<?php echo $stocktakingId;?></span>
			<span class="col-md-4">建单人：<?php echo $userName;?></span>
			<span class="col-md-4">建单时间：<?php echo $createTime;?></span>
		</li>
		<li class="list-group-item clearfix">
			<span class="col-md-4">盘点仓库：<?php echo $warehouse;?></span>
			<span class="col-md-4">产品编号：<?php echo $serialNumber;?></span>
			<span class="col-md-4">盘点总数量：<?php echo Order::quantityFormat( $total ).' '.$unit;?></span>
		</li>
		<li class="list-group-item clearfix">
			<span class="col-md-4">盘点人：<?php echo $takinger;?></span>
			<span class="col-md-4">确认人：<?php echo $checkUser;?></span>
			<span class="col-md-4">确认时间：<?php echo $updateTime;?></span>
		</li>
	</ul>
</div>

<br>
	<table class="table table-condensed table-bordered order">
   <thead>
    <tr>
	 <td>单品编号</td>
     <td>仓位</td>
	 <td>批次</td>
	 <td>原数量</td>
     <td>盘点数量</td>
	 <td>状态</td>
	  </tr>
	  </thead>
	  <tbody>
	 <?php foreach( $products as $key=>$pval) :?>
     <tr class="order-list-bd <?php if( $pval['state'] == '2'){ echo 'danger';}else if($pval['state'] == '1'){echo 'warning';} ?>">
	 <td><?php echo $pval['singleNumber'];?></td>
	 <td><?php echo $pval['positionTitle'];?></td>
	 <td><?php echo $pval['productBatch'];?></td>
	 <td><?php echo Order::quantityFormat( $pval['oldNum']).' '.$unit;?></td>
     <td><?php echo Order::quantityFormat( $pval['num'] ).' '.$unit;?></td>
	 <td><?php echo $pval['stateTitle'];?></td>
	 </td>
	  </tr>
	<?php endforeach;?>
</table>
<br>
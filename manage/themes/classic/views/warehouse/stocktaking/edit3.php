<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<?php $this->beginContent('//layouts/_error2');$this->endContent();?>
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
		</li>
	</ul>
</div>
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

<form class="form-horizontal" method="post" action="">
<input type="hidden" value="" name="op"/><!-- 取消传值为 cancle ,确定传值为 doSave -->
</form>
<div align="center">
	<button class="btn btn-danger btn-comfirm" data-op="cancle"><span class="glyphicon glyphicon-remove"></span>取消盘点</button>
	<?php if( !$abnormity ){ ?>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<button class="btn btn-success btn-comfirm" data-op="doSave"><span class="glyphicon glyphicon-ok"></span>确定盘点</button>
	<?php }?>
</div>
<br><br>
<script>
seajs.use('statics/app/warehouse/js/stocktaking.js');
</script>
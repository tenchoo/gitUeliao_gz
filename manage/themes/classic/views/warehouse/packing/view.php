<div class="panel panel-default">
    <div class="panel-heading clearfix">
	<span class="col-md-4">订单编号：<?php echo $orderId;?>
	<?php if($orderState == '7'){?>
			<span class="text-danger">（订单已取消）</span>
	<?php }?>
	</span>
	<span class="col-md-4">下单日期：<?php echo $createTime;?></span>
	<span class="col-md-4">提货方式：<?php echo $deliveryMethod;?></span>
	</div>
	 <ul class="list-group">
	<li class="list-group-item clearfix">
	<span class="col-md-4">分拣编号：<?php echo $packingId;?> </span>
	<span class="col-md-4">分拣日期：<?php echo $packingTime;?></span>
	<span class="col-md-4">分拣单提交人：<?php echo $operator;?></span>
	</li>
	<li class="list-group-item clearfix">
		<span class="col-md-4">分配单号：<?php echo $distributionId;?></span>
		<span class="col-md-4">分配时间：<?php echo $distributionTime;?></span>
		<span class="col-md-4">分配人：<?php echo $distributioner;?></span>
	</li>
	<li class="list-group-item clearfix">
		<span class="col-md-12">分拣仓库：<?php echo $warehouse;?></span>
	</li>
	<li class="list-group-item clearfix">
		<span class="col-md-12">调拨发货仓库：<?php echo $deliveryWarehouse;?></span>
	</li>
    </ul>
  </div>
   <br/>
  <table class="table table-condensed table-bordered">
   <thead>
    <tr>
	 <td>产品编号</td>
	 <td>颜色</td>
	 <td>仓位</td>
     <td>产品批次</td>
	 <td>分配数量</td>
	</tr>
	  </thead>
	  <tbody>
	   <?php
		foreach( $distribution as $dval) :
			$count = count($dval['detail']);
			foreach( $dval['detail'] as $key=>$deval) :
		?>
	   <tr>
	   <?php if($key=='0'):?>
	   <td rowspan="<?php echo $count;?>"><?php echo $dval['singleNumber'];?></td>
	   <td rowspan="<?php echo $count;?>"><?php echo $dval['color'];?></td>
	   <?php endif;?>
	   <td><?php echo $deval['positionTitle'];?></td>
	   <td><?php echo $deval['productBatch'];?></td>
	   <td><?php echo Order::quantityFormat( $deval['distributionNum'] );?></td>
	   </tr>
	  <?php endforeach;?>
	<?php endforeach;?>
	 </table><br/> <br/>
	<table class="table table-condensed table-bordered">
	 <colgroup><col width=""><col width="30%"><col width="30%"></colgroup>
   <thead>
    <tr>
	 <td>仓位号</td>
	 <td width="30%">产品批次</td>
     <td width="30%">分拣数量</td>
	  </tr>
	  </thead>
	  </table>
	  <br>

<?php foreach( $distribution as $dval) : ?>
<table class="table table-condensed table-bordered">
	<tbody>
	<tr class="list-hd">
	 <td colspan="4">
		<span class="first">产品编号:<?php echo $dval['singleNumber'];?></span>
		<span>颜色:<?php echo $dval['color'];?></span>
		<span>分配数量：<?php echo Order::quantityFormat( $dval['total'] );?> <?php echo $dval['unit'];?></span>
	</td>
	</tr>
<?php
if(isset($detail[$dval['orderProductId']]) && is_array($detail[$dval['orderProductId']])):
	foreach( $detail[$dval['orderProductId']] as $val) :
		foreach( $val['pack'] as  $pval) :
?>
	<tr class="order-list-bd">
	<td><?php echo $val['positionTitle'];?></td>
	<td width="30%"><?php echo $pval['productBatch'];?></td>
    <td width="30%"><?php echo Order::quantityFormat( $pval['packingNum'] );?> <?php echo $dval['unit'];?> </td>
 </tr>
<?php endforeach;?>
<?php endforeach;?>
<?php endif;?>
 </table><br/>
<?php endforeach;?>
<br/>
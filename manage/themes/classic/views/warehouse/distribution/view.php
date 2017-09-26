<div class="panel panel-default">
  <div class="panel-heading clearfix">
		<span class="col-md-4"><?php echo $orderType;?>：<?php echo $orderId;?></span>
		<span class="col-md-4">下单日期：<?php echo $orderTime;?></span>
		<span class="col-md-4">业务员：<?php echo $salesman;?></span>
	</div>
  <ul class="list-group">
    <li class="list-group-item clearfix">
      <span class="col-md-4">分配人：<?php echo $operator;?> </span>
		  <span class="col-md-4">分配日期：<?php echo $distributionTime;?></span>
		  <span class="col-md-4">提货方式：<?php echo $deliveryMethod;?></span>
    </li>
	  <li class="list-group-item clearfix">
			<span class="col-md-12">收货地址：
			<?php echo $address;?> ( <?php echo $name;?>  收 ) <?php echo $tel;?>
			</span>
		</li>
	    <li class="list-group-item clearfix">
			<span class="col-md-12">备注：<?php echo $memo;?></span>
		</li>
		<li class="list-group-item clearfix">
			<span class="col-md-12">发货仓库：<?php echo $deliveryWarehouse;?></span>
		</li>
    </ul>
  </div>
  <br>
	<table class="table table-condensed table-bordered">
	 <colgroup><col width="20%><col width="20%"><col width="20%"><col width="20%"><col width="20%"></colgroup>
   <thead>
    <tr>
	 <td>仓库</td>
	 <td>仓位</td>
     <td>产品批次</td>
     <td>分配数量</td>
	  <td>分配分拣员</td>
	  </tr>
	  </thead>
	</table>
	<br>

	<?php foreach(  $products as $pro ) :?>
	<table class="table table-condensed table-bordered">
	  <colgroup><col width="20%><col width="20%"><col width="20%"><col width="20%"><col width="20%"></colgroup>
	  <tbody>
	<tr>
		<td colspan="5" class="list-hd">
		<span class="first">产品编号:<?php echo $pro['singleNumber'];?></span>
		<span>颜色：<?php echo $pro['color'];?></span>
		<span>购买数量：<?php echo Order::quantityFormat( $pro['num'] );?> 码</span>
		</td>
	</tr>
	<?php foreach(  $pro['detail'] as $dval) : ?>
	<tr>
		<td ><?php echo  $dval['warehouse'];?></td>
		<td ><?php echo  $dval['positionTitle'];?></td>
		<td><?php echo $dval['productBatch'];?></td>
		<td><?php echo Order::quantityFormat( $dval['distributionNum'] );?></td>
		<td><?php echo $dval['packinger'];?><br/></td>
	</tr>
	<?php endforeach;?>
	</tbody>
	</table>
 <br/>
	<?php endforeach;?>
  <br/>
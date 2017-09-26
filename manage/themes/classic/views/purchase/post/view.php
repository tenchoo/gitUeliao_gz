<div class="panel panel-default">
  <div class="panel-heading clearfix">
    <span class="col-md-4">发货单号：<?php echo $orderPost->postId;?> </span>
    <span class="col-md-4">单据来源：
	<?php if ($orderPost->orderType == '1'){
			$op = tbUser::model()->getUsername($orderPost->userId);
			echo '采购创建 ( 填单人: '.$op.' )';
		}else{
			echo '工厂创建';
		} ?> </span>
    <span class="col-md-4">发货时间：<?php echo $orderPost->createTime;?></span>
  </div>
  <ul class="list-group">
	<li class="list-group-item clearfix">
		 <span class="col-md-4">采购单号：<?php echo $purchase->purchaseId;?> </span>
		<span class="col-md-4">采购日期：<?php echo date('Y-m-d',$purchase->createTime);?> </span>
		<span class="col-md-4">采购人员：<?php echo tbUser::model()->getUsername($purchase->userId);?></span>
	  </li>
	  <li class="list-group-item clearfix">
		  <span class="col-md-4">工厂编号：<?php echo $purchase->supplierSerial;?></span>
		  <span class="col-md-4">工厂名称：<?php echo $purchase->supplierName;?></span>
		  <span class="col-md-4">工厂联系人：<?php echo $purchase->supplierContact;?></span>
	  </li>
	  <li class="list-group-item clearfix">
	    <span class="col-md-12">工厂联系电话：<?php echo $purchase->supplierPhone;?></span>
	  </li>
	  <li class="list-group-item clearfix">
	    <span class="col-md-12">收货地址：<?php echo $purchase->address;?></span>
	  </li>
	  <li class="list-group-item clearfix">
	    <span class="col-md-12">订单备注：<?php echo $purchase->comment;?></span>
	  </li>
	</ul>
</div>
<br>

<table class="table table-condensed table-bordered">
	<thead>
		<tr>
			<td>工厂产品编号</td>
			<td>产品编号</td>
			<td>颜色</td>
			<td>采购数量</td>
			<td>发货数量</td>
		</tr>
	</thead>
	
	<tbody>
<?php foreach( $products as $product ):
	$detail = $product['detail'];
	$unit = ZOrderHelper::getUnitName( $detail['productCode'] );
	?>
	<tr>
		<td><?php echo $detail['supplierCode'];?></td>
		<td><?php echo $detail['productCode'];?></td>
		<td><?php echo $detail['color'];?></td>
		<td><?php echo Order::quantityFormat($detail['quantity']).$unit;?></td>
		<td><?php echo Order::quantityFormat($product['postTotal']).$unit;?></td>
	</tr>
<?php endforeach;?>
	</tbody>
</table>
<br>

<table class="table table-condensed table-bordered">
	<thead>
		<tr class="list-hd">
			<td><span class="first">发货单号: <?php echo $orderPost->logisticsCode;?></span><span>发货时间: <?php echo $orderPost->postTime;?></span></td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
			  <br>
			  &nbsp;&nbsp;&nbsp;&nbsp;<span>物流公司: <?php echo $orderPost->logisticsName;?></span><br>
			  &nbsp;&nbsp;&nbsp;&nbsp;<span>物流单号：<?php echo $orderPost->logisticsCode;?></span><br>
			  &nbsp;&nbsp;&nbsp;&nbsp;
			</td>
		</tr>
	</tbody>
</table>
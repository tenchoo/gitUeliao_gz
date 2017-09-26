<div class="panel panel-default">
  <div class="panel-heading clearfix">
	 <span class="col-md-4">采购单号:<?php echo $order->purchaseId;?></span>
	 <span class="col-md-4">采购日期：<?php echo date('Y-m-d H:i', $order->createTime);?></span>
	 <span class="col-md-4">采购人：<?php echo tbUser::model()->getUsername($order->userId);?></span>
	</div>
	<ul class="list-group">
	  <li class="list-group-item clearfix">
		  <span class="col-md-4">工厂编号：<?php echo $order->supplierSerial;?></span>
		  <span class="col-md-4">工厂名称：<?php echo $order->supplierName;?></span>
		  <span class="col-md-4">工厂联系人：<?php echo $order->supplierContact;?></span>
	  </li>
	  <li class="list-group-item clearfix">
	    <span class="col-md-12">工厂联系电话：<?php echo $order->supplierPhone;?></span>
	  </li>
	  <li class="list-group-item clearfix">
	    <span class="col-md-12">收货地址：<?php echo $order->address;?></span>
	  </li>
	  <li class="list-group-item clearfix">
	    <span class="col-md-12">订单备注：<?php echo htmlspecialchars($order->comment);?></span>
	  </li>
	</ul>
</div>
<br>
<table class="table table-condensed table-bordered">
	<thead>
		<tr class="list-hd">
			<td width="10%">产品编号</td>
			<td width="10%">颜色</td>
			<td width="10%">工厂产品编号</td>
			<td width="10%">交货日期</td>
			<td width="15%">来源单号</td>
			<td width="15%">采购数量</td>
			<td width="15%">备注</td>
			<td width="15%">合计</td>
		</tr>
	</thead>
	<tbody>
<?php
foreach( $order->getProducts() as $product ) {
	$ditails = $product->getProducts();
	$unit    = ZOrderHelper::getUnitName( $product->productCode );
	$tableView = new MagicTableRow('产品编号','颜色','工厂产品编号','交货日期','来源单号','采购数量','备注','合计');
	foreach( $ditails as $detail ) {
		$tableView->appendRow(
			$product->productCode,
			$product->color,
			$product->supplierCode,
			$product->deliveryDate,
			$detail->orderId,
			$detail->quantity.$unit,
			$detail->comment,
			$product->quantity
		);
	}
	$tableView->show();
}
?>
	</tbody>
</table>
<br>
<?php if( !empty( $close ) ) { ?>
	<div class="panel panel-default">
  <div class="panel-heading clearfix">
	 <span class="col-md-4">取消记录</span>
	</div>
	<ul class="list-group">
	  <li class="list-group-item clearfix">
		  <span class="col-md-4">操作人：<?php echo $close['operation'];?></span>
		  <span class="col-md-4">取消时间：<?php echo $close['createTime'];?></span>
	  </li>
	  <li class="list-group-item clearfix">
	    <span class="col-md-12">取消原因：<?php echo $close['reason'];?></span>
	  </li>
	</ul>
</div>
<?php } ?>
<div class="panel panel-default">
	<div class="panel-heading clearfix">
		<span class="col-md-4">留货订单：<?php echo $order->orderId; ?></span>
		<span class="col-md-4">下单时间：<?php echo $order->createTime; ?></span>
		<span class="col-md-4">跟单业务业：<?php echo $order->userName; ?></span>
	</div>
	<ul class="list-group">
		<?php if(!empty( $check->userId ) ){ ?>
		<li class="list-group-item clearfix">
			<span class="col-md-4">审核状态：<?php echo $check->stateTitle(); ?></span>
			<span class="col-md-4">审核时间：<?php echo date('Y-m-d H:i:s',$check->expireTime); ?></span>
			<span class="col-md-4">审核人：<?php echo $check->userName();?></span>
		</li>
		<li class="list-group-item clearfix"><span class="col-md-12">审核理由：<?php echo $check->cause; ?></span></li>
		<?php }?>
		<?php if( $check->buyState == '1' ){ ?>
		<li class="list-group-item clearfix">
			<span class="col-md-4">确定购买：已确定购买</span>
			<span class="col-md-4">确定购买时间：<?php echo date('Y-m-d H:i:s',$check->buyTime); ?></span>
		</li>
		<?php }?>
		<li class="list-group-item clearfix">
			<span class="col-md-4">客户名称：<?php echo $member['companyname']; ?></span>
			<span class="col-md-4">联系人：<?php echo $order->name; ?>（<?php echo $order->tel; ?>）</span>
			<span class="col-md-4">付款方式：<?php echo $order->orderPayMode->paymentTitle;?></span>
		</li>
		<li class="list-group-item clearfix"><span class="col-md-12">收货地址：<?php echo $order->address; ?></span>
		</li>
		<li class="list-group-item clearfix"><span class="col-md-12">备注：<?php echo $order->memo; ?></span>
		</li>
	</ul>
</div>

<table class="table table-condensed table-bordered order">
	<thead>
		<tr class="list-hd">
			<td>产品编号</td>
			<td>颜色</td>
			<td>单价（元）</td>
			<td>购买数量</td>
			<td>小计（元）</td>
		</tr>
	</thead>
	<tbody>
<?php
	$priceTotal = 0;
	foreach ( $products as $product ) {
		$price = bcmul( $product->price,$product->num ,2);
		$priceTotal = bcadd ( $price,$priceTotal,2);
?>
            <tr>
			<td><?php echo $product->singleNumber; ?></td>
			<td><?php echo $product->color; ?></td>
			<td><?php echo Order::priceFormat($product->price); ?></td>
			<td><?php echo Order::quantityFormat($product->num); ?></td>
			<td><?php echo Order::priceFormat($price); ?></td>
		</tr>
        <?php } ?>
        <tr>
			<td colspan="5" align="right">
				运费：<?php echo $order->freight; ?> &nbsp;&nbsp;&nbsp;
				总额：<?php echo Order::priceFormat($priceTotal); ?>
			</td>
		</tr>
	</tbody>
</table>
<br>

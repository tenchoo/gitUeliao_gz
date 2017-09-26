<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<div class="panel panel-default">
	<div class="panel-heading clearfix">
	 <span class="col-md-4">订单编码：<?php echo $orderId;?></span>
	 <span class="col-md-4">下单日期：<?php echo $orderTime;?></span>
	</div>
	<ul class="list-group">
	 <li class="list-group-item clearfix">
		<span class="col-md-4">客户名称：<?php echo $companyname;?></span>
		<span class="col-md-4">提货方式：<?php echo $deliveryMethod;?></span>
		<span class="col-md-4">发货仓库：<?php echo $Dwarehouse;?></span>
	 </li>
	</ul>
</div>
<table class="table table-condensed table-bordered order order-detail">
	<thead>
    <tr>
	 <td>产品编号</td>
	 <td>购买数量</td>
	 <td>备货总数量</td>
	 <td>备货说明</td>
	</tr>
    </thead>
    <tbody class="list-page-body">
    <?php foreach( $products as $pval) :?>
    <tr class="list-body-bd">
    <td><?php echo $pval['singleNumber'];?></td>
	<td><?php echo  Order::quantityFormat( $pval['num'] );?> </td>
	<td><?php echo  Order::quantityFormat( $pval['packingNums'] );?></td>
	<td><?php echo $pval['remarks'];?></td>
    </tr>
     <?php endforeach;?>
    </tbody>
</table>
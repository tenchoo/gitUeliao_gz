<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<div class="panel panel-default">
	<div class="panel-heading clearfix">
	 <span class="col-md-4">申请单号：<?php echo $applyInfo['id'];?></span>
	 <span class="col-md-4">申请时间：<?php echo $applyInfo['createTime'];?></span>
	 <span class="col-md-4">申请人：<?php echo $applyInfo['originator'];?></span>
	</div>
	<ul class="list-group">
	 <li class="list-group-item clearfix">
		<span class="col-md-4">审核结果：<?php echo $applyInfo['state'];?></span>
		<span class="col-md-4">审核时间：<?php echo $applyInfo['checkTime'];?></span>
		<span class="col-md-4">审核人：<?php echo $applyInfo['checkUser'];?></span>
	 </li>
	</ul>
</div>
<br>
<div class="panel panel-default">
	<div class="panel-heading clearfix">
	 <span class="col-md-4"><?php echo $model->orderType;?>：<?php echo $model->orderId;?></span>
	 <span class="col-md-4">下单日期：<?php echo $model->createTime;?></span>
	 <span class="col-md-4">业务员：<?php echo $member['salesman'];?></span>
	</div>
	<ul class="list-group">
	 <li class="list-group-item clearfix">
		<span class="col-md-4">客户名称：<?php echo $member['companyname'];?></span>
		<span class="col-md-4">提货方式：<?php echo $model->deliveryMethod;?></span>
		<span class="col-md-4">支付方式：<?php echo $model->payModel;?></span>
	 </li>
	 <li class="list-group-item clearfix">
		<span class="col-md-4">联系人：<?php echo $model->name;?> （<?php echo $model->tel;?>）</span>
		<span class="col-md-4">收货地址：<?php echo $model->address;?></span>
	 </li>
	 <li class="list-group-item clearfix">
		<span class="col-md-12">订单备注：<?php echo $model->memo;?></span>
	 </li>
	</ul>
</div>

<table class="table table-condensed table-bordered order">
	<thead>
    <tr>
	 <td>产品编号</td>
	 <td>颜色</td>
	 <td>数量</td>
	 <td>单价（元）</td>
     <td>申请价格</td>
	</tr>
    </thead>
    <tbody class="list-page-body">
    <?php foreach( $model->products as $pval) :
		if( isset( $applyPrice[$pval->orderProductId]) ) :
	?>
    <tr class="list-body-bd">
   <td ><?php echo $pval['singleNumber'];?></td>
	<td ><?php echo $pval['color'];?></td>
	<td><?php echo Order::quantityFormat($pval['num']);?> </td>
	 <td> <?php echo Order::priceFormat($pval['salesPrice']);?></td>
	<td><?php echo Order::priceFormat($applyPrice[$pval->orderProductId]);?></td>
    </tr>
	<?php endif;?>
     <?php endforeach;?>
    </tbody>
</table>
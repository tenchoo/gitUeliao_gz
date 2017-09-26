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
	 <td>备货总数量</td>
	 <td>备货说明</td>
	</tr>
    </thead>
    <tbody class="list-page-body">
    <?php foreach( $products as $pval) :?>
    <tr class="list-body-bd">
    <td><?php echo $pval['singleNumber'];?> &nbsp;<?php echo $pval['color'];?></td>
	<td><?php echo  Order::quantityFormat( $pval['packingNum'] );?></td>
	<td><?php echo $pval['remark'];?></td>
    </tr>
     <?php endforeach;?>
    </tbody>
</table>
 <?php $this->beginContent('//layouts/_error2');$this->endContent();?>
<form action="" METHOD="POST">
<div class="panel panel-default">
	<ul class="list-group">
		<li class="list-group-item clearfix">
			<span class="col-md-12">目标仓库：
      <span><?php  echo $Dwarehouse; ?></span>&nbsp;&nbsp;
			 <?php echo CHtml::dropDownList('driverId','',$drivers,array('empty'=>'请选择驾驶员','class'=>'form-control input-sm'))?>&nbsp;&nbsp;
			  <?php echo CHtml::dropDownList('vehicleId','',$vehicle,array('empty'=>'请选择车辆','class'=>'form-control input-sm'))?>&nbsp;&nbsp;
			<button class="btn btn-success">确定调拨</button>
			</span>
		</li>
	</ul>
</div>
 </form>
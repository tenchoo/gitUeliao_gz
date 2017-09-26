<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<div class="panel panel-default">
    <div class="panel-heading clearfix">
      <span class="col-md-4">发货单号：<?php echo $deliveryId;?></span>
			<span class="col-md-4">发货时间：<?php echo $createTime;?></span>
			<span class="col-md-4">操作人：<?php echo $operator;?></span>
    </div>
    <ul class="list-group">
		<li class="list-group-item clearfix">
			<span class="col-md-4">客户名称：<?php echo $member['companyname'];?></span>
			<span class="col-md-4">联系人：<?php echo $name;?></span>
		</li>
	    <li class="list-group-item clearfix">
			<span class="col-md-4">联系电话：<?php echo $tel;?></span>
			<span class="col-md-4">地址：<?php echo $member['address'];?></span>
		</li>
	    <li class="list-group-item clearfix">
			<span class="col-md-12">收货地址：<?php echo $address;?></span>
		</li>
	    <li class="list-group-item clearfix">
			<span class="col-md-12">备注：<?php echo $memo;?></span>
		</li>
    </ul>
  </div>
<form  method="post" action="">
	<table class="table table-condensed table-bordered order">
   <thead>
    <tr>
	 <td>产品编号</td>
	 <td>产品信息</td>
	 <td>颜色</td>
	 <td>订单编号</td>
	 <td>数量</td>
	  </tr>
	  </thead>
	  <tbody>
	 <?php foreach( $products as $pval) :?>
	  <tr>
     <tr class="order-list-bd">
	 <td><?php echo $pval['singleNumber'];?></td>
	 <td><?php echo $pval['title'];?></td>
	 <td><?php echo $pval['color'];?></td>
	 <td><?php echo $orderId;?></td>
	 <td><?php echo Order::quantityFormat( $pval['packingNum'] );?> 码</td>
	  </tr>
	<?php endforeach;?>
	 </table>
	  </form>
	 <br/>
	<div class="panel panel-default">
    <ul class="list-group">
		<li class="list-group-item clearfix">
			<span class="col-md-4">物流公司：<?php echo $logistics['com'];?></span>
			<span class="col-md-4">物流编号：<?php echo $logistics['logisticsNo'];?></span>
		</li>
	    <li class="list-group-item clearfix">
			<span class="col-md-12">物流信息:
			<ul class="list-group">
		<?php if( isset($logistics['detail']) && is_array ($logistics['detail']) ){
			foreach( $logistics['detail'] as $dval ){ ?>
	    <li class="list-group-item clearfix">
			<?php echo $dval['time'];?> <?php echo $dval['context'];?>
		</li>
			<?php }}else{ ?>
		 <li class="list-group-item clearfix">
			暂无物流信息
		</li>
		<?php } ?>
    </ul></span>
		</li>
    </ul>
  </div>

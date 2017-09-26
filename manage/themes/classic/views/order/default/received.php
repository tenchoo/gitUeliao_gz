<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<form  method="post" action="">
	<table class="table table-condensed table-bordered order">
   <thead>
    <tr><td colspan="5">订单编号：<?php echo $products['0']['orderId'];?></td></tr>
    <tr>
	 <td>产品编号</td>
	 <td>颜色</td>
     <td>单价</td>
	 <td>发货数量</td>
	 <td>收货数量</td>
	  </tr>
	  </thead>
	  <tbody>
	 <?php foreach( $products as $pval) :
		$pval['deliveryNum'] = Order::quantityFormat( $pval['deliveryNum'] );
	 ?>
	  <tr>
     <tr class="order-list-bd">
	 <td><?php echo $pval['singleNumber'];?></td>
	 <td><?php  echo $pval['color'];?></td>
	 <td><?php echo Order::priceFormat($pval['price']);?></td>
     <td><?php echo $pval['deliveryNum'];?> <?php echo (isset($units[$pval['productId']]))?$units[$pval['productId']]['unit']:''?></td>
	 <td><input type="text" name="data[<?php echo $pval['orderProductId']?>]"
		value="<?php echo (isset($dataArr[$pval['orderProductId']]))?$dataArr[$pval['orderProductId']]:$pval['deliveryNum'];?>"/> <?php echo (isset($units[$pval['productId']]))?$units[$pval['productId']]['unit']:''?></td>
	  </tr>
	<?php endforeach;?>
	 </table>
	 <br/>
	 <?php $this->beginContent('//layouts/_error');$this->endContent();?><br/>
	<div align="center">
		<button class="btn btn-success">确认收货</button>
	</div>
 </form>
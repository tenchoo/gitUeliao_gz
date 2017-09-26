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
	 <li class="list-group-item clearfix">
		<span class="col-md-4">收货人：<?php echo $order['name'];?></span>
		<span class="col-md-4">联系电话：<?php echo $order['tel'];?></span>
	 </li>
	  <li class="list-group-item clearfix">
		<span class="col-md-12">收货地址：<?php echo $order['address'];?></span>
	 </li>
	  <li class="list-group-item clearfix">
		<span class="col-md-12">订单留言：<?php echo $order['memo'];?></span>
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
<?php if( $order['isRecognition'] == '0' ){ ?>
<div class="clearfix alert alert-danger">
  <strong class="alert-danger">待财务确认</strong>
</div>
<?php }else{ ?>
 <?php $this->beginContent('//layouts/_error2');$this->endContent();?>
<form  method="post" action="">
	 <br/>
	 <input type="hidden" name ="confirmpay" value="1"/>
	<div align="center">
    <button type="button" class="btn btn-success">发货</button>
	</div>
 </form>
<script>
  $('.btn-success').on('click',function(){
    if(confirm('确定发货吗？')){
      $('form').trigger('submit');
    }
  });
</script>
<?php }?>
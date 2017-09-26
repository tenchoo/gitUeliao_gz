<div class="panel panel-default">
	<div class="panel-heading clearfix">
	 <span class="col-md-4">结算单号：<?php echo $model->settlementId;?></span>
	 <span class="col-md-4">出单日期：<?php echo $model->createTime;?></span>
	 <span class="col-md-4">出单人：<?php echo $originator;?></span>
	</div>
	<ul class="list-group">
	 <li class="list-group-item clearfix">
		<span class="col-md-4"><?php echo $orderModel->orderType;?>：<?php echo $orderModel->orderId;?></span>
	 <span class="col-md-4">下单日期：<?php echo $orderModel->createTime;?></span>
	 <span class="col-md-4">业务员：<?php echo $member['salesman'];?></span>
	 </li>
	 <li class="list-group-item clearfix">
		<span class="col-md-4">客户名称：<?php echo $member['companyname'];?></span>
		<span class="col-md-4">提货方式：<?php echo $orderModel->deliveryMethod;?></span>
		<span class="col-md-4">支付方式：<?php echo $orderModel->payModel;?></span>
	 </li>
	</ul>
</div>
<br/>
<table class="table table-condensed table-bordered order order-detail">
	<thead>
    <tr>
	 <td>产品编号</td>
	 <td>颜色</td>
	 <td>结算数量</td>
	 <td>单价（元）</td>
	 <td>金额（元）</td>
     <td>是否赠板</td>
	 <td>备注</td>
	</tr>
    </thead>
    <tbody class="list-page-body">
    <?php foreach( $detail as $pval) :	?>
    <tr class="list-body-bd">
    <td><?php echo $pval['singleNumber'];?></td>
	<td><?php echo $pval['color'];?></td>
	<td><?php echo  Order::quantityFormat( $pval['num'] );?> </td>
	<td><?php echo Order::priceFormat($pval['price']);?></td>
	<td><?php echo Order::priceFormat($pval['subprice']);?></td>
	<td><?php echo ($pval['isSample']=='1')?'是':'否';?></td>
	<td><?php echo $pval['remark'];?></td>
    </tr>
     <?php endforeach;?>
    </tbody>
	<tr>
	<td colspan="8" align="right">
		 <span>运费 ：<?php echo $model->freight;?></span>
		 <span>商品金额 ： <?php echo Order::priceFormat($model->productPayments);?></span>
		<span>总金额 ： <?php echo Order::priceFormat($orderModel->realPayment);?></span>
	</td>
	</tr>
</table>
<br>
<form  method="post" action="">
	 <br/>
	 <?php $this->beginContent('//layouts/_error');$this->endContent();?><br/>
	 <input type="hidden" name ="confirmpay" value="1"/>
	<div align="center">
    <button type="button" class="btn btn-success">确认收款</button>
	</div>
 </form>
<script>
  $('.btn-success').on('click',function(){
    if(confirm('确定确认收款？')){
      $('form').trigger('submit');
    }
  });
</script>



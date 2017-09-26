<link rel="stylesheet" href="/themes/classic/statics/app/finance/css/style.css">
 <?php $this->beginContent('//layouts/_error2');$this->endContent();?>
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
	 <li class="list-group-item clearfix">
		<span class="col-md-4">联系人：<?php echo $orderModel->name;?> （<?php echo $orderModel->tel;?>）</span>
		<span class="col-md-4">收货地址：<?php echo $orderModel->address;?></span>
	 </li>
	 <li class="list-group-item clearfix">
		<span class="col-md-12">订单备注：<?php echo $orderModel->memo;?></span>
	 </li>
	 <?php if( !empty($orderModel->warehouseId) ){?>
	 <li class="list-group-item clearfix">
		<span class="col-md-4">发货仓库：<?php echo $orderModel->warehouseId;?></span>
		<span class="col-md-4">是否已发货：<?php echo ($model->state)?'是':'否';?></span>
	 </li>
	 <?php }?>
	</ul>
</div>

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
    <tbody class="list-page-body hide">
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
	<td colspan="8">
		<a href="javascript:;" id="detail">显示/隐藏产品</a>
		 <div class="pull-right form-inline">
		 <span>运费 ：<?php echo $model->freight;?></span>
		 <span>商品金额 ： <?php echo Order::priceFormat($model->productPayments);?></span>
		<span>总金额 ： <?php echo Order::priceFormat($receipts['realPayment']);?></span>
		</div>
	</td>
	</tr>
</table>

<?php $this->beginContent('_receipts',array('receipts'=>$receipts) );$this->endContent();?>
<?php $this->beginContent('_addform',array('receipts'=>$receipts) );$this->endContent();?>

<script>seajs.use('statics/app/finance/js/add.js');</script>
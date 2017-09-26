<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<form  method="post" action="" class="apply-price">
<div class="panel panel-default">
	<div class="panel-heading clearfix">
	 <span class="col-md-4"><?php echo $model->orderType;?>：<?php echo $model->orderId;?></span>
	 <span class="col-md-4">下单日期：<?php echo $model->createTime;?></span>
	 <span class="col-md-4">业务员：<?php echo $member['salesman'];?></span>
	</div>
	<ul class="list-group">
	 <li class="list-group-item clearfix">
		<span class="col-md-4">客户名称：<?php echo $member['companyname'];?></span>
		<span class="col-md-4">联系电话：<?php echo $model->tel;?></span>
		<span class="col-md-4">提货方式：<?php echo $model->deliveryMethod;?></span>
	 </li>
	 <li class="list-group-item clearfix">
		<span class="col-md-12">支付方式：<?php echo $model->payModel;?></span>
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
     <td width="20%">申请价格</td>

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
	<td><input type="text" name="data[<?php echo $pval->orderProductId;?>]" value="<?php echo Order::priceFormat($applyPrice[$pval->orderProductId]);?>" class="form-control input-sm price-only"/></td>
    </tr>
	 <?php endif;?>
     <?php endforeach;?>
    </tbody>
</table><br/>
<?php $this->beginContent('//layouts/_error');$this->endContent();?><br/>
  <div class="text-center">
  <!-- 审核状态：1审核能过，2审核不能过，JS处理值-->
	<input type="hidden" name="state" value="1"/>
	<button class="btn btn-default" type="button">审核不通过</button>
	<button class="btn btn-success" type="button">审核通过</button>
 </div>
 <br>
</div>
</form>
<script>seajs.use('statics/app/order/js/applyprice.js');</script>
<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<form  method="post" action="">
<div class="panel panel-default">
	<div class="panel-heading clearfix">
	 <span class="col-md-4">订单类型：<?php echo $model->orderType;?></span>
	</div>
	<ul class="list-group">
	 <li class="list-group-item clearfix">
		<span class="col-md-4">订单编号：<?php echo $model->orderId;?></span>
		<span class="col-md-4">下单日期：<?php echo $model->createTime;?></span>
		<span class="col-md-4">业务员：<?php echo $member['salesman'];?></span>
	 </li>
	 <li class="list-group-item clearfix">
		<span class="col-md-4">客户名称：<?php echo $member['companyname'];?></span>
		<span class="col-md-4">联系电话：<?php echo $model->tel;?></span>
	 </li>
	 <li class="list-group-item clearfix">
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
	 <td>操作</td>
	</tr>
    </thead>
    <tbody class="list-page-body">
    <?php foreach( $model->products as $pval) :?>
    <tr class="list-body-bd">
   <td ><?php echo $pval['singleNumber'];?></td>
	<td ><?php echo $pval['color'];?></td>
	<td><?php echo Order::quantityFormat($pval['num']);?> </td>
	 <td> <?php echo Order::priceFormat($pval['price']);?></td>
	<td><input type="text" name="data[<?php echo $pval->orderProductId;?>]" value="<?php echo Order::priceFormat($pval['price']);?>" class="form-control input-sm price-only"/> </td>
	<td><a href="javascript:" class="del">删除</a></td>
    </tr>
     <?php endforeach;?>
    </tbody>
</table><br/>
<?php $this->beginContent('//layouts/_error');$this->endContent();?><br/>
  <div class="text-center">
	<button class="btn btn-success" type="submit">提交申请</button>
 </div>
</div>
</form>
<script>seajs.use('statics/app/order/js/orderpriceapply.js')</script>
<link rel="stylesheet" href="/themes/classic/statics/modules/uploader/css/style.css"/>
<div class="panel panel-default">
	 <div class="panel-heading clearfix"><span class="col-md-4">订单类型：<?php echo ($model->orderType == '1')?'订货':'现货';?> </span></div>
    <div class="panel-heading clearfix"><span class="col-md-4">订单编号：<?php echo $model->orderId;?> </span><span class="col-md-4">下单日期：<?php echo $model->createTime;?></span><span class="col-md-4">业务员：<?php echo $member['salesman'];?></span></div>
    <ul class="list-group">
	    <li class="list-group-item clearfix">
			<span class="col-md-4">客户编号：<?php echo $model->memberId;?></span>
			<span class="col-md-4">客户名称：<?php echo $member['companyname'];?></span>
			<span class="col-md-4">联系人：<?php echo $model->name;?></span>
		</li>
	    <li class="list-group-item clearfix">
			<span class="col-md-4">联系电话：<?php echo $model->tel;?></span>
			<span class="col-md-4">地址：<?php echo $member['address'];?></span>
		</li>
	    <li class="list-group-item clearfix">
			<span class="col-md-12">付款方式：<?php echo ($model->payModel)?$payments[$model->payModel]['paymentTitle']:'未付款'?></span>
		</li>
	    <li class="list-group-item clearfix">
			<span class="col-md-12">收货地址：<?php echo $model->address;?> ( <?php echo $model->name;?>  收 ) <?php echo $model->tel;?></span>
		</li>
	    <li class="list-group-item clearfix">
			<span class="col-md-12">备注：<?php echo $model->memo;?></span>
		</li>
    </ul>
  </div>
  <script>seajs.use('statics/app/order/js/order.js');</script>
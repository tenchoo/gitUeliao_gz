<div class="alert alert-danger alert-dismissible fade in" role="alert">

<?php if($applyinfo['state']=='0'){ ?>
	订单已申请修改，待审核
	<?php if( $model->state =='7' ){ ?>
	(订单已关闭)
	<?php }?>
<?php }else if( $applyinfo['state'] == '1'){ ?>
	同意修改订单
<?php }else if( $applyinfo['state'] == '2'){ ?>
	不同意修改订单
	<p>审核反馈：<?php echo $applyinfo['checkInfo'];?></p>
<?php }else if( $applyinfo['state'] == '4'){ ?>
	订单已关闭
<?php }?>
</div>
<div class="panel panel-default">
	<div class="panel-heading clearfix">
	 <span class="col-md-4"><?php echo $model->orderType;?>：<?php echo $model->orderId;?></span>
	 <span class="col-md-4">下单日期：<?php echo $model->createTime;?></span>
	 <span class="col-md-4">跟单业务员：<?php echo $member['salesman'];?></span>
	</div>
	<ul class="list-group">
	 <li class="list-group-item clearfix">
		<span class="col-md-4">客户名称：<?php echo $member['companyname'];?></span>
		<span class="col-md-4">提货方式：<?php echo $model->deliveryMethod;?></span>
		<span class="col-md-4">支付方式：<?php echo ($model->payModel)?$model->payModel:'未付款'?></span>
	 </li>
	 <li class="list-group-item clearfix">
		<span class="col-md-12">联系人：<?php echo $model->name;?>（<?php echo $model->tel;?>）</span>
	 </li>
	 <li class="list-group-item clearfix">
		<span class="col-md-12">订单备注：<?php echo $model->memo;?></span>
	 </li>
	 <?php if( !empty($model->warehouseId) ){?>
	 <li class="list-group-item clearfix">
		<span class="col-md-12">发货仓库：<?php echo $model->warehouseId;?></span>
	 </li>
	 <?php }?>
	</ul>
</div>

<table class="table table-condensed table-bordered order">
	<thead>
    <tr>
	 <td>产品编号</td>
	 <td>颜色</td>
	 <td>改前数量</td>
	 <td>改后数量</td>
	 <td>订单数量</td>
	 <td>单价（元）</td>
	 <td>金额（元）</td>
     <td>是否赠板</td>
	 <td>备注</td>
	</tr>
    </thead>
    <tbody class="list-page-body">
    <?php foreach( $model->products as $pval) : ?>
    <tr class="list-body-bd">
    <td><?php echo $pval['singleNumber'];?></td>
	<td><?php echo $pval['color'];?></td>
	<td><?php echo $applyinfo['detail'][$pval->orderProductId]['oldNum'];?></td>
	<td><?php echo $applyinfo['detail'][$pval->orderProductId]['checkNum'];?></td>

	<td><?php echo Order::quantityFormat($pval['num']);?></td>
	<td><?php echo Order::priceFormat($pval['price']);?></td>
	<td>
		<?php $t = ($pval['isSample']=='1')?'0':$pval['num']*$pval['price'];
			echo Order::priceFormat($t ,2);?>
	</td>
	<td> <?php if($pval['isSample']=='1'){?>是 <?php }else{?>否 <?php }?></td>
	<td><?php echo $pval['remark'];?></td>
    </tr>
     <?php endforeach;?>
    </tbody>
	<tr>
	<td colspan="9" align="right">
		 <span>运费 ：<?php echo $model->freight;?></span>
		 <span>商品金额 ： <?php echo Order::priceFormat($model->realPayment-$model->freight);?></span>
		<span>总金额 ： <?php echo Order::quantityFormat($model->realPayment,2);?></span>
	</td>
	</tr>
</table><br/>
<?php if($model->state =='6'){ ?>
<table class="table table-condensed table-bordered">
	<thead>
    <tr>
	 <td>产品编号</td>
	 <td>颜色</td>
	 <td>购买数量</td>
	 <td>发货数量</td>
	 <td>收货数量</td>
	</tr>
    </thead>
    <tbody class="list-page-body">
    <?php foreach( $model->products as $pval) :?>
    <tr class="list-body-bd">
    <td><?php echo $pval['singleNumber'];?></td>
	<td><?php echo $pval['color'];?></td>
	<td><?php echo Order::quantityFormat($pval['num']);?> </td>
	<td><?php echo Order::quantityFormat($pval['deliveryNum']);?> </td>
	<td><?php echo Order::quantityFormat($pval['receivedNum']);?></td>
    </tr>
     <?php endforeach;?>
    </tbody>
</table>
<?php }?>
<div class="panel panel-default">
	<div class="panel-heading clearfix">
		<span class="col-md-4">分批交货</span>
	</div>
	<ul class="list-group">
	 <?php foreach( $model->batches as $bval) :?>
        <li  class="list-group-item clearfix">
        <span class="col-md-4">交货日期：<?php echo $bval->exprise;?></span>
        <span>备注：<?php echo $bval->remark;?></span>
        </li>
		 <?php endforeach;?>
	</ul>
  </div>
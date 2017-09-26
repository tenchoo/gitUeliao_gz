<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<link rel="stylesheet" href="/themes/classic/statics/modules/uploader/css/style.css"/>

<div class="panel panel-default">
	<div class="panel-heading clearfix">
	 <span class="col-md-4"><?php echo $model->orderType;?>：<?php echo $model->orderId;?></span>
	 <span class="col-md-4">下单日期：<?php echo $model->createTime;?></span>
	 <span class="col-md-4">业务员：<?php echo $member['salesman'];?></span>
	</div>
	<ul class="list-group">
	 <li class="list-group-item clearfix">
		<span class="col-md-4">客户名称：<?php echo $member['companyname'];?></span>
		<span class="col-md-4">提货方式：<?php echo $model->deliveryMethod;?></span>
		<span class="col-md-4">支付方式：<?php echo ($model->payModel)?$payments[$model->payModel]['paymentTitle']:'未付款';?></span>
	 </li>
	 <li class="list-group-item clearfix">
		<span class="col-md-4">联系人：<?php echo $model->name;?> （<?php echo $model->tel;?>）</span>
		<span class="col-md-4">收货地址：<?php echo $model->address;?></span>
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

<table class="table table-condensed table-bordered order order-detail">
	<thead>
    <tr>
	 <td>产品编号</td>
	 <td>颜色</td>
	 <td>购买数量</td>
	 <?php if( $model->state>1 ){?>
	 <td>备货数量</td>
	 <?php if( $model->payState == '3' ){?>
	  <td>结算数量</td>
	 <?php }?>
	 <?php }?>
	 <td>单价（元）</td>
	 <td>金额（元）</td>
     <td>是否赠板</td>
	 <td>备注</td>
	</tr>
    </thead>
    <tbody class="list-page-body">
    <?php foreach( $model->products as $pval) :
		if( $pval['isSample']=='1' ){
			$t = 0;
		}else{
			if( $model->payState == '3' && $model->state!='7' ){
				$t = $pval['deliveryNum']*$pval['price'];
			}else{
				$t = $pval['num']*$pval['price'];
			}
		}
	?>
    <tr class="list-body-bd">
    <td><?php echo $pval['singleNumber'];?></td>
	<td><?php echo $pval['color'];?></td>
	<td><?php echo  Order::quantityFormat( $pval['num'] );?> </td>
	 <?php if($model->state>1){?>
	<td><?php echo  Order::quantityFormat( $pval['packingNum'] );?> </td>
	 <?php if( $model->payState == '3' ){?>
	  <td><?php echo  Order::quantityFormat( $pval['deliveryNum'] );?></td>
	 <?php }?>
	 <?php }?>
	<td><?php echo Order::priceFormat($pval['price']);?></td>
	<td><?php echo Order::priceFormat($t);?></td>
	<td><?php echo ($pval['isSample']=='1')?'是':'否';?></td>
	<td><?php echo $pval['remark'];?></td>
    </tr>
     <?php endforeach;?>
    </tbody>
	<tr>
	<td colspan="8" align="right">
		 <span>运费 ：<?php echo $model->freight;?></span>
		 <span>商品金额 ： <?php echo Order::priceFormat($model->realPayment-$model->freight);?></span>
		<span>总金额 ： <?php echo Order::priceFormat($model->realPayment);?></span>
	</td>
	</tr>
</table>
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
	<td><?php echo Order::quantityFormat( $pval['num'] );?> </td>
	<td><?php echo Order::quantityFormat( $pval['deliveryNum'] );?> </td>
	<td><?php echo Order::quantityFormat( $pval['receivedNum'] );?></td>
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

 <form  method="post" action="">
 <div class="panel panel-default">
	<div class="panel-heading clearfix">
		<span class="col-md-4">订金信息</span>
	</div>
	<ul class="list-group">
        <li  class="list-group-item clearfix">
        <span class="col-md-4">订金金额：<input type="text" name ="amount" value="<?php echo $deposit->amount;?>"/></span>
        <span>尾款：<?php echo bcsub($model->realPayment,$deposit->amount);?></span>
        </li>
	</ul>
  </div>
<?php $this->beginContent('//layouts/_error');$this->endContent();?><br/>
   <div align="center">
	<a class="btn btn-xs" href="javascript:history.go(-1);">取消</a>
	<a class="btn btn-xs" href="<?php echo $this->createUrl('check',array('id'=>$model->orderId));?>">订单审核</a>
	<button type="submit" class="btn btn-xs btn-success">确定</button>
 </div>
</form>
 <script>
  seajs.use('statics/app/order/js/order.js');
</script>
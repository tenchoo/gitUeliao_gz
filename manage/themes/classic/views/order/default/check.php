<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<form  method="post" action="">
<div class="panel panel-default">
	<div class="panel-heading clearfix">
	 <span class="col-md-4"><?php echo $orderType;?>：<?php echo $model->orderId;?></span>
	 <span class="col-md-4">下单日期：<?php echo $model->createTime;?></span>
		<span class="col-md-4">业务员：<?php echo $member['salesman'];?></span>
	</div>
	<ul class="list-group">
	 <li class="list-group-item clearfix">
		<span class="col-md-4">客户名称：<?php echo $member['companyname'];?></span>
		<span class="col-md-4">联系人：<?php echo $model->name;?></span>
		<span class="col-md-4">联系电话：<?php echo $model->tel;?></span>
	 </li>
	 <li class="list-group-item clearfix">
	  <span class="col-md-4">提货方式：
			<?php echo CHtml::dropDownList('data[deliveryMethod]',$model->deliveryMethod,$deliveryMethod,array('class'=>'form-control input-sm'))?></span>
		<span class="col-md-4">支付方式：
			<select name="data[payModel]" class="form-control input-sm">
			<?php foreach ($payModel as $val){?>
				<option value="<?php echo $val['paymentId']?>" <?php if($val['paymentId']==$model->payModel){echo 'selected';} ?>>
					<?php echo $val['paymentTitle']?>
				</option>
			<?php }?>
			</select>
		</span>
	<?php if(!empty($creditInfo)){ ?>
		<span class="col-md-4">可用信用额度：<?php echo $creditInfo['validCredit'];?></span>
	 </li>
	 <?php }?>
	 <li class="list-group-item clearfix">
		<span class="col-md-12">收货地址：<input type="text" value="<?php echo $model->address;?>" name="data[address]" class="form-control input-sm input-note"/></span>
	 </li>
	 <li class="list-group-item clearfix">
		<span class="col-md-12">订单备注：<input type="text" value="<?php echo $model->memo;?>" name="data[memo]"  class="form-control input-sm input-note"/>
		</span>
	 </li>
	</ul>
</div>

<table class="table table-condensed table-bordered order">
	<thead>
    <tr>
	 <td>产品编号</td>
	 <td>颜色</td>
	 <td width="10%">数量</td>
	 <td>单价（元）</td>
   <td>金额（元）</td>
   <td>赠板</td>
	 <td  width="20%">备注</td>
	 <td>操作</td>
	</tr>
    </thead>
    <tbody class="list-page-body">
    <?php
	$products = $model->products;
	$n = count( $products );
	foreach( $products as $pval) :
		$pval['num'] = Order::quantityFormat( $pval['num'] );
	?>
    <tr class="list-body-bd">
    <td ><?php echo $pval['singleNumber'];?></td>
	<td ><?php echo $pval['color'];?></td>
	<?php if($pval->saleType == 'whole'){ ?>
	<td><?php echo $pval['num'];?></td>
	 <td data-unit="<?php echo $pval['price']*100;?>"> <?php echo Order::priceFormat($pval['price']);?></td>
     <td data-price="<?php echo ($pval['isSample']=='1')?0:$pval['num']*$pval['price']*100;?>">
		<?php $t = ($pval['isSample']=='1')?'0':$pval['num']*$pval['price'];
		 echo Order::priceFormat($t);?>
	</td>
	 <td><input type="checkbox" disabled /></td>
	<td><input type="text" name ="data[products][<?php echo $pval->orderProductId;?>][remark]" value="<?php echo $pval['remark'];?>" class="form-control input-sm"/></td>
	<td></td>
	<?php }else{ ?>
	<td><input type="text" name="data[products][<?php echo $pval->orderProductId;?>][num]" value="<?php echo $pval['num'];?>" class="form-control num-float-only input-sm"/> </td>
	 <td data-unit="<?php echo $pval['price']*100;?>"> <?php echo Order::priceFormat($pval['price']);?></td>
     <td data-price="<?php echo ($pval['isSample']=='1')?0:$pval['num']*$pval['price']*100;?>">
		<?php $t = ($pval['isSample']=='1')?'0':$pval['num']*$pval['price'];
		 echo Order::priceFormat($t);?>
	</td>
	 <td><input type="checkbox" name ="data[products][<?php echo $pval->orderProductId;?>][isSample]" value="1" <?php if($pval['isSample']=='1'){echo 'checked';}?> <?php if($pval['num']>4){echo 'disabled';}?>/></td>
	<td><input type="text" name ="data[products][<?php echo $pval->orderProductId;?>][remark]" value="<?php echo $pval['remark'];?>" class="form-control input-sm"/></td>
	<td>
		<?php if( $n > 1 ){ ?><a href="javascript:" class="del-product">删除</a><?php } ?>
	</td>


	<?php } ?>
    </tr>
     <?php endforeach;?>
	<tr>
		<td colspan="8" align="right">
		 <span>运费 ： <input type="text" name="data[freight]" value="<?php echo Order::priceFormat($model->freight);?>" class="form-control input-sm price-only"/></span>
		 <span class="p-total">商品金额 ：<?php echo Order::priceFormat($model->realPayment-$model->freight);?></span>
		<span class="a-total">总金额 ：<?php echo Order::priceFormat($model->realPayment);?></span>
	    </td></tr>
       </tbody>
</table>

 <?php
 $changeDeposit = false;
 if( $model->orderType == '1') {
	$deposit = $model->deposit;
	if( !empty( $deposit ) && $deposit->amount > 0 ){
		if( $deposit->payState == '0' ){
			$changeDeposit = true;
		}
 ?>
 <div class="panel panel-default">
	<div class="panel-heading clearfix">
		<span class="col-md-4">订金信息</span>
	</div>
    <ul class="list-group">
       <li class="list-group-item clearfix">
		<span class="col-md-4">订金金额：<?php echo $deposit->amount;?></span>
		<span class="col-md-4">尾款：<?php echo bcsub($model->realPayment,$deposit->amount);?></span>
		</li>
	</ul>
  </div><br/>
 <?php }}?>
<div class="panel panel-default">
	<div class="panel-heading clearfix">
		<span class="col-md-4">分批交货</span>
	</div>
	<ul class="list-group">
	 <?php foreach( $model->batches as $bval) :?>
        <li  class="list-group-item clearfix">
        <span>交货日期： <input type="text" name="data[batches][exprise][]" value="<?php echo $bval->exprise;?>" class="form-control input-sm input-date" readonly/></span>
        <span>备注：<input type="text" name="data[batches][remark][]" value="<?php echo $bval->remark;?>" class="form-control input-sm input-note"/></span>
			 <a href="javascript:" class="del-batch">删除</a>
        </li>
		 <?php endforeach;?>
		<li class="list-group-item clearfix" ><a href="javascript:" class="add-batch">添加批次</a></li>
	</ul>
  </div><br/>
  <?php $this->beginContent('//layouts/_error');$this->endContent();?><br/>
  <div align="center">
	<a class="btn btn-sm btn-default" href="javascript:history.go(-1);">取消</a>
	<a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('/order/applyprice/apply',array('id'=>$model->orderId));?>">价格申请</a>
	<?php if( $changeDeposit ){ ?>
	<a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('changedeposit',array('id'=>$model->orderId));?>">修改订金</a>
	<?php } ?>
	<button class="btn btn-success">确定</button>
  <br><br>
 </div>
</div>
</form>
<script type="text/html" id="batch">
  <li  class="list-group-item clearfix">
  <span>交货日期： <input type="text" name="data[batches][exprise][]" value="" class="form-control input-sm input-date" readonly/></span>
  <span>备注：<input type="text" name="data[batches][remark][]" value="" class="form-control input-sm input-note"/></span>
  <a href="javascript:" class="del-batch">删除</a>
  </li>
</script>
<script>seajs.use('statics/app/order/js/ordercheck.js')</script>

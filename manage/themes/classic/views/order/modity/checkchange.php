<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<form  method="post" action="">
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
		<span class="col-md-4">支付方式：<?php echo $model->payModel;?></span>
	 </li>
	 <li class="list-group-item clearfix">
		<span class="col-md-12">联系人：<?php echo $model->name;?>（<?php echo $model->tel;?>）</span>
	 </li>
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
	 <td width="10%">改前数量</td>
	 <td width="10%">改后数量</td>
	 <td>单价（元）</td>
   <td>金额（元）</td>
   <td>赠板</td>
	 <td  width="20%">备注</td>
	 <td>操作</td>
	</tr>
    </thead>
    <tbody class="list-page-body">
    <?php $realPayment = 0;
		foreach( $model->products as $pval) :
			$pval['num'] = $data['products'][$pval->orderProductId]['changeNum'];
			$pval['remark'] = $data['products'][$pval->orderProductId]['remark'];
			$t = ($pval['isSample']=='1')?'0':$pval['num']*$pval['price'];
			$realPayment += $t;
	?>
    <tr class="list-body-bd">
    <td ><?php echo $pval['singleNumber'];?></td>
	<td ><?php echo $pval['color'];?></td>
	<td><?php echo $applyinfo[$pval->orderProductId]['oldNum'];?></td>
	<td><input type="text" name="data[products][<?php echo $pval->orderProductId;?>][changeNum]" value="<?php echo Order::quantityFormat($pval['num']);?>" class="form-control num-float-only input-sm"/> </td>
	 <td data-unit="<?php echo $pval['price']*100;?>"> <?php echo Order::priceFormat($pval['price']);?></td>
     <td data-price="<?php echo $t*100;?>">
		<?php echo Order::priceFormat($t);?>
	</td>
	 <td><input type="checkbox" name ="data[products][<?php echo $pval->orderProductId;?>][isSample]" value="1" <?php if($pval['isSample']=='1'){echo 'checked';}?> <?php if($pval['num']>4){echo 'disabled';}?>/></td>
	<td><input type="text" name ="data[products][<?php echo $pval->orderProductId;?>][remark]" value="<?php echo $pval['remark'];?>" class="form-control input-sm"/></td>
	<td><a href="javascript:" class="del-product">删除</a></td>
    </tr>
     <?php endforeach;?>
	<tr>
		<td colspan="9" align="right">
		 <span>运费 ： <input type="text" name="data[freight]" value="<?php echo Order::priceFormat($data['freight']);?>" class="form-control input-sm price-only"/></span>
		 <span class="p-total">商品金额 ：<?php echo Order::priceFormat($realPayment);?></span>
		<span class="a-total">总金额 ：<?php echo Order::priceFormat($realPayment+$data['freight']);?></span>
	    </td></tr>
       </tbody>
</table>
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
  <div class="panel panel-default">
	<div class="panel-heading clearfix">
	 <span class="col-md-4">审核结果： <label class="radio-inline">
		<input type="radio" name="data[state]" value="1" <?php if( $data['state']=='1'){echo 'CHECKED';}?>/>同意修改订单
	 </label>
      <label class="radio-inline">
		<input type="radio" name="data[state]" value="2" <?php if( $data['state']=='2'){echo 'CHECKED';}?>/>不同意修改订单
	  </label></span>
	</div>
	<div class="panel-heading clearfix">
	 <span class="col-md-4">审核反馈：
	  <label class="radio-inline">
	 <textarea name="data[checkInfo]" class="form-control"><?php echo $data['checkInfo'];?></textarea>
	  </label>
	 </span>
	</div>
</div>
  <?php $this->beginContent('//layouts/_error');$this->endContent();?><br/>
  <div align="center">
	<button class="btn btn-success">提交信息</button>
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
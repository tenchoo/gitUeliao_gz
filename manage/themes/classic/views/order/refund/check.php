<div class="panel panel-default">
	<div class="panel-heading clearfix">
	  <span class="col-md-4"><?php echo $orderType;?>：<?php echo $orderId;?></span>
		<span class="col-md-4">下单日期：<?php echo $orderCreateTime;?></span>
		<span class="col-md-4">业务员：<?php echo $member['salesman'];?></span>
	</div>
	<ul class="list-group">
		<li class="list-group-item clearfix">
			<span class="col-md-4">客户名称：<?php echo $member['companyname'];?></span>
			<span class="col-md-4">联系人：<?php echo $member['corporate'].'('.$member['tel'].')';?></span>
		</li>
		<li class="list-group-item clearfix">
			<span class="col-md-4">提货方式：<?php echo $deliveryMethod;?></span>
			<span class="col-md-4">支付方式：<?php echo $payModel;?></span>
		</li>
		<li class="list-group-item clearfix">
			<span class="col-md-12">收货地址：<?php echo $address;?></span></li>
		<li class="list-group-item clearfix">
			<span class="col-md-12">订单备注：<?php echo $memo;?></span></li>
		<li class="list-group-item clearfix">
			<span class="col-md-12">申请退货时间：<?php echo $createTime;?></span></li>
		<li class="list-group-item clearfix">
			<span class="col-md-12">申请退货理由：<?php echo $cause;?></span></li>
	</ul>
</div>
<br>
	<table class="table table-condensed table-bordered">
   <thead>
    <tr>
	 <th>产品编号</th>
	 <th>颜色</th>
	 <th>购买数量</th>
	 <th>退货数量</th>
	 <th>单价（元）</th>
	  <th>赠板</th>
	 <th>金额（元）</th>
	  </tr>
	  </thead>
	  <tbody>
	 <?php  foreach( $products as $pval) :
		 $unit = ZOrderHelper::getUnitName($pval['singleNumber']);
	 ?>
	  <tr>
     <tr class="order-list-bd">
	 <td><?php echo $pval['singleNumber'];?></td>
	 <td><?php echo $pval['color'];?></td>
	 <td><?php echo  Order::quantityFormat( $pval['buynum'] ).$unit;?> </td>
	 <td><?php echo  Order::quantityFormat( $pval['num'] ).$unit;?> </td>
	 <td><?php echo Order::priceFormat($pval['price']);?></td>
	 <td><?php echo $pval['isSample'];?></td>
	 <td><?php echo Order::priceFormat($pval['subprice']);?></td>
	 </td>
	  </tr>
	<?php  endforeach;?>
	 </table>
<br/>

<form method="post" action=""> 
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <span class="col-md-4">审核反馈</span>
        </div>
        <ul class="list-group">
            <li class="list-group-item clearfix">
                <span class="col-md-4"><span class="text-danger">*</span>审核结果：
                    <label class="radio-inline"><input type="radio" name="state" value="pass" checked="checked"/>同意退货</label>
                    <label class="radio-inline"><input type="radio" name="state" value="nopass"/>不同意退货</label>
                </span>
            </li>
            <li class="list-group-item clearfix"><span class="col-md-8"><span class="text-danger">*</span>原因：<label class="radio-inline"><textarea name="cause" class="form-control"></textarea></label></span></li>
        </ul>
    </div>
</form>
	<?php $this->beginContent('//layouts/_error');$this->endContent();?>
    <div class="text-center">
      <input class="btn btn-success" type="submit" value="提交审核"/>
    </div>
<br/>
<div class="panel panel-default">
	<div class="panel-heading clearfix">
	  <span class="col-md-4">操作日志</span>
	</div>
	<ul class="list-group">
	<?php foreach ( $oplog as $val ){?>
		<li class="list-group-item clearfix">
			<span class="col-md-12">
				<?php echo $val['opTime'];?>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<?php echo $val['remark'];?>
			</span>
		</li>
	<?php }?>
	</ul>
</div>
<br>
</div>
<script>
  $('.btn-success').on('click',function(){
    if(confirm('确定提交审核？')){
      $('form').trigger('submit');
    }
  });
</script>
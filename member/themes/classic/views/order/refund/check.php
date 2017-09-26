<link rel="stylesheet" href="/app/member/trade/css/style.css"/>
<div class="pull-right frame-content">
	<div class="order-details-list">
    <div class="hd">
	  <span class="item"><?php echo $orderType;?>：<?php echo $orderId;?></span>
	  <span class="item">下单日期：<?php echo $orderCreateTime;?></span>
    </div>
    <ul class="bd list-unstyled">      <li>
		<span class="item">客户名称：<?php echo $member['companyname'];?></span>
	    <span class="item">联系人：<?php echo $member['corporate'].'('.$member['tel'].')';?></span>
	 </li>
	 <li>
		<span class="item">业务员：<?php echo $member['salesman'];?></span>
		<span class="item">客户名称：<?php echo $member['companyname'];?></span>
	 </li>
	 <li>
        <span class="item">提货方式：<?php echo $deliveryMethod;?></span>
		<span class="item">支付方式：<?php echo $payModel;?></span>
      </li>
	 <li><span>收货地址：<?php echo $address;?></span></li>
   <li class="memo"><span class="pull-left">订单备注：</span><div style="padding-left:60px;"><?php echo $memo;?></div></li>
	 <li><span>申请退货时间：<?php echo $createTime;?></span></li>
	 <li><span>申请退货理由：<?php echo $cause;?></span></li>
    </ul>
	</div>
	<div class="frame-list order-details">
		<div class="frame-list-bd">
			<table>
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
			    <tbody class="list-page-body">
			    <?php foreach( $products as $pval) :	?>
			    <tr class="list-body-bd">
				    <td><?php echo $pval['singleNumber'];?></td>
						<td><?php echo $pval['color'];?></td>
						<td><?php echo  Order::quantityFormat( $pval['buynum'] );?> </td>
						<td><?php echo  Order::quantityFormat( $pval['num'] );?> </td>
						<td><?php echo Order::priceFormat($pval['price']);?></td>
						<td><?php echo $pval['isSample'];?></td>
						<td><?php echo Order::priceFormat($pval['subprice']);?></td>

			    </tr>
			    <?php endforeach;?>
					<tr class="total">
					<td colspan="7">
						<span>退货金额 ： <?php echo Order::priceFormat($realPayment);?></span>
					</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
<form method="post">
  <div style="border:solid 1px #ddd;padding:15px 20px">
  <div class="clearfix">
    <label  class="pull-left">审核结果：</label>
    <div class="pull-left form-group">
      <label class="radio-inline"><input type="radio" name="state" value="pass"/>同意退货</label>
      <label class="radio-inline"><input type="radio" name="state" value="nopass"/>不同意退货</label>
    </div>
  </div>
  <br>
  <div class="clearfix">
    <label class="pull-left">审核反馈：</label>
    <div class="pull-left form-group textarea-group">
      <textarea name="cause" class="form-control" style="height:60px"></textarea>
    </div>
  </div>
  </div>
  <br>
    <div class="text-center">
      <input class="btn btn-success" type="submit" value="提交审核"/>
    </div>
</form>
</div>
<script>
  seajs.use('app/member/trade/js/refundcheck.js');
</script>
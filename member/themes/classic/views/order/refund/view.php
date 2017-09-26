<link rel="stylesheet" href="/app/member/trade/css/style.css"/>
<div class="pull-right frame-content">
	<div class="frame-tab">
	    <ul class="clearfix list-unstyled frame-tab-hd">
	      <li class="active">
	        <a href="javascript:">查看退款单</a>
	      </li>
	    </ul>
	 </div>
	<div class="order-details-list">
    <div class="hd">
      <span class="item">退货单号：<?php echo $refundId;?></span>
	  <span class="item">申请日期：<?php echo $createTime;?></span>

    </div>
    <ul class="bd list-unstyled">
      <li>
			<span class="item"><?php echo $orderType;?>：<?php echo $orderId;?></span>
			<span class="item">下单日期：<?php echo $orderCreateTime;?></span>
	 </li>
	 <li>
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
					<td colspan="8">
						<span>退货金额 ： <?php echo Order::priceFormat($realPayment);?></span>
					</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
<div class="order-details-list">
    <div class="hd">
      <span class="item">操作日志</span>
    </div>
    <ul class="bd list-unstyled">
	<?php foreach ( $oplog as $val ){?>
		<li>
			<span class="item"><?php echo $val['opTime'];?></span>
			<span class="item"><?php echo $val['remark'];?></span>
		</li>
	<?php }?>
    </ul>
	</div>
</div>
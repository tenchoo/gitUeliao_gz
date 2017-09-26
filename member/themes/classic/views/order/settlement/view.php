<link rel="stylesheet" href="/app/member/trade/css/style.css"/>
<div class="pull-right frame-content">
	<div class="frame-tab">
	    <ul class="clearfix list-unstyled frame-tab-hd">
	      <li class="active">
	        <a href="javascript:">查看结算单</a>
	      </li>
	    </ul>
	 </div>
	<div class="order-details-list">
    <div class="hd">
      <span class="item">结算单号：<?php echo $model->settlementId;?></span>
			<span class="item">出单日期：<?php echo $model->createTime;?></span>
			<span class="pull-right">出单人：<?php echo $originator;?></span>
    </div>
    <ul class="bd list-unstyled">
      <li>
				<span class="item"><?php echo $orderModel->orderType;?>：<?php echo $orderModel->orderId;?></span>
			 <span class="item">下单日期：<?php echo $orderModel->createTime;?></span>
			 </li>
			 <li>
			 <span class="item">业务员：<?php echo $member['salesman'];?></span>
				<span class="item">客户名称：<?php echo $member['companyname'];?></span>
				</li>
			 <li>
				<span class="item">提货方式：<?php echo $orderModel->deliveryMethod;?></span>
				<span class="item">支付方式：<?php echo $orderModel->payModel;?></span>
			 </li>
			 <li>
				<span class="item">联系人：<?php echo $orderModel->name;?> （<?php echo $orderModel->tel;?>）</span>
				<span class="item">收货地址：<?php echo $orderModel->address;?></span>
			 </li>
			 <li>
				<span class="col-md-12">订单备注：<?php echo $orderModel->memo;?></span>
			 </li>
			 <?php if( !empty($orderModel->warehouseId) ){?>
			 <li>
				<span class="item">发货仓库：<?php echo $orderModel->warehouseId;?></span>
				<span class="item">是否已发货：<?php echo ($model->state)?'是':'否';?></span>
			 </li>
			 <?php }?>
    </ul>
	</div>
	<div class="frame-list order-details">
		<div class="frame-list-bd">
			<table>
				<thead>
			    <tr>
					 <th>产品编号</th>
					 <th>颜色</th>
					 <th>结算数量</th>
					 <th>单价（元）</th>
					 <th>金额（元）</th>
				   <th>是否赠板</th>
					 <th>备注</th>
					</tr>
			    </thead>
			    <tbody class="list-page-body">
			    <?php foreach( $detail as $pval) :	?>
			    <tr class="list-body-bd">
				    <td><?php echo $pval['singleNumber'];?></td>
						<td><?php echo $pval['color'];?></td>
						<td><?php echo  Order::quantityFormat( $pval['num'] );?> </td>
						<td><?php echo Order::priceFormat($pval['price']);?></td>
						<td><?php echo Order::priceFormat($pval['subprice']);?></td>
						<td><?php echo ($pval['isSample']=='1')?'是':'否';?></td>
						<td><?php echo $pval['remark'];?></td>
			    </tr>
			    <?php endforeach;?>
					<tr class="total">
					<td colspan="8">
						 <span>运费 ：<?php echo $model->freight;?></span>
						 <span>商品金额 ： <?php echo Order::priceFormat($model->productPayments);?></span>
						<span>总金额 ： <?php echo Order::priceFormat($orderModel->realPayment);?></span>
					</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
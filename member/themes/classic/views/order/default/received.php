<link rel="stylesheet" href="/app/member/trade/css/style.css"/>
<div class="pull-right frame-content">
	<div class="frame-tab">
    <ul class="clearfix list-unstyled frame-tab-hd">
      <li class="active">
        <a href="javascript:">确认收货</a>
      </li>
    </ul>
 </div>
   <div class="order-details-list">
          <div class="hd">
            <span class="item"><?php echo $orderType;?>：<?php echo $model->orderId;?></span>
            <span class="item">下单日期：<?php echo $model->createTime;?></span>
          </div>
          <ul class="bd list-unstyled">
            <li>
              <span class="item">客户名称：<?php echo $member['companyname'];?></span>
 			  <span class="item">联系人：<?php echo $model->name;?>（<?php echo $model->tel;?>）</span>
            </li>
            <li>
            <span class="item">提货方式：<?php echo $model->deliveryMethod;?></span>
            <span class="item">支付方式：<?php echo $model->payModel;?>
			<?php if(!empty($model->logistics)){ ?>( <?php echo $model->logistics;?> )<?php }?>
			</span> </li>
            <li><span>收货地址：<?php echo $model->address;?></span></li>
			      <li><span>发货仓库：<?php echo $model->warehouseId;?></span></li>
            <li class="memo"><span class="pull-left">订单备注：</span><div style="padding-left:60px;"><?php echo $model->memo;?></div></li>
          </ul>
        </div>

 <div class="frame-list order-details">
        <div class="frame-list-bd">
<form  method="post" action="">
	<table>
   <thead>
    <tr>
	 <th>产品编号</th>
	 <th>颜色</th>
	 <th>发货数量</th>
	 <th>收货数量</th>
	  </tr>
	  </thead>
	   <tbody class="list-page-body">
	 <?php foreach( $model->products as $pval) :
		$pval['deliveryNum'] = Order::quantityFormat( $pval['deliveryNum'] );
	 ?>
	  <tr class="list-body-bd">
      <td><?php echo $pval['singleNumber'];?></td>
	 <td><?php echo $pval['color'];?></td>
     <td><?php echo $pval['deliveryNum'];?> <?php echo (isset($units[$pval['productId']]))?$units[$pval['productId']]['unit']:''?></td>
	 <td><input type="text" name="data[<?php echo $pval['orderProductId']?>]"
		value="<?php echo (isset($dataArr[$pval['orderProductId']]))?$dataArr[$pval['orderProductId']]:$pval['deliveryNum'];?>"/> <?php echo (isset($units[$pval['productId']]))?$units[$pval['productId']]['unit']:''?></td>
	  </tr>
	  </tr>
	<?php endforeach;?>
	 </table>
	 <br>
	<div align="center">
		<?php if( $error =$this->getError()){ ?>
			<span class="text-warning"><?php echo $error;?></span><br/>
		<?php }?>
		<button class="btn btn-success">确认收货</button>
	</div>
 </form>
  </div>
  </div>
</div>

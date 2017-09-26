<link rel="stylesheet" href="/app/member/trade/css/style.css"/>

<div class="pull-right frame-content order-check">
<div class="frame-tab">
    <ul class="clearfix list-unstyled frame-tab-hd">
      <li class="active">
        <a href="javascript:">订单详情</a>
      </li>
    </ul>
 </div>
<div class="order-status">
<?php if( $model->state =='7' ){ ?>
	<div class="hd">订单已取消，交易关闭</div>
<?php }else{ ?>
	<?php if($applyinfo['state']=='0'){ ?>
	<div class="hd">订单已申请修改，待审核</div>
	<?php }else if( $applyinfo['state'] == '1'){ ?>
	<div class="hd">同意修改订单</div>
	<?php }else if( $applyinfo['state'] == '2'){ ?>
	<div class="hd">不同意修改订单</div>
	<div class="bd">
		<p>审核反馈：<?php echo $applyinfo['checkInfo'];?></p>
	</div>
<?php }}?>
 </div>

        <div class="order-details-list">
          <div class="hd">
            <span class="item"><?php echo $model->orderType;?>：<?php echo $model->orderId;?></span>
            <span class="item">下单日期：<?php echo $model->createTime;?></span>
          </div>
          <ul class="bd list-unstyled">
            <li>
              <span class="item">客户名称：<?php echo $member['companyname'];?></span>
               <span class="item">联系人： <?php echo $model->name;?>（<?php echo $model->tel;?>）</span>
            </li>
            <li>
              <span class="item">提货方式： <?php echo $model->deliveryMethod;?></span>
              <span class="item">支付方式：<?php echo $model->payModel;?></span>
            </li>
            <li>

              <span>收货地址：<?php echo $model->address;?></span>
            </li>
            <li class="memo"><span class="pull-left">订单备注：</span><div style="padding-left:60px;"><?php echo $model->memo;?></div></li>
          </ul>
        </div>
 <div class="frame-list order-details">
        <div class="frame-list-bd">
          <table>
            <thead>
              <tr>
	 <th>产品编号</th>
	 <th>颜色</td>
	 <th>改前数量</th>
	 <th>改后数量</th>
	 <th>单价（元）</th>
     <th>金额（元）</th>
     <th>赠板</th>
	 <th>备注</th>
	</tr>
    </thead>
    <tbody class="list-page-body">
    <?php foreach( $model->products as $pval) :?>
    <tr class="list-body-bd">
    <td><?php echo $pval['singleNumber'];?></td>
	<td><?php echo $pval['color'];?></td>
    <td><?php echo Order::quantityFormat( $applyinfo['detail'][$pval->orderProductId]['oldNum'] );?></td>
	 <td><?php echo Order::quantityFormat( $applyinfo['detail'][$pval->orderProductId]['checkNum'] );?></td>
    <td><?php echo Order::priceFormat($pval['price']);?></td>
    <td>
		<?php $t = ($pval['isSample']=='1')?'0':$pval['num']*$pval['price'];
			echo Order::priceFormat($t);?>
	</td>
	<td> <?php if($pval['isSample']=='1'){?>是 <?php }else{?>否 <?php }?></td>
	<td><?php echo $pval['remark'];?></td>
    </tr>
     <?php endforeach;?>
	<tr>
    <td colspan="8" class="text-right" >
     <span>运费 ： <?php echo Order::priceFormat($model->freight);?></span>
     <span class="p-total">商品金额 ：<?php echo Order::priceFormat($model->realPayment-$model->freight);?></span>
    <span class="a-total">总金额 ：<?php echo Order::priceFormat($model->realPayment);?></span>
      </td></tr>
       </tbody>
    </table>
     </div>
 </div>
   <div class="order-details-list">
     <div class="hd">分批交货</div>
      <ul class="bd list-unstyled">
		<?php foreach( $model->batches as $bval) :?>
		 <li>
			<span class="item">交货日期：<?php echo $bval->exprise;?></span>
			<span class="item memo"><span class="pull-left">备注：</span><div style="padding-left:36px"><?php echo $bval->remark;?></div></span>
		</li>
		<?php endforeach;?>
	  </ul>
    </div>
	<br/>
</div>
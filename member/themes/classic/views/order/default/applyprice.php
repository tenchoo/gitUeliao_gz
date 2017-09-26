<link rel="stylesheet" href="/app/member/trade/css/style.css"/>
<form  method="post" action="">
<div class="pull-right frame-content order-check">
<div class="frame-tab">
    <ul class="clearfix list-unstyled frame-tab-hd">
      <li class="active">
        <a href="javascript:">价格申请</a>
      </li>
    </ul>
 </div>
        <div class="order-details-list">
          <div class="hd">
          <span class="item"><?php echo ($model['orderType'] =='0')?'现货':'预订' ?>订单：<?php echo $model->orderId;?></span>
          <span class="item">下单日期：<?php echo $model->createTime;?></span>
          </div>
          <ul class="bd list-unstyled">
            <li>
              <span class="item">客户名称：<?php echo $member['companyname'];?></span>
 			  <span class="item">联系人：<?php echo $model->name;?>（<?php echo $model->tel;?>）</span>
            </li>
            <li>
              <span class="item">提货方式：<?php echo $model->deliveryMethod;?></span>
              <span class="item">支付方式：<?php echo $model->payModel;?></span>
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
	 <th>数量</th>
	 <th>单价（元）</th>
     <th>申请价格</th>
	 <th>操作</th>
	</tr>
    </thead>
    <tbody class="list-page-body">
    <?php foreach( $model->products as $pval) :?>
    <tr class="list-body-bd">
    <td ><?php echo $pval['singleNumber'];?></td>
	<td ><?php echo $pval['color'];?></td>
	<td><?php echo Order::quantityFormat( $pval['num'] );?> </td>
	 <td> <?php echo Order::priceFormat( $pval['price'] );?></td>

	<?php if( !empty ( $applyprices ) ){ ?>
	<td>
	<input type="text" value="<?php echo $applyprices[$pval->orderProductId];?>" class="form-control price-only input-xs" disabled />
	</td>
	<td></td>
	<?php }else{ ?>
	<td>
	<input type="text" name="data[<?php echo $pval->orderProductId;?>]" value="<?php echo $pval['price'];?>" class="form-control price-only input-xs"/>
	</td>
	<td><a href="javascript:" class="text-link del">删除</a></td>
	<?php }?>
    </tr>
     <?php endforeach;?>
      </tbody>
    </table>
     </div>
 </div><br/>
  <div class="text-center">
	<?php if( !empty ( $applyprices ) ){ ?>
	<button class="btn btn-xs" disabled >提交申请</button>
	<?php }else{ ?>
	<button class="btn btn-xs btn-success" type="submit" >提交申请</button>
	<?php }?>
 </div>
</div>
</form>
<script>
var msg = '<?PHP echo $msg; ?>';
seajs.use('app/member/trade/js/orderpriceapply.js');
</script>


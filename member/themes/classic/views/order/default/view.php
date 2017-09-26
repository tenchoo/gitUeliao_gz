<link rel="stylesheet" href="/app/member/trade/css/style.css"/>
<div class="pull-right frame-content">
<div class="frame-tab">
    <ul class="clearfix list-unstyled frame-tab-hd">
      <li class="active">
        <a href="javascript:">订单详情</a>
      </li>
    </ul>
 </div>
<div class="order-status">
<?php
switch( $model->state ){
	case '0': ?><!--待审核-->
		<?php if( $model->orderType == tbOrder::TYPE_KEEP ){
			if (!empty( $keep ) ){ ?>
		<div class="hd">当前订单状态：<?php echo $keep['state'];?>,留货至：<?php echo $keep['expireTime'];?></div>
		<div class="bd">
		<p>如果你想取消这笔订单，可以<span><a href="javascript:" class="cancel-order text-link" data-orderid="<?php echo $model->orderId ?>">取消订单</a></span></p>
		</div>
		<?php }}else{?>
		<div class="hd">当前订单状态：已拍下，订单待审核</div>
		<div class="bd">
        <p>如果你想取消这笔订单，可以<span><a href="javascript:" class="cancel-order text-link" data-orderid="<?php echo $model->orderId ?>">取消订单</a></span></p>
		<p>如果订单未付款，点击这里<span><a href="<?php echo $this->createUrl('/cart/pay/index/',array('orderids'=>$model->orderId));?>" class="text-link" target="_blank">付款</a></span>,系统将在<?php echo $member['payTime'] ;?>分钟后关闭订单</p>
		</div>
		<?php }?>
<?php	break;
	case '1': ?>
		<div class="hd">当前订单状态：备货中</div>
		<div class="bd">
        <p>如果你想取消这笔订单，可以<span><a href="javascript:" class="cancel-order text-link" data-orderid="<?php echo $model->orderId ?>">取消订单</a></span></p>
		</div>
<?php	break;
	case '2': ?>
		<div class="hd">当前订单状态：备货完成</div>
		<div class="bd">
        <!-- p>1.如果你想取消这笔订单，可以<span><a href="javascript:" class="cancel-order text-link" data-orderid="<?php //echo $model->orderId ?>">取消订单</a></span></p-->
		<?php if($this->userType !='member'){ ?>
	    <p>2.业务员确认，并<span><a href="<?php echo $this->createUrl('settlement',array('id'=>$model->orderId));?>" class="text-link">生成结算单</a></span></p>
		<?php } ?>
		</div>
<?php	break;
	case '3': ?>
		<div class="hd">当前订单状态：待仓库发货</div>
		<div class="bd">
        <!-- <p>如果你想取消这笔订单，可以<span><a href="javascript:" class="cancel-order text-link">取消订单</a></span></p> -->
		</div>
<?php	break;
	case '4':  ?>
		<div class="hd">当前订单状态：已发货</div>
		<div class="bd">
        <p>1.如果您已收到货，您可以<span><a href="<?php echo $this->createUrl('received',array('id'=>$model->orderId));?>" class="text-link">确认收货</a></span></p>
		<p>2.如果还未收到货，您可以<span><a href="<?php echo $this->createUrl('expressinfo',array('id'=>$model->orderId));?>" class="text-link">查看物流</a></span></p>
		</div>
<?php	break;
	case '6':
		if( $model->commentState == '0' ){   ?>
			<div class="hd">当前订单状态：交易成功</div>
			<div class="bd">
			<p>1.如果没有收到货，或收到货后出现问题，您可以联系卖家协商解决。</p>
			<p>2.如果您对本次交易有异议，请对商家进行反馈，<span><a href="<?php echo $this->createUrl('/order/comment/add',array('orderId'=>$model->orderId));?>" class="text-link">立即反馈</a></span></p>
			</div>
	<?php }else { ?>
			<div class="hd">当前订单状态：买家已反馈</div>
			<div class="bd">
			<p>感谢您对商城的支持，期待您的下次光临！你可以：<span><a href="<?php echo $this->createUrl('/order/comment/index');?>" class="text-link">查看反馈内容</a></span></p>
			</div>
	<?php }?>
<?php	break;
	case '7':  ?>
		<div class="hd">当前订单状态：交易关闭</div>
		<div class="bd">
			<p>关闭类型：<?php echo $closeReason['reasonType'];?></p>
			<p>原因：<?php echo $closeReason['reason'];?></p>
		</div>
<?php	break; }?>
 </div>

<?php $this->beginContent('_orderinfo',array('model' => $model ,'member'=>$member,'payments' => $payments,'orderType'=>$orderType));$this->endContent();?>
 <div class="frame-list order-details">
    <div class="frame-list-bd">
    <table>
     <thead>
      <tr>
	 <th>产品编号</th>
	 <th>颜色</td>
	 <th>购买数量</th>
     <th>单价（元）</th>
     <th>金额（元）</th>
	 <th>赠板</th>
	 <th>备注</th>
	 </tr>
     </thead>
    <tbody class="list-page-body">
    <?php foreach( $model->products as $pval) :?>
     <tr class="list-body-bd">
      <td><?php echo $pval['serialNumber'];?></td>
	  <td><?php echo $pval['color'];?></td>
	  <td><?php echo Order::quantityFormat( $pval['num'] );?></td>
	  <td><?php echo Order::priceFormat( $pval['price'] );?></td>
	  <td>
		 <?php if( $pval['isSample'] == '1'){ ?>
		  <?php echo Order::priceFormat(0);?>
		 <?php }else { ?>
		   <?php echo Order::priceFormat( bcmul( $pval['num'],$pval['price'],2) );?>
		 <?php }?>
	  </td>
	  <td><?php echo ($pval['isSample'])?'是':'否';?></td>
	  <td><?php echo $pval['remark'];?></td>
	 </tr>
    <?php endforeach;?>
    <tr class="total">
	 <td colspan="7">
		<span>运费 ： <?php echo Order::priceFormat($model['freight']);?>元</span>
		<span>商品金额 ： <?php echo Order::priceFormat( bcsub($model['realPayment'],$model['freight'],2) );?>元</span>
		<span>总金额 ：   <?php echo Order::priceFormat( $model['realPayment']  );?>元</span>
	  </td>
	 </tr>
     </tbody>
    </table>
   </div>
  </div>

<?php if( $model->orderType == tbOrder::TYPE_BOOKING ) {
	$deposit = $model->deposit;
	if( !empty( $deposit ) && $deposit->amount > 0 ){
 ?>
  <div class="order-details-list">
    <div class="hd">订金信息</div>
    <ul class="bd list-unstyled">
        <li>
		<span class="item">订金金额：<?php echo $deposit->amount;?></span>
		<span class="item">尾款：<?php echo bcsub($model->realPayment,$deposit->amount);?></span>
		</li>
	</ul>
  </div>
 <?php }}?>
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
</div>
<div class="cancel-order-tip hide">
					<p>您确定要取消该订单吗？取消订单后，不能恢复。</p>
					<p>
					  <select name="closeReason">
							<?php foreach( $closeReasons as $val ) { ?>
							<option value="<?php echo $val;?>"><?php echo $val;?></option>
							<?php } ?>
						</select>
					</p>
				</div>
    <script>seajs.use('app/member/trade/js/orderview.js');</script>
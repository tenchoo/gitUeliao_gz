<link rel="stylesheet" href="/app/member/trade/css/style.css"/>

<div class="pull-right frame-content order-check">
<div class="order-status">
<?php if( $model->state =='7' ){ ?>
	<div class="hd">订单已取消，交易关闭</div>
<?php }else{ ?>
	<?php if($applyinfo['state']=='0'){ ?>
	<div class="hd">订单已申请取消，待审核</div>
	<?php }else if( $applyinfo['state'] == '2'){ ?>
	<div class="hd">不同意取消订单</div>
	<div class="bd">
		<p>审核反馈：<?php echo $applyinfo['remark'];?></p>
	</div>
<?php }}?>
 </div>
<?php $this->beginContent('_orderinfo',array('model'=>$model,'member'=>$member));$this->endContent();?>
</div>
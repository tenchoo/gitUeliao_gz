<div class="alert alert-danger alert-dismissible fade in" role="alert">
<?php if( $model->state =='7' ){ ?>
	订单已取消，交易关闭
<?php }else{ ?>
<?php if($applyinfo['state']=='0'){ ?>
	订单已申请取消，待审核
<?php }else if( $applyinfo['state'] == '2'){ ?>
	不同意取消订单
	<p>审核反馈：<?php echo $applyinfo['remark'];?></p>
<?php }}?>
</div>

<?php $this->beginContent('_orderinfo',array('model'=>$model,'member'=>$member));$this->endContent();?>
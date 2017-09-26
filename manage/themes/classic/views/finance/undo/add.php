<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
 <?php $this->beginContent('//layouts/_error2');$this->endContent();?><br/>
<form  method="post" action="">
<div class="panel panel-default">
	<ul class="list-group">
		<li class="list-group-item clearfix">
			<span class="col-md-4">收款流水号：<?php echo $recordsId;?></span>
			<span class="col-md-4">提交收款时间：<?php echo $createTime;?></span>
			<span class="col-md-4">操作员：<?php echo $username;?></span>
		</li>
		<li class="list-group-item clearfix">
			<span class="col-md-4">客户：<?php echo $memberId;?></span>
			<span class="col-md-4">结算<?php echo ( $type == '0' )?'单号':'月份'; ?>：<?php echo $settlementId;?></span>
			<span class="col-md-4">收款金额：<?php echo $amount;?></span>
		</li>
	</ul>
</div>
<?php if( empty( $undoModel ) ){ ?>
<br>
<form action="" method="post">
  <div class="form-group clearfix">
    <label class="control-label col-md-2">申请撤销理由：</label>
    <div class="clearfix col-md-4">
      <div class="input-group">
		<textarea class="form-control" rows="2" cols="40" name="cause" maxlength="50"></textarea>
      </div>
    </div>
  </div>
    <div class="form-group clearfix">
    <div class="col-md-offset-2 col-md-10">
      <button class="btn btn-success" type="submit">申请撤销</button>
    </div>
  </div>
</form>
<?php }else{ ?>
<div class="clearfix alert alert-warning">
 <strong class="text-warning"> 已申请撤销</strong>
</div>
<?php }?>
<script>seajs.use('statics/app/finance/js/add.js');</script>

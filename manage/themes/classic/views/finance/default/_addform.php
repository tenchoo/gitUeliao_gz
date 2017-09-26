<div class="clearfix alert alert-warning">
 <strong class="text-warning"> 总未收款：<?php echo Order::priceFormat($receipts['notReceive'])?> 元 = <?php echo Order::priceFormat($receipts['realPayment'])?> 元 (总应收款) - <?php echo Order::priceFormat($receipts['totalReceipt']);?> 元 (总已收款)</strong>
</div>

<?php if( $receipts['notReceive'] > 0 ){ ?>
<ul class="nav nav-tabs">
  <li role="addform" class="active"><a href="javascript:;">新增收款</a></li>
  <li role="applyform" class=""><a href="javascript:;">申请结算</a></li>
</ul>
<div class="addform tab-form">
   <form action="" method="post">
   <input type="hidden" name="action" value="add"/>
  <div class="form-group  clearfix">
    <label class="control-label col-md-1">当前收款：</label>
    <div class="clearfix col-md-4">
      <div class="input-group">
        <input type="text" class="pull-left form-control input-sm price-only" name="amount" value="<?php echo $receipts['notReceive']?>" maxlength="10">
        <div class="input-group-addon">元</div>
      </div>
    </div>
  </div>
    <div class="form-group   clearfix">
    <div class="col-md-offset-1 col-md-10">
      <button class="btn btn-success" type="submit">增加收款</button>
    </div>
  </div>
</form>
</div>
<div class="applyform hide tab-form">
<?php if( $receipts['isApply']  ) { ?>
<div class="clearfix alert alert-warning">
 <strong class="text-warning"> 已经申请结算，请等待审核</strong>
</div>
<?php }else{ ?>

<form action="" method="post">
	<input type="hidden" name="action" value="apply"/>
	<div class="form-group clearfix">
    <label class="col-sm-1 control-label">申请金额：</label>
    <div class="col-sm-10"><?php echo Order::priceFormat($receipts['notReceive'])?> 元</div>
	</div>
  <div class="form-group clearfix">
    <label class="control-label col-md-1">申请理由：</label>
    <div class="clearfix col-md-4">
      <div class="input-group">
		<textarea class="form-control" rows="2" cols="40" name="cause" maxlength="50"></textarea>
      </div>
    </div>
  </div>
    <div class="form-group clearfix">
    <div class="col-md-offset-1 col-md-10">
      <button class="btn btn-success" type="submit">申请结算</button>
    </div>
  </div>
</form>
<?php }?>
</div>
<br><br>
<?php }?>
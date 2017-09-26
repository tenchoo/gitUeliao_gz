    <div class="panel panel-default">
		<div class="panel-heading clearfix">
           <span class="col-md-4">客户：<?php echo $companyname;?></span>
			<span class="col-md-4">结算<?php echo ( $type == '0' )?'单号':'月份'; ?>：<?php echo $settlementId;?></span>
			
        </div>		
        <ul class="list-group">
			<li class="list-group-item clearfix">
			<span class="col-md-4">应收款金额：<span class="text-danger"><?php echo Order::priceFormat( $realPayment );?></span>元</span>
			<span class="col-md-4">已收款：<span class="text-danger"><?php echo Order::priceFormat( $receipt );?></span>元</span>
			<span class="col-md-4">未收款：<span class="text-danger"><?php echo Order::priceFormat( $notReceive );?></span>元</span>
		</li>
		<li class="list-group-item clearfix">
			<span class="col-md-4">申请人：<?php echo $username;?></span>
			<span class="col-md-4">申请时间：<?php echo $createTime;?></span>
			<span class="col-md-4">申请金额：<span class="text-danger"><?php echo Order::priceFormat( $amount );?></span>元</span>
		</li>
        <li class="list-group-item clearfix">
			<span class="col-md-12">申请理由：<?php echo $applyCause;?></span>
        </li>
        <li class="list-group-item clearfix">
            <span class="col-md-4">审核结果：<?php echo $stateTitle;?></span>
			<span class="col-md-4">审核人：<?php echo $checkUsername;?></span>
			<span class="col-md-4">审核时间：<?php echo $checkTime;?></span>
        </li>
         <li class="list-group-item clearfix">
			<span class="col-md-12">审核理由：<?php echo $checkCause;?></span>
        </li>
    </div>
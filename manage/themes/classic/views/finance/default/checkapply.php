<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
 <?php $this->beginContent('//layouts/_error2');$this->endContent();?><br/>
<form method="post">
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
			<span class="col-md-4">申请金额：<span class="text-danger"><?php echo Order::priceFormat( $notReceive );?></span>元</span>
		</li>
        <li class="list-group-item clearfix">
			<span class="col-md-12">申请理由：<span class="text-danger"><?php echo $applyCause;?></span></span>
        </li>
        <li class="list-group-item clearfix">
            <span class="col-md-4">审核结果：
                <label class="radio-inline"><input type="radio" name="state" value="pass"/>同意</label>
                <label class="radio-inline"><input type="radio" name="state" value="notpass"/>不同意</label>
            </span>
        </li>
        <li class="list-group-item clearfix"><span class="col-md-8">审核理由：<label class="radio-inline"><textarea name="cause" class="form-control"></textarea></label></span></li>
        </ul>
    </div>
    <div class="text-center">
      <input class="btn btn-success" type="submit" value="提交审核"/>
    </div>
</form>
<script>seajs.use('statics/app/finance/js/add.js');</script>
<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
 <?php $this->beginContent('//layouts/_error2');$this->endContent();?><br/>
<form method="post">
    <div class="panel panel-default">
		<div class="panel-heading clearfix">
           <span class="col-md-4">客户：<?php echo $companyname;?></span>
			<span class="col-md-4">结算<?php echo ( $records['type'] == '0' )?'单号':'月份'; ?>：<?php echo $records['settlementId'];?></span>
			<span class="col-md-4">收款金额：<span class="text-danger"><?php echo Order::priceFormat( $records['amount'] );?></span>元</span>
        </div>
        <ul class="list-group">
			<li class="list-group-item clearfix">
			<span class="col-md-4">收款流水号：<?php echo $recordsId;?></span>
			<span class="col-md-4">提交收款时间：<?php echo $records['createTime'];?></span>
			<span class="col-md-4">操作员：<?php echo $records['username'];?></span>
		</li>
		<li class="list-group-item clearfix">
			<span class="col-md-4">申请撤销人：<?php echo $username;?></span>
			<span class="col-md-4">申请撤销时间：<?php echo $createTime;?></span>
		</li>
        <li class="list-group-item clearfix">
			<span class="col-md-12">申请撤销理由：<span class="text-danger"><?php echo $applyCause;?></span></span>
        </li>
        <li class="list-group-item clearfix">
            <span class="col-md-4">审核结果：
                <label class="radio-inline"><input type="radio" name="state" value="pass"/>同意撤销</label>
                <label class="radio-inline"><input type="radio" name="state" value="notpass"/>不同意撤销</label>
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
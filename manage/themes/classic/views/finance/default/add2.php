<link rel="stylesheet" href="/themes/classic/statics/app/finance/css/style.css">
 <?php $this->beginContent('//layouts/_error2');$this->endContent();?>
<div class="panel panel-default">
	<div class="panel-heading clearfix">
	 <span class="col-md-4">客户名称：<?php echo $member;?></span>
	 <span class="col-md-4">结算月份：<?php echo $month;?></span>
	</div>
</div>
<?php $this->beginContent('_details',array('detail'=>$detail,'realPayment'=>$receipts['realPayment'],'month'=>$month ) );$this->endContent();?>
<?php $this->beginContent('_receipts',array('receipts'=>$receipts) );$this->endContent();?>
<?php $this->beginContent('_addform',array('receipts'=>$receipts) );$this->endContent();?>
<script>seajs.use('statics/app/finance/js/add.js');</script>
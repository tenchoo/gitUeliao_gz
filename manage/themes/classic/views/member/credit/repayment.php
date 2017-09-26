<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<div class="panel panel-default">
	<div class="panel-heading clearfix">
	 <span class="col-md-4">客户ID：<?php echo $model->memberId;?></span>
	 <span class="col-md-4">信用额度：<?php echo number_format( $model->credit,2 );?> 元 &nbsp; &nbsp;<?php if($model->state == '1' ){?>已取消月结<?php }?></span>
		<span class="col-md-4">还款周期：<?php echo $model->billingCycle;?> 个月</span>
	</div>
	<ul class="list-group">
	 <li class="list-group-item clearfix">
		<span class="col-md-4">客户名称：<?php echo $companyName;?></span>
		<span class="col-md-4">已用额度：<?php echo number_format( $usedCredit,2 );?></span>
		<span class="col-md-4">可用额度：<?php echo number_format( $validCredit,2 );?> </span>
	 </li>
	</ul>
</div>
<br>
<form class="form-horizontal" method="post" action="">
   <div class="form-group">
    <label class="control-label col-md-2" for="address">还款金额：</label>
    <div class="col-md-4">
		 <input class="form-control input-sm" name="amount" type="text" />
    </div>
  </div>
<?php $this->beginContent('//layouts/_error');$this->endContent();?><br/>

  <div class="form-group">
    <div class="col-md-offset-2 col-md-10">
      <button type="button" class="btn btn-success">提交还款</button>
    </div>
  </div>
</form>
<script>
  $('.btn-success').on('click',function(){
    if(confirm('确定已收款？')){
      $('form').trigger('submit');
    }
  });
</script>
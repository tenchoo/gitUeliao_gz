<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<?php $this->beginContent('_orderinfo',array('model'=>$model,'member'=>$member));$this->endContent();?>

<form method="post" action="">
<div class="panel panel-default">
	<div class="panel-heading clearfix">
	 <span class="col-md-4">审核结果： <label class="radio-inline">
		<input type="radio" name="data[state]" value="1"/>同意取消订单
	 </label>
      <label class="radio-inline">
		<input type="radio" name="data[state]" value="2" />不同意取消订单
	  </label></span>
	</div>
	<div class="panel-heading clearfix">
	 <span class="col-md-4">审核反馈：
	  <label class="radio-inline">
	 <textarea name="data[remark]" class="form-control"></textarea>
	  </label>
	 </span>
	</div>
</div>

   <?php $this->beginContent('//layouts/_error');$this->endContent();?>
   <div class="text-center">
		<button class="btn btn-success">保存</button>
	</div>
 </form>
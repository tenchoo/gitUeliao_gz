<?php
$this->beginContent('//layouts/_error');$this->endContent();
$products = tbWarehouseWarrantDetail::model()->findAllByWarrant($warrant->warrantId);
?>
<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<div class="panel panel-default">
	<div class="panel-heading clearfix">
	  <span class="col-md-4">入库单号：<?php echo $warrant->warrantId; ?></span>
		<span class="col-md-4">操作时间：<?php echo substr($warrant->createTime, 0, 10); ?></span>
		<span class="col-md-4">操作员：<?php echo $warrant->operator; ?></span>
	</div>
	<ul class="list-group">
		<?php if (!is_null($warrant->posts)) {?>
		<li class="list-group-item clearfix">
			<span class="col-md-4">采购单号：<?php echo $warrant->posts->purchaseId; ?></span>
			<span class="col-md-4">发货时间：<?php echo $warrant->posts->postTime; ?></span>
		</li>
		<?php }?>
		<li class="list-group-item clearfix">
			<span class="col-md-4">工厂编号：<?php echo $warrant->factoryNumber; ?></span>
			<span class="col-md-4">工厂名称：<?php echo $warrant->factoryName; ?></span>
			<span class="col-md-4">联系人：<?php echo $warrant->contactName; ?></span>
		</li>
		<li class="list-group-item clearfix">
			<span class="col-md-4">联系电话：<?php echo $warrant->phone; ?></span>
			<span class="col-md-4">地址：<?php echo $warrant->address; ?></span>
		</li>
		<li class="list-group-item clearfix">
			<span class="col-md-12">备注：<?php echo $warrant->remark; ?></span>
		</li>
	</ul>
</div>
<br>
	<table class="table table-condensed table-bordered">
   <thead>
    <tr>
	 <td>产品编号</td>
	 <td>颜色</td>
     <td>发货数量</td>
	 <td>仓位</td>
	 <td>入库数量</td>
	 <td>产品批次</td>
	  </tr>
	  </thead>
	  <tbody>
	<?php foreach ($products as $key => $pval):
    $unit = ZOrderHelper::getUnitName($pval->singleNumber);
    ?>
	     <tr class="order-list-bd">
		 <td><?php echo $pval->singleNumber; ?></td>
		 <td><?php echo $pval->color; ?></td>
		 <td><?php echo $pval->postQuantity . $unit; ?></td>
		 <td><?php echo $pval->positionName; ?></td>
	     <td><?php echo $pval->num . $unit; ?></td>
		 <td><?php echo $pval->batch; ?></td>
		  </tr>
		<?php endforeach;?>
	</tbody>
</table>

<form action="" method="post">
<input type="hidden" name="warrantId" value="<?php echo $warrant->warrantId;?>" />
<div class="panel panel-default">
<div class="panel-heading clearfix">申请撤消说明</div>
<ul class="list-group">
		<li class="list-group-item clearfix">
			<textarea name="remark" class="form-control" rows="3" maxlength="50"></textarea>
		</li>
		<li class="list-group-item clearfix text-center">
			<button type="submit" class="btn btn-primary">提交撤消申请</button>
		</li>
	</ul>
</div>
</form>
<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<div class="panel panel-default">
	<div class="panel-heading clearfix">
	  <span class="col-md-4">入库单号：<?php echo $data['warrantId'];?></span>
		<span class="col-md-4">操作时间：<?php echo substr($data['createTime'],0,10);?></span>
		<span class="col-md-4">操作员：<?php echo $data['operator'];?></span>
	</div>
	<ul class="list-group">
		<li class="list-group-item clearfix">
			<span class="col-md-4">退单号：<?php echo $data['postInfo']['postId'];?></span>
			<span class="col-md-4">发货时间：<?php echo $data['postInfo']['postTime'];?></span>
		</li>
		<li class="list-group-item clearfix">
			<span class="col-md-4">工厂编号：<?php echo $data['factoryNumber'];?></span>
			<span class="col-md-4">工厂名称：<?php echo $data['factoryName'];?></span>
			<span class="col-md-4">联系人：<?php echo $data['contactName'];?></span>
		</li>
		<li class="list-group-item clearfix">
			<span class="col-md-4">联系电话：<?php echo $data['phone'];?></span>
			<span class="col-md-4">地址：<?php echo $data['address'];?></span>
		</li>
		<li class="list-group-item clearfix">
			<span class="col-md-12">备注：<?php echo $data['remark'];?></span>
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
	 <?php foreach( $data['products'] as $key=>$pval):
		 $unit = ZOrderHelper::getUnitName($pval->singleNumber);
	 ?>
	  <tr>
     <tr class="order-list-bd">
	 <td><?php echo $pval->singleNumber;?></td>
	 <td><?php echo $pval->color;?></td>
	 <td><?php echo $pval->postQuantity.$unit;?></td>
	 <td><?php echo $pval->positionName;?></td>
     <td><?php echo $pval->num.$unit;?></td>
	 <td><?php echo $pval->batch;?></td>
	 </td>
	  </tr>
	<?php  endforeach;?>
	 </table>
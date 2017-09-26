<link rel="stylesheet"
	href="/themes/classic/statics/app/order/css/style.css" />
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<?php $this->beginContent('//layouts/_success');$this->endContent();?>

<form class="form-horizontal" method="post">
	<div class="panel panel-default">
		<div class="panel-heading clearfix">
			<span class="col-md-4">请购单号：<?php echo $order->orderId;?></span>
			<span class="col-md-4">下单时间：<?php echo date('Y-m-d H:i',$order->createTime);?></span>
		</div>
		<ul class="list-group">
			<li class="list-group-item clearfix">
				<span class="col-md-4">请购人：<?php echo $order->userName;?></span>
				<span class="col-md-4">请购原因：<?php echo $order->cause;?></span>
			</li>
			<li class="list-group-item clearfix">
				<span class="col-md-4">备注：<?php echo $order->comment;?></span>
			</li>
		</ul>
	</div>
	<br />

	<table border="1" class="table table-condensed table-bordered">
		<thead>
			<tr>
				<td>产品编号</td>
				<td>颜色</td>
				<td>订货数量</td>
				<td>交货日期</td>
				<td>备注</td>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $order->products() as $index => $item  ){
				$id = $item->orderId;
				?>
			<tr>
				<td><?php echo $item->singleNumber;?></td>
				<td><?php echo $item->color;?></td>
				<td class="col-md-2"><?php echo Order::quantityFormat($item->total).$item->unitName;?></td>
				<td class="col-md-2"><?php echo date('Y/m/d',$item->dealTime)?></td>
				<td class="col-md-2"><?php echo htmlspecialchars($item->comment);?></td>
			</tr>
			<?php }?>
		</tbody>
	</table>
	
	<div class="panel panel-default">
		<div class="panel-heading clearfix">
			<textarea name="form[closeCause]" class="form-control" style="width:400px"></textarea>
		</div>
	</div>
	
	<div class="text-center">
		<input type="submit" value="关闭采购" class="btn btn-success" />
	</div>
</form>
<?php $batch = tbWarehouseProduct::DEATULE_BATCH;?>

<link rel="stylesheet" href="/themes/classic/statics/app/warehouse/css/style.css" />
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<?php $this->beginContent('//layouts/_success');$this->endContent();?>

<div class="panel panel-default">
  <div class="panel-heading clearfix">
    <span class="col-md-4">发货单号：<?php echo $post->postId;?></span>
    <span class="col-md-4">发货日期：<?php echo $post->postTime;?></span>
  </div>
  <ul class="list-group">
    <li class="list-group-item clearfix">
		<span class="col-md-4">工厂编号：<?php echo $order->supplierSerial;?></span>
		<span class="col-md-4">工厂名称：<?php echo $order->supplierName;?></span>
		<span class="col-md-4">联系人：<?php echo $order->supplierContact;?></span>
	 </li>
	 <li class="list-group-item clearfix">
	   <span class="col-md-12">联系电话：<?php echo $order->supplierPhone;?></span>
	 </li>
	 <li class="list-group-item clearfix">
	   <span class="col-md-12">收货地址：<?php echo $order->address;?></span>
	 </li>
	 <li class="list-group-item clearfix">
	   <span class="col-md-12">订单备注：<?php echo $order->comment;?></span>
	 </li>
  </ul>
</div>
<table class="table table-condensed table-bordered">
		<thead>
		<tr>
			<td>产品编号</td>
			<td>颜色</td>
			<td>发货数量</td>
		</tr>
		</thead>

		<tbody>
	<?php foreach( $products as $index=> $product ):
		$detail = $product->details;?>
	<tr>
		<td><?php echo $detail->productCode;?></td>
		<td><?php echo $detail->color;?></td>
		<td><?php echo Order::quantityFormat($detail->quantity)?><?php echo ZOrderHelper::getUnitName($detail->productCode);?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<br />

<form method="post">
	<input type="hidden" name="postId" value="<?php echo $post->postId;?>" />
	<input type="hidden" name="orderId" value="<?php echo $post->purchaseId;?>" />
	<table class="table table-condensed table-bordered import">
		<thead>
		<tr>
			<td width="15%">产品编号</td>
			<td>仓位号</td>
			<td width="15%">入库数量</td>
			<td width="15%">产品批次</td>
			<td width="10%">操作</td>
		</tr>
		</thead>

		<tbody>
		<?php foreach( $products as $index=> $product ):
				$detail = $product->details;?>
			<tr>
				<td><input type="hidden" name="product[<?php echo $index;?>][detailId]" value="<?php echo $product->postProId;?>" /><?php echo $detail->productCode;?></td>
				<td>
				  <div class="warehouse-list">
				  <select class="form-control input-sm cate1">
	        <option value="default">请选择</option>
	      </select>
	      <select class="form-control input-sm cate2">
	        <option value="default">请选择</option>
	      </select>
	      <select class="form-control input-sm cate3">
	        <option value="default">请选择</option>
	      </select>
	      <input type="hidden" name="product[<?php echo $index;?>][positionId]" value="" />
	      </div>
				</td>
				<td><input type="hidden" name="product[<?php echo $index;?>][postQuantity]" value="<?php echo Order::quantityFormat($detail->quantity);?>">
					<input name="product[<?php echo $index;?>][total]" value="<?php echo $detail->quantity;?>" class="form-control input-sm num-float-only" maxlength="7"/></td>
				<td>
				<input name="product[<?php echo $index;?>][batch]" type="hidden" maxlength="7" value="<?php echo $batch;?>" /><?php echo $batch;?></td>
				<td></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
		<tfoot>
		<tr>
			<td colspan="5" align="center"><a href="javascript:" data-templateid="importlist" data-postid="<?php echo $post->postId;?>">添加仓位</a></td>
		</tr>
		</tfoot>
	</table>
<br>
<div align="center">
<input class="btn btn-success" type="submit" value="确认入库" />
</div>
</form>
<script type="text/html" id="importlist">
  <tr>
				<td>
				<select name="product[{{id}}][detailId]" class="form-control input-sm importtitle"><option value="default">请选择</option>{{each data}}<option value="{{$index}}">{{$value}}</option>{{/each}}</select>
        </td>
				<td id="J_{{id}}">
				  <div class="warehouse-list">
				  <select class="form-control input-sm cate1">
	        <option value="default">请选择</option>
	      </select>
	      <select class="form-control input-sm cate2">
	        <option value="default">请选择</option>
	      </select>
	      <select class="form-control input-sm cate3">
	        <option value="default">请选择</option>
	      </select>
        <input type="hidden" name="product[{{id}}][positionId]" value="" />
	      </div>
				</td>
				<td><input name="product[{{id}}][total]" value="" class="form-control input-sm" maxlength="7"/></td>
				<td>
				<input name="product[{{id}}][batch]" type="hidden" maxlength="7" value="<?php echo $batch;?>" />
				<?php echo $batch;?>
				</td>
				<td><a href="javascript:" class="del">删除</a></td>
			</tr>
</script>

<script>seajs.use('statics/app/warehouse/js/import.js');</script>
<?php $batch = tbWarehouseProduct::DEATULE_BATCH;?>
<link rel="stylesheet" href="/themes/classic/statics/app/warehouse/css/style.css" />
<?php $this->beginContent('//layouts/_error');$this->endContent();?>

<form method="post" class="form-horizontal addnew-form">
<div class="panel panel-default">
  <ul class="list-group">
    <li class="list-group-item clearfix">
		<span class="col-md-5"><label class="control-label pull-left">*工厂名称：</label><input type="hidden" name="form[id]" value="<?php echo $order->id;?>" /><input type="text" class="form-control input-sm" name="form[name]" style="width:200px;" value="<?php echo $order->name;?>" data-suggestion="supplierSerial" data-search="name=%s" data-api="/api/fetch_supplier_info" autocomplete="off"/></span>
		<span class="col-md-5"><label class="control-label pull-left">联系人：</label><input type="text" class="form-control input-sm" name="form[contact]" value="<?php echo $order->contact;?>" readonly /></span>
	 </li>
	 <li class="list-group-item clearfix">
		 <span class="col-md-5"><label class="control-label pull-left">联系电话：</label><input type="text" class="form-control input-sm" name="form[phone]" value="<?php echo $order->phone;?>" readonly /></span>
		 <span class="col-md-5"><label class="control-label pull-left">收货地址：</label><input type="text" class="form-control input-sm" name="form[address]" value="<?php echo $order->address;?>" /></span>
	 </li>
	 <li class="list-group-item clearfix">
	 	<span class="col-md-5"><label class="control-label pull-left">订单备注：</label><input type="text" class="form-control input-sm" name="form[comment]" value="<?php echo $order->comment;?>" /></span>
	 	<span class="col-md-5"><label class="control-label pull-left">*发货单号：</label><input type="text" class="form-control input-sm" name="form[postId]" value="<?php echo $order->postId;?>" /></span>
	 </li>
  </ul>
</div>
<br />

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
			<tr>
				<td>
					<input type="text" class="form-control input-sm product-search" name="product[0][productCode]" data-suggestion="productSearchBox" data-search="serial=%s" data-api="/api/search_product_serial" autocomplete="off" />
				</td>
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
	      <input type="hidden" name="product[0][positionId]" value="" />
	      </div>
				</td>
				<td><input name="product[0][total]" class="form-control input-sm"  maxlength="7"/></td>
				<td>
				<input name="product[0][batch]" type="hidden" maxlength="7" value="<?php echo $batch;?>" />
				<?php echo $batch;?>
				</td>
				<td></td>
			</tr>
		</tbody>
		<tfoot>
		<tr>
			<td colspan="5" align="center"><a href="javascript:" data-templateid="importlist">添加仓位</a></td>
		</tr>
		</tfoot>
	</table>
<br>
<div align="center">
<input class="btn btn-success addlm" type="submit" value="确认入库" />
</div>
</form>
<script type="text/html" id="importlist">
  <tr id="J_{{id}}">
				<td>
					<input name="product[{{id}}][productCode]" class="form-control input-sm" data-suggestion="productSearchBox" data-search="serial=%s" data-api="/api/search_product_serial" autocomplete="off"/>
        		</td>
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
        <input type="hidden" name="product[{{id}}][positionId]" value="" />
	      </div>
				</td>
				<td><input name="product[{{id}}][total]" value="" class="form-control input-sm" maxlength="7"/></td>
				<td><input name="product[{{id}}][batch]" type="hidden" maxlength="7" value="<?php echo $batch;?>" />
				<?php echo $batch;?></td>
				<td><a href="javascript:" class="del">删除</a></td>
			</tr>
</script>

<script>seajs.use('statics/app/warehouse/js/import.js');</script>
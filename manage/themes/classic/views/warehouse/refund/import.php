<link rel="stylesheet" href="/themes/classic/statics/app/warehouse/css/style.css" />
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<?php $this->beginContent('//layouts/_success');$this->endContent();?>

<div class="panel panel-default">
  <div class="panel-heading clearfix">
    <span class="col-md-4">退货单号：<?php echo $refundId;?></span>
    <span class="col-md-4">退货日期：<?php echo $createTime;?></span>
  </div>
  <ul class="list-group">
    <li class="list-group-item clearfix">
		<span class="col-md-4"><?php echo $orderType;?>：<?php echo $orderId;?></span>
		<span class="col-md-4">下单日期：<?php echo $orderCreateTime;?></span>
		<span class="col-md-4">业务员：<?php echo $member['salesman'];?></span>
	 </li>
	 <li class="list-group-item clearfix">
	   <span class="col-md-12">申请退货理由：<?php echo $cause;?></span>
	 </li>
  </ul>
</div>
<table class="table table-condensed table-bordered">
		<thead>
		<tr>
			<td>产品编号</td>
			<td>颜色</td>
			<td>退货数量</td>
		</tr>
		</thead>
		<tbody>
	<?php foreach( $products as $pval ):?>
	<tr>
		<td><?php echo $pval['singleNumber']?></td>
		<td><?php echo $pval['color'];?></td>
		<td><?php echo Order::quantityFormat($pval['num'])?><?php echo ZOrderHelper::getUnitName($pval['singleNumber']);?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<br />

<form method="post">
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
		<?php foreach( $products as $key=> $pval ):?>
			<tr>
				<td><input type="hidden" name="product[<?php echo $key;?>][singleNumber]" value="<?php echo $pval['singleNumber']?>" /><?php echo $pval['singleNumber'];?></td>
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
	      <input type="hidden" name="product[<?php echo $key;?>][positionId]" value="" />
	      </div>
				</td>
				<td>
					<input name="product[<?php echo $key;?>][postQuantity]" value="<?php echo $pval['num'];?>" class="form-control input-sm num-float-only" maxlength="7"/></td>
				<td><input name="product[<?php echo $key;?>][batch]" class="form-control input-sm" maxlength="7"/></td>
				<td></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
		<tfoot>
		<tr>
			<td colspan="5" align="center"><a href="javascript:" data-templateid="importlist" data-postid="">添加仓位</a></td>
		</tr>
		</tfoot>
	</table>
<br>
<div align="center">
<input class="btn btn-success imporsub" type="submit" value="确认入库" />
</div>
</form>
<script type="text/html" id="importlist">
  <tr>
				<td>
				<select name="product[{{id}}][singleNumber]" class="form-control input-sm importtitle"><option value="default">请选择</option>
				<?php foreach( $products as $pval ):?>
					<option value="<?php echo $pval['singleNumber']?>"><?php echo $pval['singleNumber']?></option>
				<?php endforeach; ?>
				</select>
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
				<td><input name="product[{{id}}][postQuantity]" value="" class="form-control input-sm" maxlength="7"/></td>
				<td><input name="product[{{id}}][batch]" class="form-control input-sm" maxlength="7"/></td>
				<td><a href="javascript:" class="del">删除</a></td>
			</tr>
</script>

<script>seajs.use('statics/app/warehouse/js/import.js');</script>
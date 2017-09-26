<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<?php $this->beginContent('//layouts/_success');$this->endContent();?>
<form class="form-horizontal" method="post">
<?php if( !is_null($order->orderId) ):?>
<input type="hidden" name="form[orderId]" value="<?php echo $order->orderId;?>" />
<?php endif;?>
	<div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>请购人：</label>
    <div class="col-md-4">
      <input type="text" name="form[userName]" value="<?php echo Yii::app()->user->getstate('username');?>" class="form-control input-sm">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>请购原因：</label>
    <div class="col-md-4">
      <input type="text" name="form[cause]" class="form-control input-sm" value="<?php echo htmlspecialchars($order->cause);?>">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">备注：</label>
    <div class="col-md-4">
      <textarea rows="3" cols="40" name="form[comment]" class="form-control"><?php echo htmlspecialchars( $order->comment );?></textarea>
    </div>
  </div>
  <br />
  
	<table class="table table-condensed table-bordered">
		<thead>
			<tr>
				<td>产品编号</td>
				<td>颜色</td>
				<td>订货数量</td>
				<td>交货日期</td>
				<td>备注</td>
				<td>操作</td>
			</tr>
		</thead>
		<tbody>
		<?php foreach ( $order->products() as $item ){
			$id = $item->requestProductId;
			?>
		<tr data-id="<?php echo $id;?>">
				<td><?php echo htmlspecialchars($item->singleNumber);?><input type="hidden" name="product[<?php echo $id;?>][productId]" value="<?php echo $item->productId;?>" /><input type="hidden" name="product[<?php echo $id;?>][singleNumber]" value="<?php echo htmlspecialchars($item->singleNumber);?>" /></td>
				<td><?php echo htmlspecialchars($item->color);?><input type="hidden" name="product[<?php echo $id;?>][color]" value="<?php echo htmlspecialchars($item->color);?>" /></td>
				<td class="col-md-2"><div class="input-group title-group"><input name="product[<?php echo $id;?>][total]" class="form-control input-sm num-float-only" value="<?php echo $item->total;?>" maxlength="9" /><div class="input-group-addon"><?php echo htmlspecialchars($item->unitName);?><input type="hidden" name="product[<?php echo $id;?>][unitName]" value="<?php echo $item->unitName;?>" /></div></div></td>
				<td class="col-md-2"><input name="product[<?php echo $id;?>][dealTime]" class="form-control input-sm input-date" value="<?php echo date('Y-m-d',$item->dealTime);?>" readonly /></td>
				<td class="col-md-2"><input name="product[<?php echo $id;?>][comment]" class="form-control input-sm" value="<?php echo htmlspecialchars($item->comment);?>" maxlength="20"/></td>
				<td><a href="/javascript:" class="del">删除</a></td>
			</tr>
		<?php }?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="6">
				  <br>
				  <div class="form-group center-block">
				    <span class="control-label col-md-5">添加产品：</span>
				    <div class="col-md-7 form-inline">
				      <input type="text" class="form-control input-sm" name="productSearchBox"  data-suggestion="productSearchBox" data-search="serial=%s" data-api="/api/search_product_serial" autocomplete="off"/>
				      <input type="button" disabled class="btn btn-sm btn-default" value="添加" data-templateid="requestbuylist" id="btn-add"/>
				    </div>
				  </div>
				</td>
			</tr>
		</tfoot>
	</table>
	<br><br>
  <button class="btn btn-success center-block addnewsub">申请采购</button>
</form>
<script type="text/html" id="requestbuylist">
      <tr data-id="{{id}}">
				<td>{{title}}<input type="hidden" name="product[{{id}}][productId]" value="{{productid}}" /><input type="hidden" name="product[{{id}}][singleNumber]" value="{{title}}" /></td>
				<td>{{color}}<input type="hidden" name="product[{{id}}][color]" value="{{color}}" /></td>
				<td class="col-md-2"><div class="input-group title-group"><input name="product[{{id}}][total]" class="form-control input-sm num-float-only" maxlength="9"/><div class="input-group-addon">{{unit}}<input type="hidden" name="product[{{id}}][unitName]" value="{{unit}}" /></div></div></td>
				<td class="col-md-2"><input name="product[{{id}}][dealTime]" class="form-control input-sm input-date" readonly /></td>
				<td class="col-md-2"><input name="product[{{id}}][comment]" class="form-control input-sm" maxlength="20"/></td>
				<td><a href="/javascript:" class="del">删除</a></td>
			</tr>
</script>
<script>
seajs.use('statics/app/purchase/requestbuy/js/addnew.js');
</script>
<link rel="stylesheet" href="/themes/classic/statics/app/purchase/default/css/style.css" />
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<?php $this->beginContent('//layouts/_warning');$this->endContent();?>

<form class="form-horizontal purchase-add" method="post">
<div class="panel panel-default">
  <div class="panel-heading clearfix">
    <div class="col-md-6"><label class="control-label pull-left"><span class="text-danger">*</span>工厂名称：</label><input type="text" name="form[supplierName]" class="form-control input-sm" value="<?php echo $supplierInfo['shortname'];?>" data-suggestion="supplierSerial" data-search="name=%s" data-api="/api/fetch_supplier_info" autocomplete="off"></div>

  </div>
  <ul class="list-group">
	  <li class="list-group-item clearfix">
	    <span class="col-md-4"><label class="control-label pull-left">&nbsp;&nbsp;工厂编号：</label><label class="control-label" id="serialnumber"><?php echo $supplierInfo['supplierSerialnumber'];?></label><input type="hidden" name="form[supplierSerial]" class="form-control input-sm" value="<?php echo $supplierInfo['supplierSerialnumber'];?>"></span>
		  <span class="col-md-4"><label class="control-label pull-left">联系人：</label><label class="control-label" id="contact"><?php echo $supplierInfo['contact'];?></label><input type="hidden" name="form[supplierContact]" class="form-control input-sm" value="<?php echo $supplierInfo['contact'];?>"></span>
		  <span class="col-md-4"><label class="control-label pull-left">联系电话：</label><label class="control-label" id="phone"><?php echo $supplierInfo['phone'];?></label><input type="hidden" name="form[supplierPhone]" class="form-control input-sm" value="<?php echo $supplierInfo['phone'];?>"><input type="hidden" name="form[supplierId]" class="form-control input-sm" value="<?php echo $supplierInfo['supplierId'];?>"></span>
	  </li>
	  <li class="list-group-item clearfix">
	    <span class="col-md-12"><label class="control-label pull-left"><span class="text-danger">*</span>收货地址：</label><input type="text" name="form[address]" class="form-control input-sm"></span>
	  </li>
	  <li class="list-group-item clearfix">
	    <span class="col-md-12"><label class="control-label pull-left">&nbsp;&nbsp;订单备注：</label><input type="text" name="form[comment]" class="form-control input-sm"></span>
	  </li>
	</ul>

</div>
<br />

<table class="table table-condensed table-bordered">
	<colgroup><col width="40%" /><col width="20%" /><col width="30%" /><col width="10%" /></colgroup>
	<thead>
		<tr>
			<td>来源单号</td>
			<td>采购数量</td>
			<td>备注</td>
			<td>操作</td>
		</tr>
	</thead>
</table>
<br>
<?php
foreach( $orderList as $key=>$item ){
	?>
<table class="table table-condensed table-bordered">
	<colgroup><col width="40%" /><col width="20%" /><col width="30%" /><col width="10%" /></colgroup>
	<tbody>
	<tr class="list-hd">
		<td colspan="4">
			<span class="first">产品编号：<?php echo $item['productCode'];?></span>
			<span>颜色：<?php echo $item['color'];?></span>
		</td>
	</tr>
<?php
$tableView = new MagicTableRow( 'order','total','comment','todo' );
$tableView->rowspan(false);
foreach( $item['products'] as $row ) {
	$tableView->appendRow($row->orderId,Order::quantityFormat($row->quantity),$row->comment,"<a href=\"#\" data-id=\"{$row->purchaseId}\" data-toggle=\"modal\" data-target=\".del-confirm\">删除</a>");
}
$tableView->show();
?>
<tfoot>
<tr>
	<td colspan="4">
		<input type="hidden" name="product[<?php echo $key?>][purchaseIds]" value="<?php echo implode(':',$item['purchaseIds']);?>" />
		<span class="pull-left"><span class="text-danger">*</span>工厂产品编号：<input type="text" class="form-control input-sm" name="product[<?php echo $key?>][supplierCode]" /></span>
		<span class="pull-left"><span class="text-danger">*</span>交货日期：<input type="text" class="form-control input-sm input-date" name="product[<?php echo $key?>][deliveryDate]" readonly/></span>
		<span class="pull-left">备注：<input type="text" name="product[<?php echo $key?>][comment]" class="form-control input-sm"/></span>
		<span class="pull-left">采购总量:</span>
		<div class="input-group pull-left">
			<input type="text" name="product[<?php echo $key?>][quantity]" value="<?php echo $item['total'];?>" class="form-control input-sm num-float-only"/>
			<div class="input-group-addon">码</div>
		</div>
	</td>
</tr>
</tfoot>
</table>
<br />
<?php } ?>
<div class="text-center"><button type="submit" class="btn btn-success ordersub">立即采购</button></div>
<br>
</form>
 <div class="modal fade del-confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="exampleModalLabel">删除确认</h4>
      </div>
      <div class="modal-body">
        <p>你确定要删除吗？</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">取消</button>
        <button type="button" class="btn btn-success">确定</button>
      </div>
    </div>
  </div>
</div>
<script>seajs.use('statics/app/purchase/default/js/add.js');</script>
<script>
window.urlRemove = "<?php echo $this->createUrl('default/create',array('event'=>'remove'))?>";
</script>

<?php $this->beginContent('//layouts/_error');$this->endContent();?>


<div class="panel panel-default">
  <div class="panel-heading clearfix">
	 <span class="col-md-4">采购单号:<?php echo $order->purchaseId;?></span>
<span class="col-md-4">采购日期：<?php echo date('Y-m-d H:i', $order->createTime);?></span>
</div>
<ul class="list-group">
    <li class="list-group-item clearfix">
        <span class="col-md-12">收货地址：<?php echo $order->address;?></span>
    </li>
    <li class="list-group-item clearfix">
        <span class="col-md-12">订单备注：<?php echo htmlspecialchars($order->comment);?></span>
    </li>
</ul>
</div>
<br />

<form class="form-horizontal" method="post">
<table class="table table-condensed table-bordered">
    <colgroup><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /></colgroup>
    <thead>
    <tr>
        <td>革厂编号</td>
        <td>产品编号</td>
        <td>颜色</td>
        <td>采购数量</td>
        <td>交货日期</td>
        <td>发货数量</td>
        <td>操作</td>
    </tr>
    </thead>
<tbody>
<?php
foreach( $products as $key=>$item ){
?>
    <tr class="list-hd">
        <td><?php echo $item['productCode'];?></td>
        <td><?php echo $item['productCode'];?></td>
        <td><?php echo $item['color'];?></td>
        <td><?php echo Order::quantityFormat($item['quantity']);?></td>
        <td><?php echo $item['deliveryDate'];?></td>
        <td><div class="input-group"><input type="text" value="<?php echo Order::quantityFormat($item['quantity']);?>" name="product[<?php echo $item->purchaseProId;?>]" class="pull-left form-control input-sm"><div class="input-group-addon"><?php echo ZOrderHelper::getUnitName($item->productCode);?></div></td>
        <td><a href="#" class="del">删除</a></td>
    </tr>
<?php } ?>
</tbody>
</table>
<br />

<div class="panel panel-default">
    <br>
    <div class="form-group">
        <label class="control-label col-md-2">物流公司：</label>
        <div class="col-md-4">
            <input type="text" class="form-control input-sm" />
            <input type="hidden" name="form[logisticId]" value="0" />
            <input type="hidden" name="form[logisticsName]" value="测试公司" />
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-2"><span class="text-danger">*</span>物流单号：</label>
        <div class="col-md-4">
            <input type="text" name="form[logisticsCode]" class="form-control input-sm" />
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-2"><span class="text-danger">*</span>发货日期：</label>
        <div class="col-md-4">
            <input type="text" name="form[postTime]" class="form-control input-sm input-date" onclick="WdatePicker({minDate:'<?php echo date('Y-m-d', $order->createTime);?>',maxDate:'%y-%M-%d'})" value="<?php echo date('Y-m-d');?>" readonly/>
        </div>
    </div>
    <br>
	<?php $this->beginContent('//layouts/_error');$this->endContent();?>
    <div class="text-center"><button class="btn btn-success">立即发货</button></div><br><br>
</div>
</form>
<script>seajs.use('statics/app/purchase/default/js/post.js');</script>
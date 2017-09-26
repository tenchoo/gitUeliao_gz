<?php
/**
 * 查看工厂发货单详情
 * User: yagas
 * Date: 2016/3/8
 * Time: 10:27
 */
?>
<?php $this->beginContent('//layouts/_error');$this->endContent();?>

<div class="panel panel-default">
    <div class="panel-heading clearfix">
        <span class="col-md-4">采购单号:<?php echo $order->purchaseId;?></span>
        <span class="col-md-4">采购日期：<?php echo date('Y-m-d', $order->purchase->createTime);?></span>
    </div>
    <ul class="list-group">
        <li class="list-group-item clearfix">
            <span class="col-md-12">收货地址：<?php echo $order->purchase->address;?></span>
        </li>
        <li class="list-group-item clearfix">
            <span class="col-md-12">订单备注：<?php echo htmlspecialchars($order->purchase->comment);?></span>
        </li>
    </ul>
</div>
<br />

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
        <td>备注</td>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach( $products as $key=>$item ){
        $unit = ZOrderHelper::getUnitName($item->details->productCode);
        ?>
        <tr class="list-hd">
            <td><?php echo $item->details->supplierCode;?></td>
            <td><?php echo $item->details->productCode;?></td>
            <td><?php echo $item->details->color;?></td>
            <td><?php echo Order::quantityFormat($item->details->quantity).$unit;?></td>
            <td><?php echo $item->details->deliveryDate;?></td>
            <td><?php echo Order::quantityFormat($item->postTotal).$unit;?></td>
            <td><?php echo $item->details->comment;?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<br />

<div class="panel panel-default">
    <div class="panel-heading clearfix">
        <span class="col-md-4">物流公司:<?php echo $order->logisticsName;?></span>
        <span class="col-md-4">物流单号：<?php echo $order->logisticsCode;?></span>
        <span class="col-md-4">发货时间：<?php echo $order->createTime;?></span>
    </div>
    <ul class="list-group">
    </ul>
</div>

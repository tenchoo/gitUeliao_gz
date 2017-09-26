<div class="panel panel-default">
    <div class="panel-heading clearfix">
        <span class="col-md-4">请购单号：<?php echo $order->orderId;?></span>
        <span class="col-md-4">下单时间：<?php echo $order->createTime;?></span>
    </div>
    <ul class="list-group">
        <li class="list-group-item clearfix">
            <span class="col-md-4">客户名称：<?php echo MemberHelper::nickname($order->memberId); ?></span>
            <span class="col-md-4">联系人：<?php echo $order->name;?></span>
            <span class="col-md-4">联系电话：<?php echo $order->tel;?></span>
        </li>
        <li class="list-group-item clearfix">
            <span class="col-md-12">地址：<?php echo $order->address;?></span>
        </li>
        <li class="list-group-item clearfix">
            <span class="col-md-12">备注：<?php echo $order->memo;?></span>
        </li>
    </ul>
</div>

<br>
<table class="table table-condensed table-bordered">
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
    <?php foreach( $order->products() as $product ){?>
        <tr>
            <td><?php echo CHtml::encode($product->singleNumber);?></td>
            <td><?php echo $product->color;?></td>
            <td><?php echo Order::quantityFormat($product->total).ZOrderHelper::getUnitName($product->singleNumber);?></td>
            <td><?php echo date('Y-m-d',$product->dealTime);?></td>
            <td><?php echo CHtml::encode($product->comment);?></td>
        </tr>
    <?php }?>
    </tbody>
</table>
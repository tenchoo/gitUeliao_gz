<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<div class="panel panel-default search-panel">
    <div class="panel-body">
        <form class="pull-left form-inline">
            <input value="" name="orderid" class="form-control input-sm" placeholder="采购单号" type="text">
            <input value="" name="user" class="form-control input-sm" placeholder="采购人" type="text">
            <input value="" name="factory" class="form-control input-sm" placeholder="厂家名称" type="text">
            <input value="" name="date" class="form-control input-sm" placeholder="采购日期" type="text">
            <button class="btn btn-sm btn-default">查找</button>
        </form>
    </div>
</div>
<div class="clearfix well well-sm list-well">
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>

<table class="table table-condensed table-bordered">
    <colgroup><col width="20%"><col width="20%"><col width="20%"><col width="10%"><col width="20%"><col width="10%"></colgroup>
    <thead>
    <tr>
        <td>产品编号</td>
        <td>颜色</td>
        <td>采购数量</td>
        <td>交货日期</td>
        <td>备注</td>
        <td>操作</td>
    </tr>
    </thead>
</table>
<br>

<?php foreach( $orders as $item ): ?>
    <table class="table table-condensed table-bordered">
        <colgroup><col width="20%"><col width="20%"><col width="20%"><col width="10%"><col width="20%"><col width="10%"></colgroup>
        <tbody>
        <tr class="list-hd">
            <td colspan="6">
                <span class="first">采购单号：<?php echo $item->purchaseId; ?></span>
                <span>下单时间：<?php echo substr($item->createTime,0,10);?></span>
                <span>工厂名称：<?php echo $item->logisticsName;?></span>
                <span>采购人：<?php echo $item->user->username;?></span>
            </td>
        </tr>
        <?php
        $detail = $item->getProducts();
        $rows   = count($detail);
        foreach($detail as $index=>$product):?>
            <tr class="list-bd">
                <td><?php echo $product->details->productCode;?></td>
                <td><?php echo $product->details->color;?></td>
                <td><?php echo Order::quantityFormat($product->details->quantity).ZOrderHelper::getUnitName($product->details->productCode);?></td>
                <td><?php echo $product->details->deliveryDate;?></td>
                <td><?php echo $product->comment;?></td>
                <?php if($index == 0):?>
                    <td rowspan="<?php echo $rows;?>">
                        <?php
                        $url = $this->createUrl('view', array('id'=>$item->postId));
                        echo CHtml::link("查看",$url); ?>
                    </td>
                <?php endif;?>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
    <br>
<?php endforeach; ?>



<div class="clearfix well well-sm list-well">
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
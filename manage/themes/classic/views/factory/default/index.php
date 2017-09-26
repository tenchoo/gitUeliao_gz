<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<div class="panel panel-default search-panel">
    <div class="panel-body">
        <form class="pull-left form-inline">
            <input value="" name="orderid" class="form-control input-sm" placeholder="采购单号" type="text">
            <input value="" name="factory" class="form-control input-sm" placeholder="厂家编号" type="text">
            <button class="btn btn-sm btn-default">查找</button>
        </form>
    </div>
</div>
<div class="clearfix well well-sm list-well">
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>

<table class="table table-condensed table-bordered">
    <colgroup><col width="10%"><col width="20%"><col width="20%"><col width="20%"><col width="10%"><col width="10%"><col width="10%"></colgroup>
    <thead>
    <tr>
        <td>革厂产品编号</td>
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
        <colgroup><col width="10%"><col width="20%"><col width="20%"><col width="20%"><col width="10%"><col width="10%"><col width="10%"></colgroup>
        <tbody>
        <tr class="list-hd">
            <td colspan="7">
                <span class="first">采购单号：<?php echo $item->purchaseId; ?></span>
                <span>下单时间：<?php echo date('Y-m-d H:i:s',$item->createTime);?></span>
            </td>
        </tr>
        <?php
        $detail = $item->getProducts();
        $rows   = count($detail);
        foreach($detail as $index=>$product):?>
            <tr class="list-bd">
                <td><?php echo $product->supplierCode;?></td>
                <td><?php echo $product->productCode;?></td>
                <td><?php echo $product->color;?></td>
                <td><?php echo Order::quantityFormat($product->quantity).ZOrderHelper::getUnitName($product->productCode);?></td>
                <td><?php echo $product->deliveryDate;?></td>
                <td><?php echo $product->comment;?></td>
                <?php if($index == 0):?>
                    <td rowspan="<?php echo $rows;?>">
                        <?php
                        $url = $this->createUrl('create', array('id'=>$item->purchaseId));
                        echo CHtml::link("发货",$url); ?>
						<br>
						 <?php
                        $url = $this->createUrl('finish', array('id'=>$item->purchaseId));
                        echo CHtml::link("发货完成",$url,array('class'=>'f')); ?>
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
<script>
  seajs.use('statics/app/factory/js/list.js');
</script>
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<?php $this->beginContent('//layouts/_success');$this->endContent();?>
<form method="post" class="">
    <input type="hidden" name="form[orderId]" value="<?php echo Yii::app()->request->getQuery('id');?>" />
    <table class="table table-condensed table-bordered">
        <colgroup><col width="20%"><col width="20%"><col width="20%"><col width="20%"></colgroup>
        <thead>
        <tr class="list-hd">
            <td>产品编号</td>
            <td>颜色</td>
            <td>预订数量</td>
            <td>交货日期</td>
            <td>备注</td>
        </tr>
        </thead>
        <tbody>
        <?php foreach( $orderList->products as $item ):?>
        <tr>
            <td><?php echo $item->singleNumber;?></td>
            <td><?php echo $item->color;?></td>
            <td><?php echo Order::quantityFormat($item->num).ZOrderHelper::getUnitName($item->singleNumber);?></td>
            <td>交货日期</td>
            <td><?php echo CHtml::encode($item->remark);?></td>
        </tr>
        <?php endforeach;?>
        </tbody>
    </table>

    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <textarea name="form[reason]" class="form-control" style="width:400px"></textarea>
        </div>
    </div>

    <div class="text-center">
        <input type="submit" value="关闭采购" class="btn btn-success" />
    </div>
</form>
<div class="panel panel-default search-panel">
    <div class="panel-body">
        <form method="get" class="pull-left form-inline">
            <input type="text" placeholder="请输入产品编号" name="s" value="<?php echo Yii::app()->request->getQuery('s');?>" class="form-control input-sm" />
            <input type="text" placeholder="请输入发货单编号" name="o" value="<?php echo Yii::app()->request->getQuery('o');?>" class="form-control input-sm" />
            <input type="submit" value="查找" class="btn btn-sm btn-default" />
        </form>
    </div>
</div>
<div class="clearfix well well-sm list-well">
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
<table class="table table-condensed table-bordered">
    <colgroup><col width="25%"><col width="15%"><col width="15%"><col width="15%"><col width="15%"></colgroup>
    <thead>
    <tr>
        <td>产品编号</td>
        <td>颜色</td>
        <td>订货数量</td>
        <td>状态</td>
        <td>操作</td>
    </tr>
    </thead>
</table>
<br >

<?php foreach($orders as $row ){ ?>
    <table class="table table-condensed table-bordered">
        <colgroup><col width="25%"><col width="15%"><col width="15%"><col width="15%"><col width="15%"></colgroup>
        <thead>
        <tr class="list-hd">
            <td colspan="6"><span>发货单编号：<?php echo $row->postId;?></span><span>发货日期：<?php echo $row->postTime;?></span><span>采购单编号：<?php echo $row->purchaseId;?></span></td>
        </tr>
        </thead>

        <tbody>
        <?php foreach($row['products'] as $product):?>
        <tr>
            <td><?php echo $product->details->productCode;?></td>
            <td><?php echo $product->details->color;?></td>
            <td><?php echo Order::quantityFormat($product->quantity).ZOrderHelper::getUnitName($product->details->productCode);?></td>
		<?php if( $product->isAssign ){ ?>
			<td>已匹配</td>
			<td><?php echo CHtml::link('查看',$this->createUrl('post/assignview', array('id'=>$product->postProId)));?></td>
		<?php }else{ ?>
			<td><span style="color:#FF6000">未匹配</span></td>
			<td><?php echo CHtml::link('匹配',$this->createUrl('post/assign',array('id'=>$product->postProId)));?></td>
		<?php }?>
        </tr>
        <?php endforeach;?>
        </tbody>
    </table>
    <br>
<?php }?>
<div class="clearfix well well-sm list-well">
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
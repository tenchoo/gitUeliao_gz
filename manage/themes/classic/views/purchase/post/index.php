<div class="panel panel-default search-panel">
	<div class="panel-body">
		<form method="get" class="pull-left form-inline" action="<?php echo $this->createUrl('index')?>">
			<input type="text" placeholder="请输入产品编号" name="s" value="<?php echo $condition['productCode'];?>" class="form-control input-sm" />
			<input type="text" placeholder="请输入发货单编号" name="o" value="<?php echo $condition['postId'];?>" class="form-control input-sm" />
			<input type="text" placeholder="请输入采购单编号" name="purchaseId" value="<?php echo $condition['postTime'];?>" class="form-control input-sm" />
			<input value="<?php echo $condition['purchaseId'];?>" name="postTime" class="form-control input-sm  input-date" readonly=""  placeholder="发货日期" type="text">
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
      <td>采购数量</td>
      <td>发货数量</td>
      <td>操作</td>
    </tr>
  </thead>
  </table>
  <br >

<?php foreach( $orders as $order ){?>
<table class="table table-condensed table-bordered">
 <colgroup><col width="25%"><col width="15%"><col width="15%"><col width="15%"><col width="15%"></colgroup>
  <thead>
    <tr class="list-hd">
	  <td colspan="5"><span>发货单号：<?php echo $order->postId;?></span><span>发货日期：<?php echo $order->postTime;?></span><span>采购单号：<?php echo $order->purchaseId;?></span></td>
    </tr>
  </thead>

  <tbody>
  <?php
  $line = new MagicTableRow('singleNumber','color','buy','post','do');
  $line->filterMerge('singleNumber');
  $line->filterMerge('color');
  $line->filterMerge('buy');
  $line->filterMerge('post');

  foreach($order->getProducts() as $product):
    $detail = $product->details;
    $unit = ZOrderHelper::getUnitName($detail->productCode);

    $line->appendRow(
    		$detail->productCode,
    		$detail->color,
    		Order::quantityFormat($detail->quantity).$unit,
    		Order::quantityFormat($product->postTotal).$unit,
    		CHtml::link('查看', $this->createUrl('view',array('id'=>$order->postId)))
    		);
    endforeach;
    $line->show();
  ?>
  </tbody>
</table>
<br>
<?php }?>
<div class="clearfix well well-sm list-well">
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
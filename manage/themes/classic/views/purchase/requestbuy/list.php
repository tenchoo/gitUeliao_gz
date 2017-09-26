<div class="panel panel-default search-panel">
	<div class="panel-body">
	  <div class="pull-right form-inline"><a href="<?php echo $this->createUrl('requestbuy/addnew')?>" class="btn btn-default btn-sm">新建请购单</a></div>
		<form method="get" class="pull-left form-inline" <?php echo $this->createUrl('list');?>>
			<input type="text" placeholder="请输入产品编号" name="s" value="<?php echo Yii::app()->request->getQuery('s');?>" class="form-control input-sm" />
			<input type="text" placeholder="请输入订单编号" name="o" value="<?php echo Yii::app()->request->getQuery('o');?>" class="form-control input-sm" />
			<input type="submit" value="查找" class="btn btn-sm btn-default" />
		</form>
	</div>
</div>
<div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
<table class="table table-condensed table-bordered">
  <colgroup><col width="25%"><col width="25%"><col width="25%"><col width="25%"></colgroup>
   <thead>
    <tr class="list-hd">
     <td>产品编号</td>
     <td>颜色</td>
     <td>预订数量</td>
     <td></td>
    </tr>
   </thead>
</table>
<br>
<?php foreach ( $dataList as $item ) { ?>
<table class="table table-condensed table-bordered">
  <colgroup><col width="25%"><col width="25%"><col width="25%"><col width="25%"></colgroup>
	<tbody>
	<tr class="list-hd">
		<td colspan="4">
			<!-- <input type="checkbox" value="<?php echo $item->orderId; ?>"/> -->
			<span class="first">请购单号：<?php echo $item->orderId; ?></span>
			<span>请购时间：<?php echo date('Y-m-d',$item->createTime); ?></span>
		</td>
	</tr>
	<?php
	$begin = true;
	foreach ($item->products as $product) {
		?>
		<tr>
			<td><?php echo $product->singleNumber; ?></td>
			<td><?php echo $product->color; ?></td>
			<td><?php echo Order::quantityFormat($product->total);
				echo $product->unitName; ?></td>
			<?php
			if ($begin) {
				$begin = false;
				?>
				<td rowspan="<?php echo count($item->products); ?>"><?php $this->widget('purchase.widgets.RequestBuyAction', array('orderId' => $item->orderId, 'state' => $item->state, 'isBuyList'=>false)); ?></td>
			<?php } ?>
		</tr>
	<?php }?>
	</tbody>
</table>
<br>
<?php }?>
<div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>


<div class="modal fade add-confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">确认加入采购</h4>
      </div>
      <div class="modal-body">
        <p>您确定要加入采购吗？</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" class="btn btn-success">确定</button>
      </div>
    </div>
  </div>
</div>
<script>seajs.use('statics/app/purchase/requestbuy/js/index.js');</script>
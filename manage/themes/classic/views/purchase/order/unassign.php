<div class="panel panel-default search-panel">
    <div class="panel-body">
        <form method="get" class="pull-left form-inline" action="<?php echo $this->createUrl('unassign')?>">
            <input type="text" placeholder="请输入产品编号" name="s" value="<?php echo $serial;?>" class="form-control input-sm" />
            <input type="text" placeholder="请输入订单编号" name="o" value="<?php echo $order;?>" class="form-control input-sm" />
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
        <td>状态</td>
    </tr>
    </thead>
</table>
<br>
<?php //var_dump( $orderList );exit;
foreach ( $orderList as $item ) { ?>
<table class="table table-condensed table-bordered">
    <colgroup><col width="25%"><col width="25%"><col width="25%"><col width="25%"></colgroup>
    <tbody>
    <tr class="list-hd">
        <td colspan="4"><span class="first">订单编号：<?php echo $item['orderId']; ?></span></td>
    </tr>
    <?php
    $begin = true;
	
    foreach ( $item['products'] as $product) {
        ?>
        <tr>
            <td><?php echo $product->productCode; ?></td>
            <td><?php echo $product->color; ?></td>
            <td><?php echo Order::quantityFormat($product->quantity);
               // echo $product->unitName; ?></td>
			<td>
			<?php if( $product->isAssign ){ ?>
			已匹配
			<?php }else{ ?>
			<span style="color:#FF6000">未匹配</span>
			 <?php } ?>
			</td>     
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
<script>seajs.use('statics/app/purchase/order/js/index.js');</script>
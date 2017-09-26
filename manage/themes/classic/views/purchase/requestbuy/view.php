<div class="panel panel-default">
  <div class="panel-heading clearfix">
	 <span class="col-md-4">请购单号：<?php echo $order->orderId;?></span>
	 <span class="col-md-4">请购时间：<?php echo date( 'Y-m-d H:i:s', $order->createTime );?></span>
	 <span class="col-md-4">状态：<?php echo $order->stateTitle( $order->state );?></span>
	</div>
	<ul class="list-group">
	  <li class="list-group-item clearfix">
		  <span class="col-md-4">请购人：<?php echo $order->userName;?></span>
		  <span class="col-md-4">请购原因：<?php echo $order->cause;?></span>
	  </li>
	  <li class="list-group-item clearfix">
	    <span class="col-md-12">备注：<?php echo $order->comment;?></span>
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
	<td><?php echo $product->singleNumber;?></td>
	<td><?php echo $product->color;?></td>
	<td><?php echo Order::quantityFormat($product->total);?></td>
	<td><?php echo date( 'Y-m-d', $product->dealTime );?></td>
	<td><?php echo $product->comment;?></td>
	</tr>
	<?php }?>
    </tbody>
</table>
<br>
<?php if( !empty( $oplog )) { ?>
<div class="panel panel-default">
  <div class="panel-heading clearfix">
	 <span class="col-md-4">操作日志</span>
	</div>
	<ul class="list-group">
	<?php foreach ( $oplog as $val ) { ?>
	  <li class="list-group-item clearfix">
	    <span class="col-md-12">
			<?php echo $val['createTime'];?> &nbsp;&nbsp;&nbsp;&nbsp;
			<?php echo $val['username'];?>&nbsp;&nbsp;&nbsp;&nbsp;
			<?php echo $val['codeTitle'];?>&nbsp;&nbsp;&nbsp;&nbsp;
			<?php echo $val['remark'];?>
		</span>
	  </li>
	  <?php }?>
	</ul>
</div>
<br>
<?php }?>

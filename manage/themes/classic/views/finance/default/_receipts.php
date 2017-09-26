<div class="panel panel-default">
	<div class="panel-heading clearfix">
	 <span class="col-md-4">收款记录</span>
	</div>
	<ul class="list-group">
	 <?php if( !empty( $receipts['list'] ) ) { ?>
	 <li class="list-group-item clearfix">
	 <span class="col-md-2">收款时间</span>
	 <span class="col-md-2">确认收款人</span>
	 <span class="col-md-6">收款金额</span>
	 </li>
	 <?php foreach( $receipts['list'] as $val) :	?>
	  <li class="list-group-item clearfix">
	 <span class="col-md-2"><?php echo $val['createTime'];?></span>
	 <span class="col-md-2"><?php echo $val['username'];?></span>
	 <span class="col-md-6"><?php echo Order::priceFormat($val['amount'])?></span>
	 </li>
	<?php endforeach;?>
	  <li class="list-group-item clearfix">
	  <span class="col-md-12"><div class="pull-right">总已收款：<?php echo Order::priceFormat($receipts['totalReceipt'])?> 元</div></span>
	 </li>
	 <?php }else{ ?>
	 <li class="list-group-item clearfix">
	  <span class="col-md-12">暂无收款记录</span>
	 </li>
	 <?php }?>
	</ul>
</div>
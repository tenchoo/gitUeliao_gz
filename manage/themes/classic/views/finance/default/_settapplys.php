<div class="panel panel-default">
	<div class="panel-heading clearfix">
	 <span class="col-md-4">申请结算记录</span>
	</div>
	<ul class="list-group">
	<?php if( !empty( $settApplys ) ) { ?>
	 <li class="list-group-item clearfix">
	 <span class="col-md-1">申请时间</span>
	 <span class="col-md-1">金额</span>
	 <span class="col-md-1">审核结果</span>
	 <span class="col-md-1">申请人</span>
	 <span class="col-md-3">申请理由</span>
	 <span class="col-md-1">审核时间</span>
	 <span class="col-md-1">审核人</span>
	 <span class="col-md-3">审核理由</span>
	 </li>
	 <?php foreach( $settApplys as $val) :	?>
	  <li class="list-group-item clearfix">
	 <span class="col-md-1"><?php echo $val['createTime'];?></span>
	 <span class="col-md-1"><?php echo Order::priceFormat($val['amount'])?></span>
	 <span class="col-md-1"><?php echo $val['stateTitle'];?></span>
	 <span class="col-md-1"><?php echo $val['username'];?></span>
	 <span class="col-md-3"><?php echo $val['applyCause'];?></span>
	 <span class="col-md-1"><?php if( $val['state']>0 ){ echo $val['checkTime']; }?></span>
	 <span class="col-md-1"><?php echo $val['checkUsername'];?></span>
	 <span class="col-md-3"><?php echo $val['checkCause'];?></span>
	 </li>
	<?php endforeach;?>
	 <?php }else{ ?>
	 <li class="list-group-item clearfix">
	  <span class="col-md-12">暂无记录</span>
	 </li>
	 <?php }?>
	</ul>
</div>
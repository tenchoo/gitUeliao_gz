<div class="panel panel-default">
	<div class="panel-heading clearfix">
	 <span class="col-md-4"><?php echo $model->orderType;?>：<?php echo $model->orderId;?></span>
	 <span class="col-md-4">下单日期：<?php echo $model->createTime;?></span>
		<span class="col-md-4">业务员：<?php echo $member['salesman'];?></span>
	</div>
	<ul class="list-group">
	 <li class="list-group-item clearfix">
		<span class="col-md-4">客户名称：<?php echo $member['companyname'];?></span>
		<span class="col-md-4">联系人：<?php echo $model->name;?>（<?php echo $model->tel;?>）</span>
		<span class="col-md-4">提货方式：<?php echo $model->deliveryMethod;?></span>
	 </li>
	 <li class="list-group-item clearfix">
		<span class="col-md-12">订单备注：<?php echo $model->memo;?></span>
	 </li>
	 <?php if( !empty($model->warehouseId) ){?>
	 <li class="list-group-item clearfix">
		<span class="col-md-12">发货仓库：<?php echo $model->warehouseId;?></span>
	 </li>
	 <?php }?>
	</ul>
</div>

 <div class="panel panel-default">
     <ul class="list-group">
		<?php foreach( $trace as $dval ){ ?>
	    <li class="list-group-item clearfix">
			<?php echo $dval['createTime'];?> <?php echo $dval['subject'];?>
		</li>	
		<?php } ?>	   
    </ul>
  </div>
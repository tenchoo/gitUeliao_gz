<link rel="stylesheet" href="/themes/classic/statics/app/warehouse/css/style.css" />
<form  method="post" action="" class="form-horizontal alloction">
<div class="panel panel-default">
  <div class="panel-heading clearfix">
    <span class="col-md-4">调拨单号：<?php echo $allocationId;?></span>
	<span class="col-md-4">原仓库：<?php echo $warehouse;?></span>
	<span class="col-md-4">目标仓库：<?php echo $targetWarehouse;?></span>
  </div>
	<ul class="list-group">
		<li class="list-group-item clearfix">
			<span class="col-md-4">订单编号：<?php echo $orderId;?>
			<?php if($orderState == '7'){?>
			<span class="text-danger">（订单已取消）</span>
			<?php }?>
			</span>
			<span class="col-md-4">下单时间：<?php echo $orderTime;?></span>
		</li>
		<li class="list-group-item clearfix">
			<span class="col-md-4">来源分拣单号：<?php echo $packingId;?></span>
			<span class="col-md-4">分拣人：<?php echo $packinger;?></span>
			<span class="col-md-4">分拣时间：<?php echo $packingTime;?></span>
		</li>
	</ul>
</div>
 <?php if(isset($applyInfo['opstate']) && $applyInfo['opstate'] == tbWarehouseMessage::OP_MODIFY ){?>
<table class="table table-condensed table-bordered">
   <thead>
    <tr>
	 <td>产品编号</td>
	 <td>颜色</td>
     <td>产品批次</td>
	 <td>仓库号</td>
	 <td>调拨数量</td>
	 <td width="15%">实际调拨数量</td>
	  </tr>
	  </thead>
	  <tbody>
	 <?php foreach( $detail as $pval ):?>
	  <tr>
     <tr class="order-list-bd">
	 <td><?php echo $pval['singleNumber'];?></td>
	 <td><?php echo $pval['color'];?></td>
	 <td><?php echo $pval['productBatch'];?></td>
	  <td><?php echo $pval['positionTitle'];?></td>
	 <td><?php echo Order::quantityFormat( $pval['num'] ),$pval['unit'];?></td>
	 <td>
	  <div class="input-group title-group">
	 <input type="text" name="data[products][<?php echo $pval['id']?>]" value="<?php echo $pval['num'];?>" class="form-control input-sm num-float-only"/><div class="input-group-addon"><?php echo $pval['unit'];?></div>
	 </div>
	 </td>
	</tr>
	<?php endforeach;?>
	 </table>
 <?php }else{ ?>
 <table class="table table-condensed table-bordered">
   <thead>
    <tr>
	 <td>产品编号</td>
	 <td>颜色</td>
     <td>产品批次</td>
	 <td>调拨数量</td>
	  </tr>
	  </thead>
	  <tbody>
	 <?php foreach( $detail as $_detail ):?>
	 <?php foreach( $_detail as $pval ):?>
	  <tr>
     <tr class="order-list-bd">
	 <td><?php echo $pval['singleNumber'];?></td>
	 <td><?php echo $pval['color'];?></td>
	 <td><?php echo $pval['productBatch'];?></td>
	 <td><?php echo Order::quantityFormat( $pval['total'] ),$pval['unit'];?></td>
	  </tr>
	<?php endforeach;?>
	<?php endforeach;?>
	 </table>
 <?php }?>
<br>
<?php if($orderState == '7'){?>
	<div align="center">
		<button class="btn btn-default">关闭调拨单</button>
	</div>
<?php }else{ ?>
<div class="panel panel-default">
<br>
	   <div class="form-group">
	     <span class="control-label col-md-5">驾驶员：</span>
	     <div class="col-md-7 form-inline">
	       <?php echo CHtml::dropDownList('data[driverId]',$params['driverId'],$drivers,array('empty'=>'请选择驾驶员','class'=>'form-control input-sm'))?>
	     </div>
	   </div>
     <div class="form-group">
	     <span class="control-label col-md-5">车辆编号：</span>
	     <div class="col-md-7 form-inline">
	       <?php echo CHtml::dropDownList('data[vehicleId]',$params['vehicleId'],$vehicle,array('empty'=>'请选择车辆','class'=>'form-control input-sm'))?>
	     </div>
	   </div>
</div>
<?php if( is_array($applyInfo) ){?>
 <div class="panel panel-default">
    <div class="panel-heading clearfix">
	<span class="text-danger">
	<?php echo $applyInfo['title']?><br>
	<?php echo $applyInfo['content']?></span>
	</div>
  </div>
  <br/>
<?php }?>
	 <?php $this->beginContent('//layouts/_error');$this->endContent();?><br/>
	<div align="center">
	<?php if($applyInfo['opstate'] == tbWarehouseMessage::OP_CLOSE ){
		Yii::app()->session->add('allocation_cancle',$data['orderId']);
	?>
		<button class="btn btn-default">关闭调拨</button>
	<?php }else if($applyInfo['opstate'] == tbWarehouseMessage::OP_HOLDON ){?>
		<button class="btn btn-success" disabled >确认调拨</button>
	<?php }else{ ?>
		<button class="btn btn-success">确认调拨</button>
	<?php }?>
	</div>
 </form>
 <?php }?>
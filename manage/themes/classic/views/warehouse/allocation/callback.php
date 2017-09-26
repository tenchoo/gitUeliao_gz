<link rel="stylesheet" href="/themes/classic/statics/app/warehouse/css/style.css" />
<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />

<div class="panel panel-default">
    <div class="panel-heading clearfix">
	<span class="col-md-4">调拨仓库：<?php echo $warehouse[$data['warehouseId']];?></span>
	<span class="col-md-4">目标仓库：<?php echo $warehouse[$data['targetWarehouseId']];?> </span>
	<span class="col-md-4">订单编号：<?php echo $data['orderId'];?></span>
	</div>
</div>
	<table class="table table-condensed table-bordered">
   <thead>
    <tr>
	 <td>产品编号</td>
	 <td>颜色</td>
     <td width="20%">仓位号</td>
	 <td>产品批次</td>
     <td>数量</td>
	 </tr>
	 </thead>
	<tbody>
	 <?php foreach( $data['products'] as $key=>$pval) :?>
     <tr>
	 <td><?php echo $pval['singleNumber'];?></td>
	 <td><?php echo $pval['color'];?></td>
	 <td class="col-md-2"><?php echo $pval['positionTitle'];?></td>
	 <td class="col-md-2"><?php echo $pval['productBatch'];?></td>
     <td  class="col-md-2"><?php echo $pval['num'];?> <?php echo $pval['unitName'];?> </td>
	  </tr>
	<?php endforeach;?>
	</tbody>
</table>
<br/>
<form class="form-horizontal alloction" method="post" action="">
	 <div class="panel panel-default">
	 	<br>
	   <div class="form-group">
	     <span class="control-label col-md-5">驾驶员：</span>
	     <div class="col-md-7 form-inline">
	       <?php echo CHtml::dropDownList('driverId',$data['driverId'],$drivers,array('empty'=>'请选择驾驶员','class'=>'form-control input-sm'))?>
	     </div>
	   </div>
	   <div class="form-group">
	     <span class="control-label col-md-5">车辆编号：</span>
	     <div class="col-md-7 form-inline">
	       <?php echo CHtml::dropDownList('vehicleId',$data['vehicleId'],$vehicle,array('empty'=>'请选择车辆','class'=>'form-control input-sm'))?>
	     </div>
	   </div>
	</div>
	 <br/>
	 <?php $this->beginContent('//layouts/_error');$this->endContent();?><br/>
	<div align="center">
		<button class="btn btn-success">立即调拨</button>
	</div>
 </form>
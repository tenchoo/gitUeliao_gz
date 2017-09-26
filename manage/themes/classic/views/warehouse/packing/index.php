<link rel="stylesheet" href="/themes/classic/statics/app/warehouse/css/style.css" />
<div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl(Yii::app()->getController()->getAction()->id);?>">
     <div class="form-group">
	  <?php echo CHtml::dropDownList('warehouseId',$warehouseId,$warehouse,array('class'=>'form-control input-sm','empty'=>'请选择分拣仓库'));?>
      <input type="text" name="orderId" value="<?php echo $orderId;?>" placeholder="订单编号" class="form-control input-sm" />
	    <!--input type="text" name="singleNumber" value="<?php echo $singleNumber;?>" placeholder="产品编号" class="form-control input-sm" /-->
     </div>
     <button class="btn btn-sm btn-default">查找</button>
    </form>
   </div>
  </div>
  <div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <table class="table table-condensed table-bordered">
  <colgroup><col><col width="15%"><col width="15%"><col width="15%"><col width="15%"><col width="15%"></colgroup>
   <thead>
    <tr>
     <td>产品编号</td>
     <td>颜色</td>
	 <td>仓位</td>
     <td>产品批次</td>
     <td>分配数量</td>
     <td>操作</td>
    </tr>
   </thead>
   </table>
   <br>
   <?php foreach(  $list as $val ){?>
   <table class="table table-condensed table-bordered">
  <colgroup><col><col width="15%"><col width="15%"><col width="15%"><col width="15%"><col width="15%"></colgroup>
   <tbody>
	<tr class="list-hd">
    <td colspan="6">
		<span class="first">分拣单号：<?php echo $val['packingId'];?></span>
		<span>订单编号：<?php echo $val['orderId'];?></span>
		<span>分拣仓库：<?php echo $val['warehouse'];?></span>
		<span>调拨仓库：<?php echo $val['deliveryWarehouse'];?></span>
		<span>分配分拣员：<?php echo $val['packinger'];?></span>
	  </td>
  </tr>
	<?php
	$count = count($val['distribution']);
	foreach( $val['distribution'] as $k=>$dval  ){
	?>
	 <tr >
	 <td><?php echo $dval['singleNumber'];?></td>
	 <td><?php echo $dval['color'];?></td>
	 <td><?php echo $dval['positionTitle'];?></td>
	 <td><?php echo $dval['productBatch'];?></td>
	 <td><?php echo Order::quantityFormat( $dval['distributionNum'] );?><?php echo ZOrderHelper::getUnitName($dval['singleNumber']);?></td>
	<?php if( $k==0 ){?>
     <td rowspan="<?php echo $count;?>">
		<a href="<?php echo $this->createUrl('packing',array('id'=>$val['packingId']));?>">分拣</a><br>
		<a href="<?php echo $this->createUrl('print',array('id'=>$val['packingId']));?>" class="print">打印分拣信息</a>
	 </td>
	<?php }?>
    </tr>
   <?php }?>
   </tbody>
  </table>
  <br>
   <?php }?>

  <div class="clearfix well well-sm list-well">
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
<script>seajs.use('statics/app/order/js/applypricelist.js');</script>
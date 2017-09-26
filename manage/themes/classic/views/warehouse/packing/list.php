<link rel="stylesheet" href="/themes/classic/statics/app/warehouse/css/style.css" />
  <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl(Yii::app()->getController()->getAction()->id);?>">
     <div class="form-group">
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
  <colgroup><col width="20%"><col width="20%"><col width="15%"><col width="15%"><col width="15%"><col width="15%"></colgroup>
   <thead>
    <tr>
     <td>产品编号</td>
     <td>分配数量</td>
     <td>仓位号</td>
     <td>产品批次</td>
     <td>分拣数量</td>
     <td>操作</td>
    </tr>
   </thead>
   </table>
   <br>
   <?php foreach(  $list as $val  ){ ?>
   <table class="table table-condensed table-bordered">
   <colgroup><col width="20%"><col width="20%"><col width="15%"><col width="15%"><col width="15%"><col width="15%"></colgroup>
   <tbody>
	<tr class="list-hd">
    <td colspan="6">
		<span class="first">分拣单号：<?php echo $val['packingId'];?></span>
		<span>订单编号：<?php echo $val['orderId'];?></span>
		<!-- <span>分拣仓库：<?php //echo $val['warehouse'];?></span> -->
		<span>分拣日期：<?php echo date('Y-m-d',strtotime($val['packingTime']));?></span>
		<span>操作人：<?php echo $val['operator'];?></span>
	  </td>
  </tr>
  <?php
	$i = 0;
	$val['distribution'] = array_values($val['distribution']);
	foreach( $val['distribution'] as $dkey=>$dval  ){ $k = 0;
	
	 if( isset($dval['pack'])){
		foreach ( $dval['pack'] as $p) {
			$c = count($p['pack']);
			foreach ( $p['pack'] as $key =>$pack ){
				$i ++;$k ++;
	?>
	<tr>
	<?php if($k=='1'){?>
	<td rowspan="<?php echo $dval['rows'] ;?>" class="packing-info">
	<?php echo $dval['singleNumber'];?>&nbsp;&nbsp;<?php echo $dval['color'];?>
	</td>
	<td rowspan="<?php echo $dval['rows'] ;?>"><?php echo Order::quantityFormat( $dval['total'] );?> <?php echo $dval['unit'];?></td>
	<?php } ?>
	<?php if($key=='0'){?>
		<td rowspan="<?php echo $c ;?>"><?php echo $p['positionTitle'];?></td>
	<?php } ?>
		<td><?php echo $pack['productBatch'];?></td>
		<td><?php echo Order::quantityFormat( $pack['packingNum'] );?> <?php echo $dval['unit'];?></td>
	<?php if( $dkey=='0' && $i == '1' ){?>
     <td rowspan="<?php echo $val['rows'];?>">
	 <?php if($val['state'] == '10'){ ?>
		已关闭
	<?php }else{ ?>
		<a href="<?php echo $this->createUrl('view',array('id'=>$val['packingId']));?>">查看</a><br/>
		<a href="<?php echo $this->createUrl('print',array('id'=>$val['packingId']));?>" class="print">打印分拣单</a>
		<!-- a href="<?php //echo $this->createUrl('print',array('id'=>$val['packingId']));?>">打印标识</a-->
	<?php }?>
	 </td>
	<?php }?>
    </tr>
	<?php }}}else{ ?>
	<tr>
	<td rowspan="<?php echo $dval['rows'] ;?>" class="packing-info">
		<?php echo $dval['singleNumber'];?>&nbsp;&nbsp;<?php echo $dval['color'];?>
	</td>
	<td rowspan="<?php echo $dval['rows'] ;?>"><?php echo Order::quantityFormat( $dval['total']);?><?php echo $dval['unit'];?></td>
	 <td></td><td></td><td></td>
	 <?php if($dkey=='0'){?>
	 <td rowspan="<?php echo $val['rows'];?>">
		 <?php if($val['state'] == '10'){ ?>
		已关闭
	<?php }else{ ?>
		<a href="<?php echo $this->createUrl('view',array('id'=>$val['packingId']));?>">查看</a><br/>
		<a href="<?php echo $this->createUrl('print',array('id'=>$val['packingId']));?>" class="print">打印分拣单</a>
		<!-- a href="<?php //echo $this->createUrl('print',array('id'=>$val['packingId']));?>">打印标识</a-->
	<?php }?>
	 </td>
	 <?php }?>
	 </tr>
	<?php }?>
   <?php }?>
    </tbody>
  </table>
  <br>
   <?php }?>
  <div class="clearfix well well-sm list-well">
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
  <script>seajs.use('statics/app/order/js/applypricelist.js');</script>
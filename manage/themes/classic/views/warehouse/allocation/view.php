<div class="panel panel-default">
    <div class="panel-heading clearfix">
	<span class="col-md-4">调拨单号：<?php echo $data['allocationId']?></span>
	<span class="col-md-4">订单编号：<?php echo ($data['orderId'])?$data['orderId']:'0000'?><?php if( $data['isCallback'] == '1' ){ ?> ( 回调 )<?php }?> </span>
	<?php if( !empty( $data['orderId'] ) && !empty( $data['orderTime'] ) ) { ?>
	<span class="col-md-4">下单时间：<?php echo $data['orderTime'];?></span>
	<?php }?>
	
	</div>
    <ul class="list-group">
		<li class="list-group-item clearfix">
			<span class="col-md-4">原仓库：<?php echo $data['warehouse']?></span>
			<span class="col-md-4">调拨人：<?php echo $data['userName']?></span>
			<span class="col-md-4">调拨时间：<?php echo $data['createTime']?></span>
		</li>
	    <li class="list-group-item clearfix">
			<span class="col-md-4">目标仓库：<?php echo $data['targetWarehouse']?></span>
			<span class="col-md-4">确认调拨人：<?php echo $data['comfirmUser']?></span>
			<span class="col-md-4">确认调拨时间：<?php echo $data['comfirmTime']?></span>
		</li>
		<li class="list-group-item clearfix">
			<span class="col-md-4">司机：<?php echo $data['driverName']?></span>
			<span class="col-md-4">车辆：<?php echo $data['plateNumber']?></span>
		</li>
    </ul>
  </div>
  <h2 class="h3">调拨信息</h2><br>
<table class="table table-condensed table-bordered">
   <thead>
    <tr>
	 <td>产品编号</td>
	 <td>颜色</td>
	 <td>分配数量</td>
	 <td>调拨总数量</td>
	 <td>仓位</td>
	 <td>产品批次</td>
     <td>调拨数量</td>
	  </tr>
	</thead>
	<tbody>
<?php foreach( $data['detail'] as $detail) :$k=0;?>
<?php foreach( $detail['positions'] as $dval) : $i=0;$c = count($dval);?>
<?php foreach( $dval as $ddval) : $i++;$k++;?>
<tr>
<?php if( $k == '1') { ?>
 <td rowspan="<?php echo $detail['rowspan'];?>"><?php echo $detail['singleNumber']?></td>
 <td rowspan="<?php echo $detail['rowspan'];?>"><?php echo $detail['color']?></td>
 <td rowspan="<?php echo $detail['rowspan'];?>">
	<?php echo ($data['orderId'])?Order::quantityFormat( $detail['distributionNum'] ):'-'?></td>
 <td rowspan="<?php echo $detail['rowspan'];?>"><?php echo Order::quantityFormat( $detail['totalNum'] );?></td>
<?php }?>
<?php if( $i == '1') { ?>
 <td rowspan="<?php echo $c;?>"><?php echo $ddval['positionTitle']?></td>
<?php }?>
 <td><?php echo $ddval['productBatch']?></td>
 <td><?php echo Order::quantityFormat( $ddval['num'] );?></td>
</tr>
<?php endforeach;?>
<?php endforeach;?>
<?php endforeach;?>
	</tbody>
</table>
  <h2 class="h3">入库信息</h2><br>
 <table class="table table-condensed table-bordered">
   <thead>
    <tr>
	 <td>产品编号</td>
	 <td>颜色</td>
	 <td>入库总数量</td>
	 <td>仓位</td>
	 <td>产品批次</td>
   <td>入库数量</td>
	  </tr>
	</thead>
	<tbody>
<?php foreach( $input as $pval) : $c = count( $pval['detail']);?>
<?php foreach( $pval['detail'] as $k=>$dval) :?>
	  <tr>
	 <?php if($k == '0') :?>
	 <td rowspan="<?php echo $c?>"><?php echo $pval['singleNumber'];?></td>
	 <td rowspan="<?php echo $c?>"><?php echo $pval['color'];?></td>
	 <td rowspan="<?php echo $c?>"><?php echo Order::quantityFormat( $pval['total'] );?></td>
	 <?php endif;?>
	 <td><?php echo $dval['positionName'];?></td>
	 <td><?php echo $dval['productBatch'];?></td>
	 <td><?php echo Order::quantityFormat( $dval['num'] );?></td>
	  </tr>
<?php endforeach;?>
<?php endforeach;?>

	</tbody>
</table>
<br/><br/>
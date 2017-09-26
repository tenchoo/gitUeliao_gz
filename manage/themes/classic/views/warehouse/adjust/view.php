<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<div class="panel panel-default">
	<div class="panel-heading clearfix">
	  <span class="col-md-4">调整单号：<?php echo $adjustId;?></span>
		<span class="col-md-4">操作时间：<?php echo $createTime;?></span>
		<span class="col-md-4">操作员：<?php echo $username;?></span>
	</div>
	<ul class="list-group">
		<li class="list-group-item clearfix">
			<span class="col-md-4">产品编号：<?php echo $singleNumber;?></span>
			<span class="col-md-4">调整总数：<?php echo $num.' '.$unit;?></span>
		</li>
		<li class="list-group-item clearfix">
			<span class="col-md-12">备注：<?php echo $remark;?></span>
		</li>
	</ul>
</div>
<br>
	<table class="table table-condensed table-bordered table-hover">
   <thead>
    <tr>
	 <td>仓库</td>
	 <td>仓位</td>
	 <td>调整数量</td>
	 <td>调整前产品批次</td>
	 <td>调整后产品批次</td>
	  </tr>
	  </thead>
	  <tbody>
	 <?php foreach( $detail as $val):?>
      <tr class="order-list-bd">
		 <td> <?php echo $val['warehouse'];?></td>
		 <td> <?php echo $val['positionName'];?></td>
		 <td> <?php echo $val['num'].' '.$unit;?></td>
		 <td> <?php echo $val['oldbatch'];?></td>
		  <td> <?php echo $val['batch'];?></td>
	  </tr>
	<?php  endforeach;?>
	 </table>
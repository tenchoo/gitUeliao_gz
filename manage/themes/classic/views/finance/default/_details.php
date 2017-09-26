<?php $days = date( "t",strtotime( $month ) );?>
<nav class="navbar navbar-default">
	   <ul class="nav navbar-nav">
	   <li class="active" data-group="all"> <a href="javascript:;" style="padding-left: 7px;padding-right: 8px" > 全部 </a></li>
		<?php for( $i= 1;$i<=$days;$i++ ){ ?>
		<li data-group="<?php echo $i;?>"> <a href="javascript:;" style="padding-left: 7px;padding-right:8px"> <?php echo $i;?></a></li>
		<?php }?>
      </ul>
</nav>
<table class="table-condensed table-bordered" style="width:100%;">
	<thead>
    <tr>
	 <td width="250px;">结算单号</td>
	 <td width="250px;">订单编号</td>
	 <td width="250px;">结算金额（元）</td>
	 <td >生成时间</td>
	</tr>
    </thead>
</table>
<?php $count = count( $detail );
	if( $count >8 ) {?>
<div style="overflow-x: auto; overflow-y: auto; height: 250px; width:100%;">
<?php }?>
<br>
<table class="table table-condensed table-bordered order-detail table-hover">
<tbody class="list-page-body">
    <?php foreach( $detail as $pval) :	?>
    <tr class="list-body-bd" data-group="<?php echo (int)date('d',strtotime($pval['createTime']));;?>">
    <td width="250px;"><?php echo $pval['settlementId'];?></td>
	<td width="250px;"><?php echo $pval['orderId'];?></td>
	<td width="250px;"><?php echo Order::priceFormat($pval['realPayment']);?></td>
	<td><?php echo $pval['createTime'];?></td>
    </tr>
     <?php endforeach;?>
    </tbody>
</table>
<?php if( $count >8 ) {?>
</div>
<?php }?>
<br>
<table class="table table-condensed table-bordered">
	<tr>
	<td colspan="8">
		 <div class="pull-right form-inline">
		<span>总金额 ： <?php echo Order::priceFormat( $realPayment );?></span>
		</div>
	</td>
	</tr>
</table>
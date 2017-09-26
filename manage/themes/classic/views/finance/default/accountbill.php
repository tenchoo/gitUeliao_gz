<?php $this->beginContent('//layouts/_error2');$this->endContent();?>
<div class="panel panel-default search-panel">
  <div class="panel-body">
  <form action="<?php echo $this->createUrl('accountbill');?>" class="pull-left form-inline">
	<input type="text" autocomplete="off" data-api="/statistic/ajax/member/" data-search="q=%s&amp;t=2" data-suggestion="searchmember" class="form-control input-sm" placeholder="客户名称" value="<?php echo $memberName; ?>" name="memberName">
	&nbsp;&nbsp;
		<input id="t1" name="t1"  type="text" value="<?php echo $t1; ?>" readonly="readonly" maxlength="20"  class="form-control input-sm input-date" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'#F{$dp.$D(\'t2\')}'})"/>
       到
        <input id="t2" name="t2" type="text" readonly="readonly" maxlength="20"  value="<?php echo $t2; ?>"  class="form-control input-sm input-date" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(\'t1\')}',maxDate:'%y-%M-%d'})"/>
	<input type="hidden" value="<?php echo $memberId; ?>" name="memberId">
	<button class="btn btn-sm btn-default"  id="btn-add" <?php echo $memberId>0?'':'disabled';?> >查找</button>
	</form>
  </div>
</div>

<?php if( isset( $payments ) ){ ?>
<div class="panel panel-default">
	<div class="panel-heading clearfix">
	 <span class="col-md-4">客户名称：<?php echo $memberName;?></span>
	 <span class="col-md-4">下单时间：<?php echo $t1.'到'.$t2;?></span>
	  <span class="col-md-4 pull-right">
	 <div class="pull-right">
	 <a class="btn btn-sm btn-warning" href="<?php echo $excelUrl;?>">导出EXCEL</a>
	 </div>
	</span>
	</div>
	<ul class="list-group">
	 <li class="list-group-item clearfix">
	 <span class="col-md-4">总成交金额：<?php echo $payments;?></span>
	 <span class="col-md-4">总已收款：<?php echo $receipt;?></span>
	 <span class="col-md-4">总未收款：<?php echo $notReceive;?></span>
	 </li>
	  <li class="list-group-item clearfix">
		<span class="col-md-4">总成交单数：<?php echo $count;?></span>
		<span class="col-md-4">总取消单数：<?php echo $cancleNum;?></span>
	 </li>
	</ul>
</div>
<br>
 <table class="table table-bordered">
    <colgroup><col width="10%"/><col width="10%"/><col width="10%"/><col width="10%"/><col width="10%"/><col width="10%"/><col width="10%"/><col width="10%"/><col width="10%"/><col/></colgroup>
   <thead>
    <tr>
     <td>订单编号</td>
	 <td>结算单号</td>
	 <td>结算时间</td>
	 <td>总金额</td>
	 <td>已收款</td>
	  <td>结算完成</td>
	  <td>产品</td>
      <td>单价</td>
      <td>数量</td>
	  <td>小计</td>
    </tr>
   </thead>
 <tbody>
  <?php  foreach( $orders as $index=>$val ):
   $rows = count( $val['products'] );
   	foreach ( $val['products'] as $key=>$pval ):
	?>
	 <tr <?php if( ($index & 1) != 0 ) {?>  class="alert-warning" <?php }?>>
	<?php if($key == 0):?>
      <td rowspan="<?php echo $rows;?>"><?php echo $val['orderId'];?></td>
	  <td rowspan="<?php echo $rows;?>"><?php echo $val['settlementId'];?></td>
	  <td rowspan="<?php echo $rows;?>"><?php echo $val['settTime']?></td>
	  <td rowspan="<?php echo $rows;?>"><?php echo $val['realPayment']?>
		<?php if( $val['freight']>0 ){?>
		<br/>（运费<?php echo $val['freight']?>元）
		<?php }?>
	  </td>
	  <td rowspan="<?php echo $rows;?>"><?php echo $val['receipt'];?></td>
	  <td rowspan="<?php echo $rows;?>"><?php echo $val['isDone'];?></td>
	<?php endif;?>
	  <td><?php echo $pval['singleNumber']?><br/><?php echo $pval['color'];?></td>
	  <td><?php echo $pval['price']?></td>
	  <td><?php echo $pval['num'];?></td>
	  <td><?php echo $pval['subPrice'];?></td>
	  </tr>
	  <?php endforeach; ?>
 <?php endforeach; ?>
  </tbody>
   </table>
  <br>

<?php if(!empty( $cancleOrders )){?>
	 <table class="table table-bordered alert-danger">
    <colgroup><col width="10%"/><col width="10%"/><col width="10%"/><col width="10%"/><col width="10%"/><col width="10%"/><col width="10%"/><col width="10%"/><col width="10%"/><col/></colgroup>
   <thead>
   <tr><td colspan="10"><span class="first">已取消的订单</span></td></tr>
    <tr>
     <td>订单编号</td>
	 <td>结算单号</td>
	 <td>结算时间</td>
	 <td>总金额</td>
	 <td>已收款</td>
	  <td>结算完成</td>
	  <td>产品</td>
      <td>单价</td>
      <td>数量</td>
	  <td>小计</td>
    </tr>
   </thead>
 <tbody>
  <?php  foreach( $cancleOrders as $index=>$val ):
   $rows = count( $val['products'] );
   	foreach ( $val['products'] as $key=>$pval ):
	?>
	 <tr <?php if( ($index & 1) != 0 ) {?>  class="alert-warning" <?php }?>>
	<?php if($key == 0):?>
      <td rowspan="<?php echo $rows;?>"><?php echo $val['orderId'];?></td>
	  <td rowspan="<?php echo $rows;?>"><?php echo $val['settlementId'];?></td>
	  <td rowspan="<?php echo $rows;?>"><?php echo $val['settTime']?></td>
	  <td rowspan="<?php echo $rows;?>"><?php echo $val['realPayment']?>
		<?php if( $val['freight']>0 ){?>
		<br/>（运费<?php echo $val['freight']?>元）
		<?php }?>
	  </td>
	  <td rowspan="<?php echo $rows;?>"><?php echo $val['receipt'];?></td>
	  <td rowspan="<?php echo $rows;?>"><?php echo $val['isDone'];?></td>
	<?php endif;?>
	  <td><?php echo $pval['singleNumber']?><br/><?php echo $pval['color'];?></td>
	  <td><?php echo $pval['price']?></td>
	  <td><?php echo $pval['num'];?></td>
	  <td><?php echo $pval['subPrice'];?></td>
	  </tr>
	  <?php endforeach; ?>
 <?php endforeach; ?>
  </tbody>
   </table>
  <br>

<?php }?>

<?php if(!empty( $refund )){?>
	 <table class="table table-bordered alert-danger">
    <colgroup><col width="10%"/><col width="10%"/><col width="10%"/><col width="10%"/><col/><col width="10%"/><col width="10%"/><col width="10%"/><col width="10%"/></colgroup>
   <thead>
   <tr><td colspan="10"><span class="first">退货信息</span></td></tr>
    <tr>
     <td>退货单号</td>
	 <td>订单编号</td>
	 <td>退货总金额</td>
	 <td>申请退货时间</td>
	 <td>理由</td>
	  <td>产品</td>
      <td>单价</td>
      <td>数量</td>
	  <td>小计</td>
    </tr>
   </thead>
 <tbody>
  <?php  foreach( $refund as $index=>$val ):
   $rows = count( $val['products'] );
   	foreach ( $val['products'] as $key=>$pval ):
	?>
	 <tr <?php if( ($index & 1) != 0 ) {?>  class="alert-warning" <?php }?>>
	<?php if($key == 0):?>
      <td rowspan="<?php echo $rows;?>"><?php echo $val['refundId'];?></td>
	  <td rowspan="<?php echo $rows;?>"><?php echo $val['orderId'];?></td>
	  <td rowspan="<?php echo $rows;?>"><?php echo $val['realPayment']?></td>
	  <td rowspan="<?php echo $rows;?>"><?php echo $val['createTime']?></td>
	  <td rowspan="<?php echo $rows;?>"><?php echo $val['cause'];?></td>
	<?php endif;?>
	  <td><?php echo $pval['singleNumber']?><br/><?php echo $pval['color'];?></td>
	  <td><?php echo $pval['price']?></td>
	  <td><?php echo $pval['num'];?></td>
	  <td><?php echo $pval['subPrice'];?></td>
	  </tr>
	  <?php endforeach; ?>
 <?php endforeach; ?>
  </tbody>
   </table>
  <br>

<?php }?>




<?php }?>



<script>seajs.use('statics/app/finance/js/index.js');</script>
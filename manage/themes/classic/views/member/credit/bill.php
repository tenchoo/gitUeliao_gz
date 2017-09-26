  <div class="panel panel-default search-panel">
   <div class="panel-body">
    <form role="search" class="pull-left form-inline" action="<?php echo $this->createUrl( 'bill' );?>">
     <div class="form-group">
	 <select name="y" class="form-control input-sm cate1">
	<?php for($beginYear;$beginYear<=$endYear;$beginYear++){?>
		<option value="<?php echo $beginYear;?>" <?php if( $beginYear == $y ){ echo 'selected';}?> ><?php echo $beginYear;?>年</option>
	<?php }?>
     </select>
     <select name="m" class="form-control input-sm cate2">
		<?PHP for ( $i=1;$i<=12;$i++){ ?>
		<option value="<?php echo $i;?>" <?php if( $i == $m ){ echo 'selected';}?>><?php echo $i;?>月</option>
		<?php }?>
     </select>
     </div>
	 <input type="hidden" name="memberId" value="<?php echo $member['memberId'];?>"/>
     <button class="btn btn-sm btn-default">查看账单</button>
    </form>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<span>
	  <a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('bill',array('memberId'=>$member['memberId']))?>">未生成账单记录</a>	  
	</span>
   </div>
  </div>
  <div class="panel panel-default">
	<div class="panel-heading clearfix">
	 <span class="col-md-4">客户名称：<?php echo $member['companyName'];?>  (id:<?php echo $member['memberId'];?>)</span>
	 <span class="col-md-4">信用额度：<?php echo number_format( $member['credit'],2 );?> 元 &nbsp; &nbsp;<?php if( $member['state'] == '1' ){?>已取消月结<?php }?></span>
		<span class="col-md-4">还款周期：<?php echo $member['billingCycle'];?> 个月</span>
	</div>
	<ul class="list-group">
	 <?php if( is_null($bill) ){ ?>
	 <li class="list-group-item clearfix">
		<span class="col-md-4">
		<?php if( $y && $m ){  ?>
		无<?php echo $y.'年'.$m.'月'?>账单
		<?php }else{ ?>
		未生成账单记录（小计：<?php echo number_format( $count,2 );?> 元）
		<?php }?>
		</span>
	 </li>
	<?php }else{ ?>
	<li class="list-group-item clearfix">
		<span class="col-md-4">账单月份：<?php echo $y.'年'.$m.'月'?></span>
		<span class="col-md-4">账单金额：<?php echo $bill['credit']?></span>
		<span class="col-md-4">生成日期：<?php echo $bill['createTime']?></span>
	 </li>
	<?php }?>
	</ul>
</div>
   <br>
<?php if( !is_null($detail) ){ ?>
<table class="table table-condensed table-bordered">
   <thead>
    <tr>
     <td></td>
     <td>订单编号</td>
     <td>金额</td>
	 <td>说明</td>
    </tr>
   </thead>
   <tbody>
   <?php foreach(  $detail as $val  ):?>
    <tr>
     <td><?php echo $val['createTime'];?></td>
	 <td><?php echo empty($val['orderId'])?'-':$val['orderId'];?></td>
     <td><?php echo $val['amount'];?>
		<?php if(array_key_exists('isCheck',$val) && $val['isCheck'] == '0' ){ ?>
			(未审核)
		<?php }?>
	 </td>
	 <td><?php echo $val['mark'];?></td>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
<?php }?>
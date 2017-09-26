<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />

<div class="panel panel-default">
	<div class="panel-heading clearfix">
	 <span class="col-md-4">单号：<?php echo $applyPriceId;?></span>
	 <span class="col-md-4">客户名称：<?php echo $companyname;?></span>
	 <span class="col-md-4">业务员：<?php echo $saleman;?></span>
	</div>
</div>
<table class="table table-condensed table-bordered order">
	<thead>
    <tr>
	 <td>产品信息</td>
	 <td>单价</td>
     <td>批发价</td>
	</tr>
    </thead>
    <tbody class="list-page-body">
	 <tr class="list-bd">
   <td>
   <div class="c-img pull-left">
     <a href="javascript:"><img src="<?php echo $this->img().$mainPic;?>_50" alt="" width="50" height="50"/></a>
   </div>
	 <div class="product-title">【<?php echo $serialNumber;?>】<?php echo $title;?></div>
	 </td>
     <td> <?php echo Order::priceFormat( $price );?>元/<?php echo $unitName;?></td>
	<td class="form-inline"><?php echo Order::priceFormat( $applyPrice );?>元/<?php echo $unitName;?></td>
    </tr>
    </tbody>
</table>
<br>
<br>
<?php if( !empty( $oplog )) { ?>
<div class="panel panel-default">
  <div class="panel-heading clearfix">
	 <span class="col-md-4">操作日志</span>
	</div>
	<ul class="list-group">
	<?php foreach ( $oplog as $val ) { ?>
	  <li class="list-group-item clearfix">
	    <span class="col-md-12">
			<?php echo $val['createTime'];?> &nbsp;&nbsp;&nbsp;&nbsp;
			<?php echo $val['username'];?>&nbsp;&nbsp;&nbsp;&nbsp;
			<?php echo $val['codeTitle'];?>&nbsp;&nbsp;&nbsp;&nbsp;
			<?php echo $val['remark'];?>
		</span>
	  </li>
	  <?php }?>
	</ul>
</div>
<br>
<?php }?>
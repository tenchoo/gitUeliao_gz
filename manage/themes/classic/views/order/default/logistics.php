<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />

<?php if( !empty( $logistics ) ){ 
	foreach ( $logistics as $val ){
?>

 <div class="alert alert-danger alert-dismissible fade in" role="alert">
	收货信息:<?php echo $val['address'];?>
 </div>
 <div class="panel panel-default">
    <div class="panel-heading clearfix"><span class="col-md-4">物流编号：<?php echo $val['logisticsNo'];?> </span><span class="col-md-4">物流公司：<?php echo $val['com'];?></span><span class="col-md-4">运单号码：<?php echo $val['logisticsNo'];?></span></div>
	<div>物流信息:</div>
    <ul class="list-group">
		<?php if( isset($val['detail']) && is_array ($val['detail']) ){
			foreach( $val['detail'] as $dval ){ ?>
	    <li class="list-group-item clearfix">
			<?php echo $dval['time'];?> <?php echo $dval['context'];?>
		</li>
			<?php }}else{ ?>
		 <li class="list-group-item clearfix">
			暂无物流信息
		</li>
		<?php } ?>	   
    </ul>
  </div>
	
	<?php }}else{ ?>
 <div class="alert alert-danger alert-dismissible fade in" role="alert">
	暂无物流信息
 </div>
<?php } ?>
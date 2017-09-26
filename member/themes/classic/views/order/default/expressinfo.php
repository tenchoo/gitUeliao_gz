<link rel="stylesheet" href="/app/member/transaction/css/style.css"/>
 <div class="pull-right frame-content">
<div class="frame-tab">
    <ul class="clearfix list-unstyled frame-tab-hd">
      <li class="active">
        <a href="javascript:">查看物流</a>
      </li>
    </ul>
 </div>
 <?php if( !empty( $logistics ) ){
	foreach ( $logistics as $val ){
?>
      <div class="order-status">
        <div class="bd">
          <span class="item">物流编号：<?php echo $val['logisticsNo'];?></span>
		      <span class="item item-company">物流公司：<?php echo $val['com'];?></span>
		  <!-- <span class="item">运单号码：<?php echo $val['logisticsNo'];?></span> -->
        </div>
      </div>
      <div class="frame-box">
	  <div class="express-address">
       		<p><b>收货信息：</b><?php echo $val['address'];?></p>
        </div>
				<div class="express-info">
					<div class="hd pull-left">物流信息：</div>
					<ol class="bd list-unstyled pull-left express-items">
						<?php if( isset($val['detail']) && is_array ($val['detail']) ){
						foreach( $val['detail'] as $dval ){ ?>
					<li>
						<?php echo $dval['time'];?> <?php echo $dval['context'];?>
					</li>
						<?php }}else{ ?>
					 <li>
						暂无物流信息
					</li>
		<?php } ?>
					</ol>
				</div>

      </div>
       <?php } }else{ ?>
	   <div class="order-status">
        <div class="bd">
          暂无物流信息
        </div>
      </div>

	<?php }?>
</div>
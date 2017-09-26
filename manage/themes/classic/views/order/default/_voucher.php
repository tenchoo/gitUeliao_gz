<link rel="stylesheet" href="/themes/classic/statics/modules/uploader/css/style.css"/>
<?php if( $val['payState'] ==0 ){?>
                未付款 <?php echo $val['payModel']?'('.$payments[$val['payModel']]['paymentTitle'].')':''?><br />
<?php }?>
<br />
<?php

if (is_array ( $val ['paymemt'] )) {
	foreach ( $val ['paymemt'] as $paymemt ) {
		?>
<?php echo ($paymemt['amountType'])?'已付款':'已付定金'?>(<?php echo $payments[$paymemt['type']]['paymentTitle']?> )
<br />
<?php echo ( $paymemt['amount']>0) ?'金额：'.$paymemt['amount'].' 元<br/>':''?>
<?php if(empty( $paymemt['voucher'] )) { ?>
<span class="uploader uploader-button" data-paymemtId="<?php echo $paymemt['paymentId'];?>"><a href="javascript:" class="text-primary">上传凭证</a></span>
<?php }else{ ?>
<span><a href="<?php echo $this->img(false).$paymemt['voucher'];?>" class="text-primary" target="_blank">查看凭证</a>
</span>
<?php } ?>
<br />
<br />
<?php
	}
}
?>
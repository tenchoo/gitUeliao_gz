<link rel="stylesheet" href="/modules/uploader/css/style.css"/>
<?php echo $payModel?'('.$payments[$payModel]['paymentTitle'].')':'未付款'?><br />
<!--
<?php
	/* if (is_array ( $paymentlist ) && !empty( $paymentlist) ){
		$paymemt = current ( $paymentlist );		
	//	foreach ( $paymentlist as $paymemt ) { */
?>
<?php //echo ($paymemt['amountType'])?'已付款':'已付定金'?>(<?php //echo $payments[$paymemt['type']]['paymentTitle']?> )
	<br />
<?php //echo ( $paymemt['amount']>0) ?'金额：'.$paymemt['amount'].' 元<br/>':''?>
<?php //if(empty( $paymemt['voucher'] )) { ?>
	<span class="uploader uploader-button" data-paymemtId="<?php// echo $paymemt['paymentId'];?>"><a href="javascript:" class="text-link">上传凭证</a></span>
<?php //}else{ ?>
	<span><a href="<?php //$this->imageUrl($paymemt['voucher']);?>" class="text-link" target="_blank">查看凭证</a></span>
<?php// } ?>
<br /><br />
<?php //} //} ?>
-->
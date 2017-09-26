<?php
 $buttonInfo = array(
	'confirmbuy'=>array('title'=>'确定购买','target'=>'_blank','action'=>'check'),
	'check'=>array('title'=>'订单审核','target'=>'_blank','action'=>'check'),
	'received'=>array('title'=>'确认收货','target'=>'_blank','action'=>'received','class'=>'btn btn-warning btn-xs'),
	'comment'=>array('title'=>'客户反馈','target'=>'_blank','action'=>'/order/comment/add','paramName'=>'orderId'),
	'changedeposit'=>array('title'=>'修改订金','action'=>'changedeposit'),
	'settlement'=>array('title'=>'生成结算单','target'=>'_blank','action'=>'/order/settlement/add'),
	'change'=>array('title'=>'修改订单','target'=>'_blank','action'=>'/order/modity/change'),
	'express'=>array('title'=>'查看物流','target'=>'_blank','action'=>'expressinfo','paramName'=>'orderId'),
	'needpay'=>array('title'=>'立即付款','target'=>'_blank','action'=>'/cart/pay/index/','paramName'=>'orderids'),
	'delay'=>array('title'=>'申请延期','javascript'=>true,'class'=>'text-link pack'),
	'cancel'=>array('title'=>'取消订单','javascript'=>true,'class'=>'cancel-order text-link'),
	'alreadyClose'=>'订单已关闭',
	'alreadyRefund'=>'订单已退货',
	'view'=>array('title'=>'订单详情','action'=>'view'),
	'trace'=>array('title'=>'订单跟踪','action'=>'trace'),
	'refund'=>array('title'=>'申请退货','target'=>'_blank','action'=>'/order/refund/add'),
	'applyprice'=>array('title'=>'价格申请','target'=>'_blank','action'=>'applyprice'),

 );
 $buttons[] ='view';
 $buttons[] ='trace';

?>
<?php foreach ( $buttons as $_button):
	if( !array_key_exists ( $_button, $buttonInfo ) ) continue;

	if( !is_array( $buttonInfo[$_button] ) ){
		echo $buttonInfo[$_button].'<br/>';
		continue;
	}

	if( array_key_exists ( 'action', $buttonInfo[$_button] ) ){
		$paramName = array_key_exists ( 'paramName', $buttonInfo[$_button] )?$buttonInfo[$_button]['paramName']:'id';
		$url = $this->createUrl( $buttonInfo[$_button]['action'],array($paramName=>$orderId) );
	}else{
		$url = 'javascript:';
	}

	$class = array_key_exists ( 'class', $buttonInfo[$_button] )?$buttonInfo[$_button]['class']:'text-link';

	$attr = '';
	if( array_key_exists ( 'javascript', $buttonInfo[$_button] ) ){
		$attr .= ' data-orderid="'.$orderId.'"';
	}else{
		if( array_key_exists ( 'target', $buttonInfo[$_button] ) ){
			$attr .= ' target="'.$buttonInfo[$_button]['target'].'"';
		}
	}
	?>
	<a href="<?php echo $url;?>" <?php echo $attr?> class="<?php echo $class;?>"><?php echo $buttonInfo[$_button]['title'];?></a><br/>
<?php endforeach ;?>
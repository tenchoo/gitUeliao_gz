<?php
switch( $state ){
	case '0': ?><!--待审核-->
		<?php if( !array_key_exists ('keep' ,$val) ) { ?>
		<a href="<?php echo $this->createUrl('check',array('id'=>$val['orderId']));?>" >订单审核</a><br/>
		<?php } ?>
<?php	break;
	case '1':
	case '2':
			if( $val['isSettled'] == '0' ){
	?>
		<a href="<?php echo $this->createUrl('settlement/add',array('id'=>$val['orderId']));?>" >生成结算单</a><br/>
<?php		}
		break;
	case '4':?><!--确认收货-->
		<a href="<?php echo $this->createUrl('received',array('id'=>$val['orderId']));?>">确认收货</a><br/>
<?php	break;
	case '6':  ?><!--已完成-->
		<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $val['orderId'] ?>" data-rel="<?php echo $this->createUrl('del');?>">删除</a><br/>
		<a href="<?php echo $this->createUrl('logistics',array('orderId'=>$val['orderId']));?>">查看物流</a><br/>
<?php	break;
		case '7':  ?>
		<!--关闭-->
		<a href="#" class="del" data-toggle="modal" data-target=".del-confirm" data-id="<?php echo $val['orderId'] ?>" data-rel="<?php echo $this->createUrl('del');?>">删除</a><br/>
<?php	break;
		case '8':  ?>
		<!--财务确认已收款-->
		<a href="<?php echo $this->createUrl('confirmpayment',array('id'=>$val['orderId']));?>" >财务确认</a><br/>
<?php	break;}?>

<?php if( $state < 3 && $val['isSettled'] == '0' ){?>
<a href="javascript:" class="cancel-order" data-toggle="modal" data-target=".cancel-order-confirm" data-orderid="<?php echo $val['orderId']?>">取消订单</a><br/>
<?php } ?>
<a href="<?php echo $this->createUrl('view',array('id'=>$val['orderId']));?>">查看订单</a><br/>
<a href="<?php echo $this->createUrl('trace',array('id'=>$val['orderId']));?>">订单跟踪</a><br/>
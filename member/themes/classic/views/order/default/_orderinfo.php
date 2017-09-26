<div class="order-details-list">
    <div class="hd">
        <span class="item"><?php echo $orderType ?>：<?php echo $model->orderId;?></span>
        <span class="item">下单日期：<?php echo $model->createTime;?></span>
	<?php if ( array_key_exists ( 'salesman',$member)):?>
        <span class="pull-right">业务员：<?php echo $member['salesman'];?></span>
	<?php endif;?>
    </div>
    <ul class="bd list-unstyled">
        <li>
			<span class="item">客户名称：<?php echo $member['companyname'];?></span>
			<span class="item">联系人：<?php echo $model->name;?>（<?php echo $model->tel;?>）</span>
        </li>
        <li>
            <span class="item">提货方式： <?php echo $model->deliveryMethod;?></span>
            <span class="item">支付方式：
            <?php echo ($model->payModel)?$payments[$model->payModel]['paymentTitle']:'未付款';?>
            </span>
        </li>
        <li><span>收货地址：<?php echo $model->address;?> </span></li>
        <li class="memo"><span class="pull-left">备注：</span><div style="padding-left:36px"><?php echo $model->memo;?></div></li>
    </ul>
</div>
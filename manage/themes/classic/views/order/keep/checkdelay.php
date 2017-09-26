<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<?php $this->beginContent('//layouts/_success');$this->endContent();?>
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <span class="col-md-4">留货订单：<?php echo $order->orderId; ?></span>
            <span class="col-md-4">下单时间：<?php echo $order->createTime; ?></span>
            <span class="col-md-4">业务员：<?php echo $order->userName; ?></span>
        </div>
        <ul class="list-group">
            <li class="list-group-item clearfix">
                <span class="col-md-4">客户名称：<?php echo $userInfo->profiledetail->companyname;?></span>
                <span class="col-md-4">联系人：<?php echo $order->name; ?>（<?php echo $order->tel; ?>）</span>
                <span class="col-md-4">付款方式：<?php echo $order->orderPayMode->paymentTitle;?></span>
            </li>
            <li class="list-group-item clearfix">
                <span class="col-md-12">收货地址：<?php echo $order->address;?></span>
            </li>
            <li class="list-group-item clearfix">
                <span class="col-md-12">备注：<?php echo $order->memo; ?></span>
            </li>
        </ul>
    </div>

    <table class="table table-condensed table-bordered order">
        <thead>
        <tr class="list-hd">
            <td>产品编号</td>
            <td>颜色</td>
            <td>单价（元）</td>
            <td>购买数量</td>
            <td>小计（元）</td>
        </tr>
        </thead>

        <tbody>
        <?php
        $priceTotal = 0;
        foreach ($products as $product) {
            $price = $product->price * $product->num;
            $priceTotal += $price;
            ?>
            <tr>
                <td><?php echo $product->singleNumber;?></td>
                <td><?php echo $product->color;?></td>
                <td><?php echo Order::priceFormat($product->price);?></td>
                <td><?php echo Order::quantityFormat($product->num);?></td>
                <td><?php echo Order::priceFormat($price);?></td>
            </tr>
        <?php } ?>
        <tr>
            <td colspan="5" align="right">运费：<?php echo Order::priceFormat($order->freight);?> 总额：<?php echo Order::priceFormat($priceTotal);?></td>
        </tr>
        </tbody>
    </table>
<form method="post">
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <span class="col-md-4">延期审核</span>
        </div>
        <ul class="list-group">
            <li class="list-group-item clearfix">
				<span class="col-md-12">留货时间：<?php echo $order->createTime; ?> 至 <?php echo $expireTime; ?>
                <span class="text-danger">延期至<?php echo $delayTime; ?></span></span>
            </li>
            <li class="list-group-item clearfix">
                <span class="col-md-4">审核结果：
                    <label class="radio-inline"><input type="radio" name="state" value="1"/>同意延期</label>
                    <label class="radio-inline"><input type="radio" name="state" value="2"/>不同意延期</label>
                </span>
            </li>
            <li class="list-group-item clearfix"><span class="col-md-8">原因：<label class="radio-inline"><textarea name="reason" class="form-control"></textarea></label></span></li>
        </ul>
    </div>
    <div class="text-center">
      <input class="btn btn-success" type="submit" value="提交审核"/>
    </div>
</form>
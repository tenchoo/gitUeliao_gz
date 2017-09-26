<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<div class="panel panel-default">
  <div class="panel-heading clearfix">
    <span class="col-md-4">发货单号：<?php echo $orderProduct->postId;?> </span>
    <span class="col-md-4">采购单号：<?php echo $orderProduct->purchaseId;?> </span>
  </div>
</div>
<br>

<table class="table table-condensed table-bordered">
  <thead>
  <tr class="list-hd">
	  <td colspan="5"><span class="first">产品编号:<?php echo $detail->productCode;?></span><span>颜色：<?php echo $detail->color;?></span><span>数量：<?php echo Order::quantityFormat($orderProduct->postTotal).$unit;?></span></td>
    </tr>
    <tr class="list-hd">
	  <td>订单编号</td>
	  <td>采购数量</td>
      <td>状态</td>
	  <td>匹配操作人</td>
	  <td>匹配时间</td>
    </tr>
  </thead>
  <tbody>
  <?php foreach($products as $product){?>
  <tr>
  	<td><?php echo $product['orderId'];?></td>
	<td><?php echo Order::quantityFormat($product['quantity']).$unit;?></td>
    <td class="assign-state">已匹配</td>
	<td><?php echo tbUser::model()->getUsername($product['userId']);?></td>
	<td><?php echo date( 'Y-m-d H:i:s',$product['createTime']);?></td>
  </tr>
  <?php }?>
  </tbody>
  </table>
<?php $this->beginContent('//layouts/_error');$this->endContent();?>

<div class="panel panel-default">
  <div class="panel-heading clearfix">
    <span class="col-md-4">发货单号：<?php echo $orderProduct->postId;?> </span>
    <span class="col-md-4">采购单号：<?php echo $orderProduct->purchaseId;?> </span>
  </div>
</div>
<br>
<form method="post">
<table class="table table-condensed table-bordered">
  <thead>
  <tr class="list-hd">
	  <td colspan="4"><span class="first">产品编号:<?php echo $detail->productCode;?></span><span>颜色：<?php echo $detail->color;?></span><span>数量：<?php echo Order::quantityFormat($orderProduct->postTotal).$unit;?></span></td>
    </tr>
    <tr class="list-hd">
	  <td>订单编号</td>
	  <td>采购数量</td>
      <td>状态</td>
      <td>操作</td>
    </tr>
  </thead>
  <tbody>
  <?php foreach($products as $product){
    $assignInfo = sprintf("%s:%s", $product->purchaseId,$product->quantity);
    ?>
  <tr>
  	<td><?php echo $product->orderId;?><?php echo CHtml::hiddenField('assign[]',$assignInfo,array('disabled'=>true,'class'=>'assign-info'));?></td>
	<td><?php echo Order::quantityFormat($product->quantity).$unit;?></td>
    <td class="assign-state">未匹配<?php //echo tbOrderPost2Assign::assign($product->orderProId,'word');?></td>
    <td><a href="javascript:" class="assign">匹配</a></td>
  </tr>
  <?php }?>
  </tbody>
  </table>
  <br />
  <div class="text-center">
  <input class="btn btn-success" type="submit" value="保存匹配" />
  </div>
  </form>
  <script>seajs.use('statics/app/purchase/post/js/assign.js');</script>
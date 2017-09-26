<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<div class="panel panel-default search-panel">
<div class="panel-body">
	<form action="<?php echo $this->createUrl('list')?>" class="pull-left form-inline">
		<?php echo CHtml::dropDownList('state',$state,$stateTitles,array('class'=>'form-control input-sm','empty'=>'所有状态'));?>
		<input value="<?php echo $purchaseId;?>" name="purchaseId" class="form-control input-sm" placeholder="采购单号" type="text">
		<input value="<?php echo $factory;?>" name="factory" class="form-control input-sm" placeholder="厂家名称" type="text">
		<input value="<?php echo $purchaseDate;?>" name="purchaseDate" class="form-control input-sm  input-date" readonly=""  placeholder="采购日期" type="text">
		<button class="btn btn-sm btn-default">查找</button>
	</form>
</div>
</div>
<div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>

<table class="table table-condensed table-bordered">
  <colgroup><col width="20%"><col width="20%"><col width="20%"><col width="30%"><col width="10%"></colgroup>
  <thead>
  <tr>
  <td>产品编号</td>
	<td>颜色</td>
	<td>采购数量</td>
	<td>备注</td>
	<td>操作</td>
	</tr>
  </thead>
</table>
<br>

<?php foreach( $dataList as $item ): ?>
<table class="table table-condensed table-bordered">
  <colgroup><col width="20%"><col width="20%"><col width="20%"><col width="30%"><col width="10%"></colgroup>
  <tbody>
	<tr class="list-hd">
		<td colspan="5">
		<span class="first">采购单号：<?php echo $item->purchaseId; ?></span>
		<span>下单时间：<?php echo date('Y/m/d',$item->createTime);?></span>
		<span>工厂名称：<?php echo $item->supplierName;?></span>
		<span>采购人：<?php echo tbUser::model()->getUsername($item->userId);?></span>
		</td>
	</tr>
  <?php
  $detail = $item->getProducts();
  $rows   = count($detail);
  foreach($detail as $index=>$product):?>
    <tr class="list-bd">
      <td><?php echo $product->productCode;?></td>
	  <td><?php echo $product->color;?></td>
	  <td><?php echo Order::quantityFormat($product->quantity).ZOrderHelper::getUnitName($product->productCode);?></td>
	  <td><?php echo $product->comment;?></td>
	  <?php if($index == 0):?>
<td rowspan="<?php echo $rows;?>">
<?php	echo array_key_exists( $item->state,$stateTitles)?$stateTitles[$item->state]:'';
		echo '<br>';
		if( in_array( $item->state,array(tbOrderPurchasing::STATE_NORMAL) )  ) {
			$url = $this->createUrl('post', array('id'=>$item->purchaseId));
			echo CHtml::link("发货",$url);
			echo '<br>';
			$url = $this->createUrl('finish', array('id'=>$item->purchaseId));
            echo CHtml::link("发货完成",$url,array('class'=>'f'));
			echo '<br>';
			$url = $this->createUrl('cancle', array('id'=>$item->purchaseId));
            echo CHtml::link("取消采购单",$url);
			echo '<br>';
		}
		$url = $this->createUrl('view', array('id'=>$item->purchaseId));
        echo CHtml::link("查看采购单",$url);
?>
      </td>
      <?php endif;?>
    </tr>
   <?php endforeach;?>
  </tbody>
</table>
<br>
   <?php endforeach; ?>

<script>
  seajs.use('statics/app/factory/js/list.js');
</script>

<div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
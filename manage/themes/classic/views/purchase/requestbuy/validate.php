<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<?php $this->beginContent('//layouts/_success');$this->endContent();?>
<div class="panel panel-default search-panel">
	<div class="panel-body">
		<form method="get" class="pull-left form-inline" action="<?php echo $this->createUrl('validate');?>">
			<input type="text" placeholder="请输入产品编号" name="s" value="<?php echo Yii::app()->request->getQuery('s');?>" class="form-control input-sm" />
			<input type="text" placeholder="请输入订单编号" name="o" value="<?php echo Yii::app()->request->getQuery('o');?>" class="form-control input-sm" />
			<input type="submit" value="查找" class="btn btn-sm btn-default" />
		</form>
	</div>
</div>
<div class="clearfix well well-sm list-well">
    <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
  </div>
<table class="table table-condensed table-bordered">
   <thead>
    <tr>
     <td>产品编号</td>
     <td>颜色</td>
     <td>预订数量</td>
     <td width="170"></td>
    </tr>
   </thead>
   <tbody>
<?php foreach ( $dataList as $item ){?>

<tr class="list-hd">
	<td colspan="4">
		<input type="checkbox" value="<?php echo $item->orderId; ?>"/>
		<span class="first">请购单号：<?php echo $item->orderId; ?></span>
		<span>请购时间：<?php echo date('Y-m-d',$item->createTime); ?></span>
	</td>
</tr>

<?php
$begin = true;
foreach( $item->products as $product ){?>
    <tr>
	     <td><?php echo $product->singleNumber;?></td>
	     <td><?php echo $product->color;?></td>
	     <td><?php echo Order::quantityFormat($product->total); echo $product->unitName;?></td>
	     <?php
	     if($begin){
	     	$begin = false;
	     	?>
	     <td rowspan="<?php echo count($item->products);?>"  width="170">
         <a href="javascript:" class="addbuylist" data-url="<?php echo $this->createUrl('validate',array('todo'=>'success'))?>" data-id="<?php echo $item->orderId;?>">添加采购</a><br>
		     <a href="<?php echo $this->createUrl('close',array('id'=>$item->orderId))?>">关闭采购</a>
	     </td>
	     <?php }?>
    </tr>
<?php }?>
<?php }?>
</tbody>
</table>
<br>
<div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
<script>seajs.use('statics/app/purchase/requestbuy/js/validate.js');</script>
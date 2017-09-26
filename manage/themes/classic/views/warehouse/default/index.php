<div class="panel panel-default search-panel">
  <div class="panel-body">
  <div class="pull-right form-inline"><a href="<?php echo $this->createUrl('addnew')?>" class="btn btn-default btn-sm">新建入库单</a></div>
  <form action="<?php echo $this->createUrl('index');?>" class="pull-left form-inline">
	<input type="text" value="<?php echo $f;?>" name="f" class="form-control input-sm" placeholder="工厂编号"/>
	<input type="text" value="<?php echo $id;?>" name="id" class="form-control input-sm" placeholder="采购单号"/>
	<input type="text" value="<?php echo $p;?>" name="p" class="form-control input-sm" placeholder="产品编号"/>
	<button class="btn btn-sm btn-default">查找</button>
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
	  <td width="15%">产品颜色</td>
	  <td width="15%">发货数量</td>
	  <td width="15%">操作</td>
    </tr>
  </thead>
</table>
<br/>
 
   <?php foreach( $orders as $item ):?>
  <table class="table table-condensed table-bordered">
  <thead>
    <tr class="list-hd">
      <td colspan="4">
		<span class="first">发货单号：<?php echo $item->postId;?></span>
		<span>发货日期：<?php echo $item->postTime;?></span>
	  </td>
    </tr>
  </thead>
	<tbody>
	<?php
	$products = $item->getProducts();
	$rowspan    = count( $products );
	$setRowspan = false;
	foreach( $products as $pval ):
		$detail = $pval->details;
	?>
	<tr>
      <td><?php echo $detail->productCode;?></td>
	  <td width="15%"><?php echo $detail->color;?></td>
	  <td width="15%"><?php printf("%.1f %s", $detail->quantity, ZOrderHelper::getUnitName($detail->productCode));?></td>
	  <?php if(!$setRowspan){?>
	  <td width="15%" rowspan="<?php echo $rowspan;?>"><a href="<?php echo $this->createUrl('import',array('id'=>$item->postId))?>">产品入库</a></td>
	  <?php
		$setRowspan = true;
		}?>
    </tr>
	<?php endforeach; ?>
	</tbody>
  </table><br/>
<?php  endforeach; ?>
<div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
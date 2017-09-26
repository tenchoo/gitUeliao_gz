<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<div class="panel panel-default search-panel">
  <div class="panel-body">
  <form action="<?php echo $this->createUrl('list');?>" class="pull-left form-inline">
	<input type="text" value="<?php echo $f;?>" name="factoryNumber" class="form-control input-sm" placeholder="工厂编号"/>
	<input type="text" value="<?php echo $id;?>" name="id" class="form-control input-sm" placeholder="入库单号"/>
	<input type="text" value="<?php echo $p;?>" name="singleNumber" class="form-control input-sm" placeholder="产品编号"/>
	<button class="btn btn-sm btn-default">查找</button>
	</form>
  </div>
</div>
<div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
<table class="table table-condensed table-bordered">
  <colgroup><col><col width="15%"><col width="15%"><col width="15%"></colgroup>
  <thead>
    <tr>
      <td>产品编号</td>
	  <td>产品颜色</td>
	  <td>入库数量</td>
	  <td>&nbsp;</td>
    </tr>
  </thead>
</table>
<br>
   <?php foreach( $list as $item ):
	   $findRows=count($item->detail);?>
<table class="table table-condensed table-bordered">
  <colgroup><col><col width="15%"><col width="15%"><col width="15%"></colgroup>
	<tbody>
    <tr class="list-hd">
      <td colspan="5">
	    <span class="first">退货单号：<?php echo $item->postId;?></span>
		<span>入库单号：<?php echo $item->warrantId;?></span>
		<span>入库时间：<?php echo $item->createTime;?></span>
	  </td>
    </tr>
	<?php foreach( $item->detail as $index=>$detail ):
		   $unitName = tbProductStock::model()->unitName($detail->singleNumber);
		   ?>
	<tr>
      <td><?php echo $detail['singleNumber']?></td>
	  <td width="15%"><?php echo $detail['color']?></td>
	  <td width="15%"><?php printf("%s %s", Order::quantityFormat($detail->num), $unitName);?></td>
		<?php if(!$index){?>
	  <td width="15%" rowspan="<?php echo $findRows;?>"><a href="<?php echo $this->createUrl('view',array('id'=>$item->warrantId))?>">查看</a></td>
		<?php } ?>
    </tr>
	<?php endforeach; ?>
	</tbody>
</table>
<br>
<?php endforeach; ?>
<div class="clearfix well well-sm list-well">
  <?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
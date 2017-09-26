<div class="panel panel-default search-panel">
  <div class="panel-body">
  <form action="<?php echo $this->createUrl('index');?>" class="pull-left form-inline">
 <?php echo CHtml::dropDownList('warehouseId',$warehouseId,$warehouse,array('class'=>'form-control input-sm','empty'=>'所有仓库'));?>
	<input type="text" value="<?php echo $singleNumber;?>" name="singleNumber" class="form-control input-sm" placeholder="产品编号"/>
	<input type="text" value="<?php echo $productBatch;?>" name="productBatch" class="form-control input-sm" placeholder="产品批次"/>
	<button class="btn btn-sm btn-default">查找</button>
	</form>
  </div>
</div>
<div class="clearfix well well-sm list-well">
<?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
<table class="table table-condensed table-bordered">
  <colgroup><col width="20%"><col width="20%"><col width="20%"><col width="20%"><col width="20%"></colgroup>
  <thead>
    <tr>
    <td>仓库</td>
	<td>产品编号</td>
	  <td>仓位</td>
	  <td>批次</td>
    <td>数量</td>
    </tr>
  </thead>
</table>
<br>
<table class="table table-condensed table-bordered table-hover">
  <colgroup><col width="20%"><col width="20%"><col width="20%"><col width="20%"><col width="20%"></colgroup>
  <tbody>
    <?php foreach( $list as $item ): ?>
    <tr>
	  <td><?php echo $warehouse[$item->warehouseId];?></td>
	  <td><?php echo $item->singleNumber;?></td>
	  <td><?php echo $item->positionName;?></td>
	  <td><?php echo $item->productBatch;?></td>
	  <td><?php echo Order::quantityFormat( $item->num );?><string> <?php echo ZOrderHelper::getUnitName($item->singleNumber);?></string></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<br>
<div class="clearfix well well-sm list-well">
<?php if( $totalNum >0) { ?>总数量：<?php echo number_format($totalNum,1) ;}?>
<?php $this->beginContent('//layouts/_page',array('pages'=>$pages));$this->endContent();?>
</div>
<div class="panel panel-default search-panel">
  <div class="panel-body">
  <form class="pull-left form-inline">
	<?php echo CHtml::dropDownList('warehouseId',$warehouseId,$warehouse,array('class'=>'form-control input-sm','empty'=>'请选择仓库'))?>
	<input type="hidden" name="op" value="exportExcel"/>
	<button class="btn btn-sm btn-default">导出库存数据</button>
	</form>
   <div class="pull-right">
    <a class="btn btn-sm btn-default" href="<?php echo $this->createUrl('importexcel');?>">上传盘点数据</a>
	</div>
  </div>
</div>
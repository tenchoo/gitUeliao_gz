  <form class="form-horizontal" method="post">
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>盘点仓库：</label>
    <div class="col-md-4">
		<?php echo CHtml::dropDownList('warehouseId',$warehouseId,$warehouse,array('class'=>'form-control input-sm','empty'=>'请选择仓库'))?>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>产品编号：</label>
    <div class="col-md-4">
	  <input type="text" class="form-control input-sm" name="serialNumber"  data-suggestion="serialNumber" data-search="serial=%s" data-api="/api/search_product" autocomplete="off" placeholder="如：K365" value="<?php echo $serialNumber;?>"/>
    </div>
  </div>
   <?php $this->beginContent('//layouts/_error');$this->endContent();?>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-10">
      <button class="btn btn-success" id="btn-add" >下一步</button>
    </div>
  </div>
</form>
<script>
seajs.use('statics/app/warehouse/js/stocktaking.js');
</script>
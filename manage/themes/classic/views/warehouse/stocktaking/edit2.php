<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<link rel="stylesheet" href="/themes/classic/statics/app/warehouse/css/style.css" />

  <form class="form-horizontal" method="post" enctype="multipart/form-data">
    <input type="hidden" name="step" value="2"/>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>盘点仓库：</label>
    <div class="col-md-4">
		<?php echo CHtml::dropDownList('warehouseId',$warehouseId,$warehouse,array('class'=>'form-control input-sm','disabled'=>'disabled'))?>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>产品编号：</label>
    <div class="col-md-4">
	  <input type="text" class="form-control input-sm" name="serialNumber"  data-suggestion="serialNumber" data-search="serial=%s" data-api="/api/search_product" autocomplete="off" placeholder="如：K365" value="<?php echo $serialNumber;?>" disabled />
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>盘点人：</label>
    <div class="col-md-4">
	  <input type="text" class="form-control input-sm" name="takinger" maxlength="15"/>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>盘点数据：</label>
    <div class="col-md-4">
	<input type="file" name="cfile"  accept=".xls"/>
    </div>
  </div>
  <?php $this->beginContent('//layouts/_error');$this->endContent();?>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-10">
      <button class="btn btn-success" id="btn-add">上传盘点数据</button>
    </div>
  </div>
</form>
<br>
<p class="text-muted">说明：盘点数据除请用英文输入法(半角)输入数据内容，
	<a href="<?php echo $this->createUrl('add',array('type'=>'temp'));?>">点击下载数据模板</a>。</p>
<script>
seajs.use('statics/app/warehouse/js/stocktaking.js');
</script>
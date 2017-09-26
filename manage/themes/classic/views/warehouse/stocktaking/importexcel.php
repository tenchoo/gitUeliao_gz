<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<link rel="stylesheet" href="/themes/classic/statics/app/warehouse/css/style.css" />

  <form class="form-horizontal" method="post" enctype="multipart/form-data">
    <input type="hidden" name="step" value="2"/>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>盘点仓库：</label>
    <div class="col-md-4">
		<?php echo CHtml::dropDownList('warehouseId',$warehouseId,$warehouse,array('class'=>'form-control input-sm'))?>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>盘点人：</label>
    <div class="col-md-4">
	  <input type="text" class="form-control input-sm" name="takinger" maxlength="15" value="<?php echo $takinger?>"/>
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
      <button class="btn btn-success" id="btn-add">上传盘点数据</button><br>
	 <p class="text-muted">说明：盘点数据除请用英文输入法(半角)输入数据内容，从excel第四行开始保存数据，请不要删除模板表头。</p> 
    </div>
  </div>
</form>
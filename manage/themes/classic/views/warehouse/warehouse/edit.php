
<form class="form-horizontal modifydetail" method="post">
<div class="form-group">
  <label class="control-label col-md-2" for="">仓库名称：</label>
  <div class="col-md-4">
    <input type="text" class="form-control input-sm" name="title" value="<?php echo $title;?>">
  </div>
</div>
 <div class="form-group">
    <label class="control-label col-md-2" for="address">仓库类型：</label>
    <div class="col-md-4">
      <div class="inline-block area-select">
		<?php echo CHtml::dropDownList('type',$type,$types,array('class'=>'form-control input-sm'))?>
      </div>
    </div>
  </div>
 <div class="form-group">
    <label class="control-label col-md-2" for="address">所在地区：</label>
    <div class="col-md-4">
      <div class="inline-block area-select">
	      <select name="" class="form-control input-sm province">
	        <option value="default">请选择省份</option>
	      </select> <select name="" class="form-control input-sm city">
	        <option value="default">请选择市</option>
	      </select>
	      <input type="hidden" name="areaId" class="form-control input-sm" value="<?php echo $areaId?>" />
      </div>
    </div>
  </div>
 <br>
 <?php $this->beginContent('//layouts/_error');$this->endContent();?>
<div class="form-group">
  <div class="col-md-offset-2 col-md-10">
    <button class="btn btn-success">保存</button>
  </div>
</div>
</form>
<script>
  seajs.use('statics/app/warehouse/js/warehouse2.js');
</script>
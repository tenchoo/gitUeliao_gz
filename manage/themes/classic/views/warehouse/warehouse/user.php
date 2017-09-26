<form method="post" class="form-horizontal">
<div class="form-group">
  <label class="control-label col-md-2" for="">仓库名称：</label>
  <div class="col-md-4 "><p class="form-control-static"><?php echo $title;?></p></div>
    <input  name="warehouse[positionId]" value="<?php echo $positionId; ?>" type="hidden" >
    <input  name="warehouse[warehouseId]" value="<?php echo $warehouseId; ?>" type="hidden" >
</div>
<div class="form-group">
  <label class="control-label col-md-2" for="">分拣员：</label>
  <div class="col-md-4">
    <?php echo CHtml::dropDownList('warehouse[userId]',$userId,$user,array('class'=>'form-control input-sm','empty'=>'请选择分拣员'))?>
  </div>
</div>

<div class="form-group">
  <label class="control-label col-md-2" for="">是否设置为负责人:</label>
  <div class="col-md-4"> <label class="radio-inline">
        <input class="radio" name="warehouse[isManage]" value="0" type="radio" <?php if($isManage == 0 ) echo checked;?> ><span>否</span>
        </label>
         <label class="radio-inline">
        <input class="radio" name="warehouse[isManage]" value="1" type="radio" <?php if($isManage == 1 ) echo checked;?> >是
       </label>
         </div>
</div>

<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<br>
<div class="form-group">
  <div class="col-md-offset-2 col-md-10">
    <button class="btn btn-success">保存</button>
  </div>
</div>
</form>
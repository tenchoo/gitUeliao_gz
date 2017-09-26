<form class="form-horizontal modifydetail" method="post">
<div class="form-group">
  <label class="control-label col-md-2" for="">仓库名称：</label>
  <div class="col-md-4 "><p class="form-control-static"><?php echo $title;?></p></div>
</div>
 <div class="form-group">
    <label class="control-label col-md-2" for="address">默认打印机：</label>
    <div class="col-md-4">
      <div class="inline-block area-select">
		<?php echo CHtml::dropDownList('printerId',$printerId,$printers,array('class'=>'form-control input-sm'))?>
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
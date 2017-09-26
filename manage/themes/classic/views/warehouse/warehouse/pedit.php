<form method="post" class="form-horizontal">
<div class="form-group">
  <label class="control-label col-md-2" for=""><?php echo $name?>名称：</label>
  <div class="col-md-4">
    <input type="text" class="form-control input-sm" name="title" value="<?php echo $title;?>">
  </div>
</div>
<?php if( $parentId =='0' ) { ?>
 <div class="form-group">
    <label class="control-label col-md-2" for="address">类型：</label>
    <div class="col-md-4">
      <div class="inline-block area-select">
		<?php echo CHtml::dropDownList('type',$type,$types,array('class'=>'form-control input-sm'))?>
      </div>
    </div>
  </div>
 <div class="form-group">
    <label class="control-label col-md-2" for="address">默认打印机：</label>
    <div class="col-md-4">
      <div class="inline-block area-select">
		<?php echo CHtml::dropDownList('printerId',$printerId,$printers,array('class'=>'form-control input-sm'))?>
      </div>
    </div>
  </div>
 <?php }?>
<?php $this->beginContent('//layouts/_error2');$this->endContent();?>
<br>
<div class="form-group">
  <div class="col-md-offset-2 col-md-10">
    <button class="btn btn-success">保存</button>
  </div>
</div>
</form>
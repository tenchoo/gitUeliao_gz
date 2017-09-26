<form class="form-horizontal"  method="post">
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>送货区域：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="title" value="<?php echo $title;?>" maxlength="15">
    </div>
  </div> 
  <?php $this->beginContent('//layouts/_error');$this->endContent();?>
  <br/>
  <div class="col-md-offset-2 col-md-10">
    <button class="btn btn-success" type="submit">保存</button>
    <a href="<?php echo $this->createUrl( 'area', array('c'=>$this->cc ));?>" class="btn btn-default">取消</a>
  </div>
</form>
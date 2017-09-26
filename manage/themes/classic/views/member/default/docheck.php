<link rel="stylesheet" href="/themes/classic/statics/app/member/css/style.css"/>
<?php $this->beginContent('_tabs');$this->endContent();?>
<br>
<form class="form-horizontal" method="post" action="">

<?php if( $data['state'] == 1 ){ ?>
<div class="form-group">
    <label class="control-label col-md-2">审核：</label>
    <div class="col-md-4">
      <label class="radio-inline"><input type="radio" name="state" value="1" <?php echo ($data['state']!= '2' )?'checked':'';?> disabled/>审核通过</label>
      <label class="radio-inline"><input type="radio" name="state" value="2" <?php echo ($data['state']== '2' )?'checked':'';?> disabled />审核不通过</label>
    </div>
  </div>
   <div class="form-group">
    <label class="control-label col-md-2">理由：</label>
    <div class="col-md-4">
		<textarea name="reason" disabled class="form-control"><?php echo $data['reason'];?></textarea>
    </div>
  </div>
 <?php }else{ ?>
  <div class="form-group">
    <label class="control-label col-md-2">审核：</label>
    <div class="col-md-4">
      <label class="radio-inline"><input type="radio" name="state" value="1" <?php echo ($data['state']!= '2' )?'checked':'';?>/>审核通过</label>
      <label class="radio-inline"><input type="radio" name="state" value="2" <?php echo ($data['state']== '2' )?'checked':'';?>/>审核不通过</label>
    </div>
  </div>
   <div class="form-group">
    <label class="control-label col-md-2">理由：</label>
    <div class="col-md-4">
		<textarea name="reason" class="form-control"><?php echo $data['reason'];?></textarea>
    </div>
  </div>
  <?php $this->beginContent('//layouts/_error');$this->endContent();?>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-10">
      <button class="btn btn-success">保存</button>
    </div>
  </div>
  <?php } ?>
</form>
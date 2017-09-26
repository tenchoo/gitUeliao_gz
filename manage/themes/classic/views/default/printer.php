<link rel="stylesheet" href="/themes/classic/statics/app/role/css/style.css">
<h2 class="h3">绑定打印机</h2>
<?php $this->beginContent('//layouts/_success');$this->endContent();?>
<form method="post" class="form-horizontal">
<div class="form-group">
    <label class="control-label col-md-2">绑定打印机：</label>
    <div class="col-md-4">
        <?php echo CHtml::dropDownList('printerId', $profile->printerId, $printers); ?>
    </div>
  </div>
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<div class="form-group">
  <div class="col-md-offset-2 col-md-10">
    <button class="btn btn-success">保存</button>
  </div>
</div>
</form>

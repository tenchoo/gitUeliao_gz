<link rel="stylesheet" href="/themes/classic/statics/app/order/css/style.css" />
<form class="form-horizontal order-setting " method="post" action="">
<?php foreach ($set as $key=>$val ){?>
  <div class="form-group">
    <label class="control-label col-md-2"><?php echo $val['title']?>：</label>
    <div class="col-md-4">
		<input type="text" class="form-control input-sm int-only" name="set[<?php echo $key?>][setValue]" value="<?php echo isset($val['setValue'])?$val['setValue']:'';?>" />
      <?php echo CHtml::dropDownList('set['.$key.'][unit]',isset($val['setValue'])?$val['setValue']:'',array('day'=>'天','hour'=>'小时','min'=>'分钟'),array('class'=>'form-control input-sm unit'))?>
    </div>
  </div>
  <?php }?>
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-10">
      <button class="btn btn-success">保存</button>
    </div>
  </div>
</form>
<script>seajs.use('statics/app/order/js/ordercheck.js');</script>
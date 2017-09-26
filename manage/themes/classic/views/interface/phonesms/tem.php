<div class="alert alert-info" role="alert">变量说明：{code} 为第1个变量，{code2}为第2个变量，{code3}为第3个变量，每条短信都必须包含第1个变量。</div>
<form class="form-horizontal" method="post" action="">
<?php foreach ($model as $val ){ ?>
  <div class="form-group">
    <label class="control-label col-md-2">
		<span class="text-danger">*</span>
		<?php echo $val->comment;?> :</label>
    <div class="col-md-4">
		<textarea class="form-control" v-model="<?php echo $val->key?>" name="data[<?php echo $val->key?>]"><?php echo $val->value; ?></textarea>
    <span class="text-info">当前输入<span v-text="<?php echo $val->key?>.length"></span>字</span>
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
 <script>seajs.use('statics/app/interface/js/smstemplate.js'); </script>

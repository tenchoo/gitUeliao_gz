 <form class="form-horizontal" method="post" enctype="multipart/form-data">
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*数据来源</span>：</label>
    <div class="col-md-4">
      <label class="radio-inline"><input type="radio" name="type" value="default" checked />默认(面单)</label>
      <label class="radio-inline"><input type="radio" name="type" value="goujiazi"/>果佳滋</label>
	  <label class="radio-inline"><input type="radio" name="type" value="youzan"/>有赞</label>
	  <label class="radio-inline"><input type="radio" name="type" value="weidian"/>微店</label>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>数据文件：</label>
    <div class="col-md-4">
	<input type="file" name="cfile"  accept=".xls,.xlsx"/>
    </div>
  </div>
  <?php $this->beginContent('//layouts/_error');$this->endContent();?>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-10">
      <button class="btn btn-success" id="btn-add" type="submit">上传数据</button>
    </div>
  </div>
</form>
<br>
<p class="text-muted">说明：数据除请用英文输入法(半角)输入数据内容，从第二行开始保存数据，
	<a href="<?php echo $this->createUrl('import',array('type'=>'temp'));?>">点击下载默认数据模板</a>。</p>
<p class="text-muted">上传过程可能因网络或数量量大的问题而上传过程较慢，点击上传后请稍候，并请不要重复上传数据。</p>

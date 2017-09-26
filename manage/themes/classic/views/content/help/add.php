<link rel="stylesheet" href="/themes/classic/statics/app/product/create/css/style.css">

<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<?php $this->beginContent('//layouts/_success');$this->endContent();?>
<form action="" class="form-horizontal" method="post">
<div class="form-group">
      <label class="control-label col-md-2">选择分类：</label>
      <div class="inline-block warehousechoose col-md-4">
		<select  class="pull-left form-control input-sm cate1" style="width:40%">
      <option value="">请选择分类</option>
		<?php foreach ( $category as $c ){ ?>
		<option value="<?php echo $c['categoryId'];?>" data-child="<?php echo $c['childrens'];?>"
		<?php if( $c['categoryId'] == $cid1){ echo 'SELECTED';}?> ><?php echo $c['title'];?></option>
		<?php }?>
		</select>
		<?php if( isset($cate2) && is_array($cate2)){ ?>
			<select  style="width:40%;margin-left:20px" class="pull-left form-control input-sm cate2">
			<option value="">请选择类目</option>
			<?php foreach ( $cate2 as $c ){ ?>
			<option value="<?php echo $c['categoryId'];?>"
			<?php if( $c['categoryId'] == $cid2){ echo 'SELECTED';}?> ><?php echo $c['title'];?></option>
			<?php }?>
			</select>
		<?php }?>
        <input type="hidden" name="data[categoryId]" value="<?php echo $categoryId;?>" />
      </div>
    </div>
<div class="form-group">
      <label class="control-label col-md-2">标题：</label>
      <div class="inline-block warehousechoose col-md-4">
        <input type="text" name="data[title]" class="form-control input-sm" value="<?php echo $title;?>" />
      </div>
    </div>
<div class="form-group details">
  <label class="control-label col-md-2" for="">信息内容：</label>
  <div class="col-md-4">
    <textarea name="data[content]" class="content"><?php echo $content ?></textarea>
  </div>
</div>
<div class="form-group">
  <div class="col-md-offset-2 col-md-10">
    <button class="btn btn-success">保存</button>
  </div>
</div>
</form>
<script>
  seajs.use('statics/app/help/js/create.js');
</script>
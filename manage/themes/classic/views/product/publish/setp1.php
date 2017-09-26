<link rel="stylesheet" href="/themes/classic/statics/app/product/create/css/style.css">

<?php $this->beginContent('_tab',array('active'=>'edit','productId'=>'' ));$this->endContent();?>

<h3 class="h4">选择产品类目</h3>
<form action="" class="base-category form-inline" method="post">
  <select size="6" class="form-control cate1">
  </select>
  <select size="6" class="form-control cate2">
    <option value="default">请先选择一级类目</option>
  </select>
  <select size="6" class="form-control cate3">
    <option value="default">请先选择二级类目</option>
  </select>
  <br>
  <br>
  <div class="alert alert-warning">
	您当前选择的类目：<strong class="text-warning"><?php echo implode(' &gt; ',$categorys);?></strong>
  </div>
  <br>
  <div>
    <input type="hidden" name="categoryId" class="form-control input-sm" value="<?php echo $categoryId;?>" />
    <button class="btn btn-success" type="submit">下一步，填写产品信息</button>
  </div>
</form>
<script>
  seajs.use('statics/app/product/create/js/cate.js')
</script>
<?php $this->beginContent('_tab',array('active'=>'procurement','productId'=>$data['productId']));$this->endContent();?>
<div class="clearfix alert alert-warning">
  产品编号：<strong class="text-warning"><?php echo $serialNumber;?></strong>
</div>

<form action="" class="form-horizontal" method="post">
  <div class="form-group">
    <label class="control-label col-md-2">采购价：</label>
    <div class="clearfix col-md-4">
      <div class="input-group">
        <input type="text" class="pull-left form-control input-sm price-only" name="product[price]" value="<?php echo Order::priceFormat($data['price']);?>">
        <div class="input-group-addon">元</div>
      </div>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>生产厂家：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm" name="product[supplier]" value="<?php echo $data['supplier'];?>" data-suggestion="searchCompany" data-search="keyword=%s" data-api="/product/publish/procurement/type/ajaxsupplier" autocomplete="off" >
	  <input type="hidden" name="product[supplierId]" value="<?php echo $data['supplierId'];?>">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>厂家产品编号：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm" name="product[supplierSerialnumber]" required value="<?php echo $data['supplierSerialnumber'];?>">
    </div>
  </div>
   <!-- 没有错误信息不展示此div -->
    <?php if( $error =$this->getError()){?>
  <div class="col-md-5 col-md-offset-1 alert alert-danger">
	<?php if(is_array( $error )){
		foreach( $error as $val ) {
		echo $val['0'].'<br/>';
	}}else{
		echo $error;
	}?>
  </div>
  <?php } ?>
  <br>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-10">
      <button class="btn btn-success" type="submit">保存采购信息</button>
    </div>
  </div>
</form>
<script>
seajs.use('statics/app/product/create/js/purchase.js');
</script>
<!--查找厂商:/product/publish/ajaxsupplier?keyword=指 ,重新选择厂商后，product[supplierId] 要改为对应ID-->
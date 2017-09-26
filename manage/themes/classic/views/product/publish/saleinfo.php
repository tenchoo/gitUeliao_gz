<link rel="stylesheet" href="/themes/classic/statics/modules/uploader/css/style.css"/>
<link rel="stylesheet" href="/themes/classic/statics/app/product/create/css/style.css">
<?php $this->beginContent('_tab',array('active'=>'saleinfo','productId'=>$data['productId'],'productType'=>$data['productType']));$this->endContent();?>
<div class="clearfix alert alert-warning">
  产品编号：<strong class="text-warning"><?php echo $data['serialNumber'];?></strong>
</div>
 <?php $this->beginContent('//layouts/_error');$this->endContent();?>
<form action="" class="form-horizontal" method="post">
  <h3 class="h4">设置价格</h3>说明：设置的零售价和大货价是以基础单位报价。
  <br>
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>基础单位：</label>
    <div class="clearfix col-md-4 unit">
        <select name="product[unitId]" class="form-control input-sm">
      	<?php foreach($units as $key=>$val ){ ?>
      	<option value='<?php echo $key;?>' <?php if($key== $data['unitId']){ echo 'SELECTED';}?>><?php echo $val;?></option>
      	<?php } ?>
      	</select>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">辅助单位：</label>
    <div class="clearfix col-md-4 unit">
        <select name="product[auxiliaryUnit]" class="form-control input-sm">
      	<option value='0'>无辅助单位</option>
      	<?php foreach($units as $key=>$val ){ ?>
      	<option value='<?php echo $key;?>' <?php if($key== $data['auxiliaryUnit']){ echo 'SELECTED';}?>><?php echo $val;?></option>
      	<?php } ?>
      	</select>
        <input type="text" class="form-control int-only input-sm" name="product[unitConversion]" value="<?php echo $data['unitConversion'];?>" placeholder="换算量" maxlength="7">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>零售价：</label>
    <div class="clearfix col-md-4">
      <div class="input-group">
        <input type="text" class="pull-left form-control input-sm price-only" name="product[price]" value="<?php echo Order::priceFormat($data['price']);?>"  maxlength="7">
        <div class="input-group-addon">元</div>
      </div>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>大货价：</label>
    <div class="col-md-4">
      <div class="input-group">
        <input type="text" class="form-control input-sm price-only" name="product[tradePrice]" value="<?php echo Order::priceFormat($data['tradePrice']);?>" maxlength="7">
        <div class="input-group-addon">元</div>
      </div>
    </div>
  </div>
  <h3 class="h4">物流运费设置</h3>
  <br>
  <!--div class="form-group">
    <label class="control-label col-md-2">运费设置：</label>
    <div class="col-md-4">
      <select name="product[expressId]" class="form-control">
        <option value='0'>卖家承担运费</option>
		<?php //foreach($express as $key=>$val ){ ?>
      	<option value='<?php //echo $key;?>' <?php //if($key== $data['expressId']){ echo 'SELECTED';}?>><?php //echo $val;?></option>
      	<?php //} ?>
      </select>
    </div>
  </div-->
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>单位重量：</label>
    <div class="clearfix col-md-4">
      <div class="input-group">
        <input type="text" class="pull-left form-control input-sm price-only" name="product[unitWeight]" value="<?php echo $data['unitWeight'];?>">
        <div class="input-group-addon">千克</div>
      </div>
    </div>
  </div>

  <h3 class="h4">发布时间设置</h3>
  <br>
  <div class="form-group">
    <label class="control-label col-md-2">时间设置：</label>
    <div class="col-md-4 time">
      <label class="radio-inline"><input type="radio" name="product[timeType]" value="1" <?php if( $data['timeType'] != '2' ) { ?>checked <?php } ?>>立即发布</label>
      <label class="radio-inline"><input type="radio" name="product[timeType]" value="2" <?php if( $data['timeType'] == '2' ) { ?>checked <?php } ?>>设置发布时间<input class="form-control input-sm input-date" readonly type="text" <?php if( $data['timeType'] != '2' ) { ?>disabled <?php } ?> name="product[publishTime]" value="<?php if($data['publishTime'] !='0000-00-00 00:00:00'){ echo  $data['publishTime'];}?>"></label>
    </div>
  </div>
  <br/>

  <div class="form-group">
    <div class="col-md-offset-2 col-md-10">
      <button class="btn btn-success" type="submit">保存销售信息</button>
    </div>
  </div>
</form>
<script>
//var specStock = <?php echo json_encode( $data['specStock'] );?> || {};
seajs.use('statics/app/product/create/js/sales.js')
</script>
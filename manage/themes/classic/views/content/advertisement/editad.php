<link rel="stylesheet" href="/themes/classic/statics/modules/uploader/css/style.css"/>
<link rel="stylesheet" href="/themes/classic/statics/app/ad/css/style.css">
<form class="form-horizontal"  method="post">
 <?php $this->beginContent('//layouts/_error');$this->endContent();?>
	<div class="form-group">
    <label class="control-label col-md-2" for="">所在页面：</label>
    <div class="col-md-4 control-label ">
      <p class="text-muted text-left"><?php echo $pageTitle;?></p>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span> 广告标题：</label>
    <div class="col-md-4">
      <input type="text" class="form-control input-sm"  name="data[title]" value="<?php echo $title;?>">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span> 链接地址：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="data[link]" value="<?php  echo $link;?>" />
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for="">广告描述：</label>
    <div class="col-md-4">
	<textarea name="data[description]" class="form-control"><?php echo $description;?></textarea>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span> 广告图片：</label>
    <div class="col-md-4">
      <p class="text-muted">图片大小不超过2M，长宽<?php echo $width;?>*<?php echo $height;?>像素</p>
      <div class="uploader uploader-image">
          <button class="image-wrap" type="button">
            <?php if(!empty($image)){?>
              <img src="<?php echo $this->img(false).$image;?>" alt="" width="80" height="80"><span class="bg"></span><span>重新上传</span>
              <?php }?>
          </button>
          <input type="hidden" name="data[image]" value="<?php echo $image?>"/>
        </div>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for="">图片替换广本：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="data[replaceText]" value="<?php echo $replaceText;?>" />
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for="">客户姓名：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="data[customerName]" value="<?php echo $customerName;?>" maxlength="10">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for="">客户手机：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="data[customerTel]" value="<?php echo $customerTel;?>" >
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for="">广告价格：</label>
    <div class="col-md-6 ad-price">
	    <div class="input-group pull-left">
	      <input type="text" class="pull-left form-control input-sm"  name="data[price]" value="<?php echo Order::priceFormat($price);?>" maxlength="10">
	      <div class="input-group-addon">元</div>
	    </div>
	    <select name="data[priceCycle]" class="form-control input-sm pull-left">
		<?php foreach( $cycles as $val ){?>
			 <option value="<?php echo $val;?>" <?php if( $val==$priceCycle ){echo 'selected';}?>><?php echo $val;?></option>
		<?php }?>
	    </select>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for="">广告排序：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="data[listOrder]" value="<?php echo $listOrder;?>" maxlength="5">
    <p class="text-muted">为数字，数字越小越靠边前</p>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for="">开始时间：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm input-date"  name="data[startTime]" value="<?php echo $startTime;?>" readonly>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for="">结束时间：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm input-date"  name="data[endTime]" value="<?php echo $endTime;?>" readonly>
    <p class="text-muted">不填写则为永久有效。</p>
    </div>
  </div>
  <br/>
  <div class="col-md-offset-2 col-md-10">
    <button class="btn btn-sm btn-success" type="submit">保存</button>
  </div>
</form>
<script>
  seajs.use('statics/app/ad/js/addad.js')
</script>
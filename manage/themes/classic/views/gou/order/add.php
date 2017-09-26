<link rel="stylesheet" href="/themes/classic/statics/modules/uploader/css/style.css"/>
<form class="form-horizontal"  method="post">
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>订单号：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="data[orderId]" value="<?php echo $orderId;?>" maxlength="20">
    </div>
  </div>
   <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>收货人：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="data[name]" value="<?php echo $name;?>" maxlength="20">
    </div>
  </div>
   <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>手机号码：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="data[phone]" value="<?php echo $phone;?>" maxlength="20">
    </div>
  </div>
    <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>订单地址：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="data[orderAddress]" value="<?php echo $orderAddress;?>" maxlength="100">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>片区：</label>
    <div class="col-md-4">
    <?php echo CHtml::dropDownList('data[areaId]',$areaId,$areas,array('class'=>'form-control'))?>
    </div>
  </div>
    <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>收货地址：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="data[deliveryAddress]" value="<?php echo $deliveryAddress;?>" maxlength="100">
    </div>
  </div>
   <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>数量：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="data[num]" value="<?php echo $num;?>" maxlength="6">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>预约送货日期：</label>
    <div class="col-md-4">
    <input type="text"  name="data[appointment]" value="<?php echo $appointment;?>" class="form-control input-sm input-date" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>配送：</label>
    <div class="col-md-4">
    <?php echo CHtml::dropDownList('data[state]',$state,$states,array('class'=>'form-control'))?>
    </div>
  </div>

   <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>送货员：</label>
    <div class="col-md-4">
    <?php echo CHtml::dropDownList('data[deliverymanId]',$deliverymanId,$mems,array('class'=>'form-control'))?>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for=""><span class="text-danger">*</span>商品标题：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="data[title]" value="<?php echo $title;?>" maxlength="50">
    </div>
  </div>
   <div class="form-group">
    <label class="control-label col-md-2" for="">客户留言：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="data[remark]" value="<?php echo $remark;?>" maxlength="50">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for="">商家备注：</label>
    <div class="col-md-4">
    <input type="text" class="form-control input-sm"  name="data[shopRemark]" value="<?php echo $shopRemark;?>" maxlength="50">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for="">送货备注：</label>
    <div class="col-md-4">
		<?php foreach(  $ops as  $_op  ) {
			echo $_op['opTime'].'&nbsp;'.$_op['remark'].'<br>';
	  }?>
    </div>
  </div>

  <?php $this->beginContent('//layouts/_error');$this->endContent();?>
  <br/>
  <div class="col-md-offset-2 col-md-10">
    <button class="btn btn-success" type="submit"  id="btn-add">保存</button>
    <a href="<?php echo $this->createUrl( 'index' );?>" class="btn btn-default">取消</a>
  </div>
</form>
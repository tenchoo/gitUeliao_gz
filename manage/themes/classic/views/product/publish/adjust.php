<link rel="stylesheet" href="/themes/classic/statics/app/product/create/css/style.css">
<?php $this->beginContent('_tab',array('active'=>'adjustratio','productId'=>$productId,'productType'=>$productType));$this->endContent();?>
<div class="clearfix alert alert-warning">
  产品编号：<strong class="text-warning"><?php echo $serialNumber;?></strong>
</div>
<form action="" class="form-horizontal" method="post">
  <div class="form-group">
    <label class="control-label col-md-2"><span class="text-danger">*</span>设置比例：</label>
    <div class="control-div spec-set col-md-8">
      <div class="well batch-spec-set">
        批量设置（千分比）：<input type="text" class="form-control input-sm int-only" maxlength="3">
        <button class="btn btn-default btn-sm batch-save" type="button">设置</button>
      </div>
      <ul class="clearfix inventory colors-checked list-unstyled">
         <?php foreach($model as $val){ ?>
        <li data-rel="<?php echo $val['relation'];?>">
          <span class="c" style="background:#<?php echo $val['code'];?>">
        	<?php if(!empty( $val['picture'] )) { ?>
        		<img src="<?php echo $val['picture'];?>" alt="" width="32" height="32">
        	<?php } ?>
          </span>
          <span class="text">
        	<?php foreach( $val['spec'] as $sval ){ ?>
			<?php echo $sval['title'];?>
        	<?php echo $sval['serialNumber'];?>
        	<?php } ?>
        	</span>
          <input type="text" class="form-control input-sm int-only" placeholder="千分比" value="<?php echo $val['adjustRatio'];?>" name="form[<?php echo $val['stockId']?>]" maxlength="3">‰
        </li>
        <?php  }?>
      </ul>
    </div>
  </div>
  <br>
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-10">
      <button class="btn btn-success" type="submit">保存信息</button>
    </div>
  </div>
</form>
<script>seajs.use('statics/app/product/create/js/batch.js')</script>
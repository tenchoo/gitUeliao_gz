<?php $this->beginContent('_tab',array('active'=>'glass','productId'=>$productId));$this->endContent();?>
<div class="clearfix alert alert-warning">
  产品编号：<strong class="text-warning"><?php echo $serialNumber;?></strong>
</div>
<br>
<form action="" method="post">
<table class="table table-striped table-condensed table-hover table-bordered">
   <colgroup><col width="20%"><col></colgroup>
   <thead>
    <tr>
     <td>等级名称</td>
     <td>呆滞时长（天）</td>
    </tr>
   </thead>
   </table>
   <br>
   <table class="table table-condensed table-bordered">
    <colgroup><col width="20%"><col></colgroup>
   <tbody>
   <?php foreach(  $list as $val  ):?>
    <tr>
	 <td><?php echo $val['title'];?>
		<?php if ( !empty ( $val['logo'] ) ){?>
		<img src="<?php echo $this->img().$val['logo'];?>" alt="" height="20">
		<?php }?>
	</td>
     <td>
	 <input class="form-control input-sm int-only" type="text" name="form[<?php echo $val['id']?>]" value="<?php echo $val['conditions'] ?>" style="width:200px" maxlength="4"/></td>
    </tr>
   <?php endforeach;?>
   </tbody>
  </table>
  <br>
<?php $this->beginContent('//layouts/_error');$this->endContent();?>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-10">
      <button class="btn btn-success" type="submit">保存</button>
    </div>
  </div>
  </form>
 <br>
 <script>seajs.use('statics/app/product/create/js/glass.js')</script>
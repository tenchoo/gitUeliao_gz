<link rel="stylesheet" href="/themes/classic/statics/modules/uploader/css/style.css"/>
<link rel="stylesheet" href="/themes/classic/statics/app/member/css/style.css"/>
<?php $this->beginContent('_tabs');$this->endContent();?>
<br>
<form class="form-horizontal" method="post" action="">
<input name="memberId" type="hidden" value="<?php echo $infos['memberId'];?>" />
  <div class="form-group">
    <label class="control-label col-md-2">所属客户组：</label>
    <div class="col-md-4">
      <?php echo CHtml::dropDownList('groupId',$infos['groupId'],$groupList,array('class'=>'form-control input-sm'))?>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">所属等级：</label>
    <div class="col-md-4">
      <?php echo CHtml::dropDownList('level',$infos['level'],$levelList,array('class'=>'form-control input-sm','empty'=>'请选择客户等级'))?>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">业务员：</label>
    <div class="col-md-4">
		<input type="text" value="<?php echo $saleName;?>" class="form-control input-sm" disabled >
	  
      <?php //echo CHtml::dropDownList('userId',$infos['userId'],$saleList,array('class'=>'form-control input-sm'))?>
    </div>
  </div>
  <!--div class="form-group">
    <label class="control-label col-md-2">付款方式：</label>
    <div class="col-md-4">
	 <?php //foreach ($payModels as $key=>$payval ){ ?>
		 <div class="<?php //if($key=='1'){ ?>paymonthly <?php //}else{ ?>form-margin-top <?php //} ?>">
			<label class="checkbox-inline">
				<input type="checkbox" name="payModel[<?php //echo $key;?>]" value="<?php //echo $key;?>" <?php //if( $key>1 || isset($infos['payModel'][$key])) {//echo 'checked';}?> <?php //if( $key>1 ){ echo 'disabled';}?> />
				<?php //echo $payval;?>
			</label>
			<?php //if($key=='1'){ ?>
			 <span>
			<?php //foreach ( $monthlyMethods as $key=>$val ){ ?>
				 <label class="radio-inline">
				 <input type="radio" name="monthlyType" value="<?php //echo $val;?>" <?php //if ( $infos['monthlyType'] == $val || ( $key=='0' && empty($infos['monthlyType']) ) ) { echo 'checked';} ?> disabled="disabled"/>
					<?php //echo $val;?>
				 </label>
			<?php //} ?>
			</span>
			<?php //} ?>
		</div>
	 <?php //} ?>
    </div>
  </div-->
  <div class="form-group">
    <label class="control-label col-md-2">价格：</label>
    <div class="col-md-4">
      <label class="radio-inline"><input type="radio" name="priceType" value="0" <?php if ($infos['priceType'] == 0) {echo 'checked';}?>/>零售价</label>
      <label class="radio-inline"><input type="radio" name="priceType" value="1" <?php if ($infos['priceType'] == 1) {echo 'checked';}?>/>大货价</label>
    </div>
  </div>
    <div class="form-group">
    <label class="control-label col-md-2">是否月结：</label>
	 <div class="col-md-4">
		<div class="paymonthly">
			<label class="checkbox-inline">
				<input type="checkbox" value="1" name="monthlyPay" id="monthlyPay" <?php if ( $monthlyPay ) {echo 'checked';}?>>月结</label>
			<span>
				<label class="radio-inline">
					信用额度：<input type="text" name="credit" id="credit" disabled="disabled" value="<?php echo $credit;?>" class="int-only"/> 元 </label>
			</span>
		</div>
	</div>
  </div>
   <div class="form-group">
    <label class="control-label col-md-2" for="address">月结周期：</label>
    <div class="col-md-4">
	      <select name="billingCycle" class="form-control input-sm" id="billingCycle" disabled="disabled">
	        <option value="">请选择月结周期</option>
			<?php for ( $i =1;$i<=12;$i++ ){ ?>
			 <option value="<?php echo $i;?>" <?php if( $i == $billingCycle ) {echo 'selected="selected"';}?>><?php echo $i;?>个月</option>
			<?php }?>
	      </select>
    </div>
  </div>
<?php $this->beginContent('//layouts/_error');$this->endContent();?><br/>

  <div class="form-group">
    <div class="col-md-offset-2 col-md-10">
      <button class="btn btn-success">保存</button>
    </div>
  </div>

</form>

<?php /*if( $action != 'edit' ){
<script>
$(function(){
   $("input").attr("disabled",true);
   $("select").attr("disabled",true);
   $("button").attr("disabled",true);
   $("textarea").attr("disabled",true);
});
</script>
<?php }?>
  */?>
<script>
 seajs.use('statics/app/member/js/setting.js');
</script>


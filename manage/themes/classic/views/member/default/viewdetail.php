<link rel="stylesheet" href="/themes/classic/statics/modules/uploader/css/style.css" />
<link rel="stylesheet" href="/themes/classic/statics/app/member/css/style.css" />
<?php $this->beginContent('_tabs');$this->endContent();?>
<br>
<form class="form-horizontal modifydetail" method="post" action="">
  <div class="form-group">
    <label class="control-label col-md-2" for="company">公司名称：</label> 
    <div class="col-md-4">
      <input type="text" name="companyname" class="form-control input-sm" id="company" value="<?php echo $infos['companyname']?>" disabled="disabled"/>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for="address">公司地址：</label>
    <div class="col-md-4">
      <div class="inline-block area-select">
	      <select name="" class="form-control input-sm province" disabled="disabled">
	        <option value="default">请选择省份</option>
	      </select> <select name="" class="form-control input-sm city" disabled="disabled">
	        <option value="default">请选择市</option>
	      </select> <select name="" class="form-control input-sm county" disabled="disabled">
	        <option value="default">请选择区/县</option>
	      </select> 
	      <input type="hidden" name="areaId" class="form-control input-sm" value="<?php echo $infos['areaId']?>" />
      </div>
	    <div class="form-group-offset form-margin-top">
	      <input type="text" name="address" class="form-control input-sm" id="address" placeholder="详细地址" value="<?php echo $infos['address']?>" disabled="disabled"/>
	    </div>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for="corporate">企业法人：</label> 
    <div class="col-md-4">
      <input type="text" name="corporate" class="form-control input-sm" id="corporate" value="<?php echo $infos['corporate']?>" disabled="disabled"/>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for="telephone">电话：</label> 
    <div class="col-md-4">
      <input type="text" name="tel" class="form-control input-sm" id="telephone" value="<?php echo $infos['tel']?>" disabled="disabled"/>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for="company-type">公司性质：</label>
    <div class="col-md-4">
      <?php echo CHtml::dropDownList('companytype',$infos['companytype'],array('1'=>'自产自销','2'=>'贸易型','3'=>'生产型'),array('class'=>'form-control input-sm','empty'=>'请选择公司性质','disabled'=>'disabled'))?>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for="sale-region">销售区域：</label>
    <div class="col-md-4">
      <?php echo CHtml::dropDownList('saleregion',$infos['saleregion'],array('1'=>'内销','2'=>'外销'),array('class'=>'form-control input-sm','empty'=>'请选择销售区域','disabled'=>'disabled'))?>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for="company-type">主营产品：</label>
    <div class="col-md-4">
      <?php echo CHtml::checkBoxList('mainproduct',$infos['mainproduct'],array('1'=>'钱包','2'=>'男包','3'=>'女包','4'=>'其它'),array('template'=>'<label class="checkbox-inline">{input}{label}</label>','separator'=>'','disabled'=>'disabled'))?>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for="people-number">生产人数：</label> 
    <div class="col-md-4">
      <input type="text" name="peoplenumber" class="form-control input-sm" id="people-number" value="<?php echo $infos['peoplenumber']?>" disabled="disabled"/>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for="output-value">年产值：</label> 
    <div class="col-md-4">
      <input type="text" name="outputvalue" class="form-control input-sm" id="output-value" value="<?php echo $infos['outputvalue']?>" disabled="disabled"/>
    </div>
    <div class="inline-block">万元</div> 
  </div>
  <div class="form-group">
    <label class="control-label col-md-2" for="brand">产品品牌：</label> 
    <div class="col-md-4">
      <input type="text" name="brand" class="form-control input-sm" id="brand" value="<?php echo $infos['brand']?>" disabled="disabled"/>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">有无档口：</label>
    <div class="col-md-4">
    	<label class="radio-inline"><input type="radio" name="stalls" value="1" <?php echo ($infos['stalls']== 1 )?'checked':''?> disabled="disabled"/>有</label> 
      <label class="radio-inline"><input type="radio" name="stalls" value="2" <?php echo ($infos['stalls']== 2 )?'checked':''?> disabled="disabled"/>无</label>
	    <div class="form-group-offset form-margin-top">
	      <input type="text" name="stallsaddress" class="form-control input-sm" placeholder="档口地址" value="<?php echo $infos['stallsaddress']?>" disabled="disabled"/>
	    </div>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-md-2">有无工厂：</label> 
    <div class="col-md-4">
      <label class="radio-inline"><input type="radio" name="factory" value="1" <?php echo ($infos['factory']== 1 )?'checked':''?> disabled="disabled"/>有</label> 
      <label class="radio-inline"><input type="radio" name="factory" value="2" <?php echo ($infos['factory']== 2 )?'checked':''?> disabled="disabled"/>无</label>
	    <div class="form-group-offset form-margin-top">
	      <?php echo CHtml::dropDownList('factoryatt',$infos['factoryatt'],array('1'=>'自建','2'=>'购买','3'=>'租赁'),array('class'=>'form-control input-sm','empty'=>'请选择工厂类型','disabled'=>'disabled'))?>
	    </div>
    </div>
  </div>
  <div class="form-group form-secant"></div>
  <div class="form-group contacts-hd">
    <label class="control-label col-md-2">内部联系人</label>
  </div>
  <div class="form-group form-contacts">
    <label class="control-label col-md-2">总经理：</label> 
    <div class="col-md-8">
	    <input type="text" name="gm[name]" class="form-control input-sm" placeholder="名字" value="<?php if( isset($infos['gm']['name']) ){ echo $infos['gm']['name']; } ?>" disabled="disabled"/> 
	    <input type="text" name="gm[phone]" class="form-control input-sm" placeholder="手机" value="<?php if( isset($infos['gm']['phone']) ){ echo $infos['gm']['phone']; }?>" disabled="disabled"/> 
	    <span> 
	      <label class="radio-inline"><input type="radio" name="gm[sex]" value="1" <?php if( isset($infos['gm']['sex']) && $infos['gm']['sex'] == 1 ) { echo 'checked'; } ?> disabled="disabled"/>男</label> 
	      <label class="radio-inline"><input type="radio" name="gm[sex]" value="2" <?php if( isset($infos['gm']['sex']) && $infos['gm']['sex'] == 2 ) { echo 'checked'; }?> disabled="disabled"/>女</label>
	    </span>
    </div>
  </div>
  <div class="form-group form-contacts">
    <label class="control-label col-md-2">采购经理：</label> 
    <div class="col-md-8">
	    <input type="text" name="pdm[name]" class="form-control input-sm" placeholder="名字" value="<?php if( isset($infos['pdm']['name']) ){ echo $infos['pdm']['name']; } ?>" disabled="disabled"/> 
	    <input type="text" name="pdm[phone]" class="form-control input-sm" placeholder="手机" value="<?php if( isset($infos['pdm']['phone']) ){ echo $infos['pdm']['phone']; }?>" disabled="disabled"/> 
	    <span> 
	      <label class="radio-inline"><input type="radio" name="pdm[sex]" value="1" <?php if( isset($infos['pdm']['sex']) && $infos['pdm']['sex'] == 1 ) { echo 'checked'; } ?> disabled="disabled"/>男</label> 
	      <label class="radio-inline"><input type="radio" name="pdm[sex]" value="2" <?php if( isset($infos['pdm']['sex']) && $infos['pdm']['sex'] == 2 ) { echo 'checked'; } ?> disabled="disabled"/>女</label>
	    </span>
    </div>
  </div>
  <div class="form-group form-contacts">
    <label class="control-label col-md-2">设计人员：</label> 
    <div class="col-md-8">
	    <input type="text" name="designers[name]" class="form-control input-sm" placeholder="名字" value="<?php if( isset($infos['designers']['name']) ){ echo $infos['designers']['name']; } ?>" disabled="disabled"/> 
	    <input type="text" name="designers[phone]" class="form-control input-sm" placeholder="手机" value="<?php if( isset($infos['designers']['phone']) ){ echo $infos['designers']['phone']; }?>" disabled="disabled"/> 
	    <span> 
	      <label class="radio-inline"><input type="radio" name="designers[sex]" value="1" <?php if( isset($infos['designers']['sex']) && $infos['designers']['sex'] == 1 ) { echo 'checked'; } ?> disabled="disabled"/>男</label> 
	      <label class="radio-inline"><input type="radio" name="designers[sex]" value="2" <?php if( isset($infos['designers']['sex']) && $infos['designers']['sex'] == 2 ) { echo 'checked'; } ?> disabled="disabled"/>女</label>
	    </span>
    </div>
  </div>
  <div class="form-group form-contacts">
    <label class="control-label col-md-2">财务经理：</label> 
    <div class="col-md-8">
      <input type="text" name="cfo[name]" class="form-control input-sm" placeholder="名字" value="<?php if( isset($infos['cfo']['name']) ){ echo $infos['cfo']['name']; } ?>" disabled="disabled"/> 
	    <input type="text" name="cfo[phone]" class="form-control input-sm" placeholder="手机" value="<?php if( isset($infos['cfo']['phone']) ){ echo $infos['cfo']['phone']; }?>" disabled="disabled"/> 
	    <span> 
	      <label class="radio-inline"><input type="radio" name="cfo[sex]" value="1" <?php if( isset($infos['cfo']['sex']) && $infos['cfo']['sex'] == 1 ) { echo 'checked'; } ?> disabled="disabled"/>男</label> 
	      <label class="radio-inline"><input type="radio" name="cfo[sex]" value="2" <?php if( isset($infos['cfo']['sex']) && $infos['cfo']['sex'] == 2 ) { echo 'checked'; } ?> disabled="disabled"/>女</label>
	    </span>
    </div>
  </div>
</form>
<script>
  seajs.use('statics/app/member/js/modifydetail.js');
</script>
<link rel="stylesheet" href="/app/member/setting/css/style.css"/>
	<div class="pull-right frame-content">
		<div class="frame-tab profile">
      <ul class="clearfix list-unstyled frame-tab-hd">
        <li><a href="<?php echo $infourl;?>">基本资料</a></li>
        <li class="active"><a href="javascript:">详细资料</a></li>
      </ul>
      <div class="frame-tab-bd frame-tab-bd-active">
          <div class="form-horizontal">
            <form action="" method="post">
              <div class="form-group">
                <label class="control-label" for="company"><span class="text-red">*</span>公司名称：</label>
                <input type="text" name="Editdetailinfo[companyname]" class="form-control input-xs" id="company" value="<?php echo $info['companyname']?>" maxlength="60"/>
              </div>
			  <div class="form-group">
                <label class="control-label" for="shortname"><span class="text-red">*</span>公司简称：</label>
                <input type="text" name="Editdetailinfo[shortname]" class="form-control input-xs" id="shortname" value="<?php echo $info['shortname']?>" maxlength="10"/>
              </div>
              <div class="form-group">
                <label class="control-label" for="address">公司地址：</label>
                <div class="inline-block area-select">
                  <select name="" class="form-control input-xs province">
                    <option value="default">请选择省份</option>
                  </select>
                  <select name="" class="form-control input-xs city">
                    <option value="default">请选择市</option>
                  </select>
                  <select name="" class="form-control input-xs county">
                    <option value="default">请选择区/县</option>
                  </select>
                  <input type="hidden" name="Editdetailinfo[areaId]" class="form-control input-xs" value="<?php echo $info['areaId']?>" />
                </div>
                <div class="form-group-offset form-margin-top">
                  <input type="text" name="Editdetailinfo[address]" class="form-control input-xs" id="address" placeholder="详细地址" value="<?php echo $info['address']?>"/>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label" for="corporate">企业法人：</label>
                <input type="text" name="Editdetailinfo[corporate]" class="form-control input-xs" id="corporate" value="<?php echo $info['corporate']?>" maxlength="8"/>
              </div>
              <div class="form-group">
                <label class="control-label" for="telephone">电话：</label>
                <input type="text" name="Editdetailinfo[tel]" class="form-control input-xs" id="telephone" value="<?php echo $info['tel']?>" maxlength="17"/>
              </div>
              <div class="form-group">
                <label class="control-label" for="company-type">公司性质：</label>
                <?php echo CHtml::dropDownList('Editdetailinfo[companytype]',$info['companytype'],array('1'=>'自产自销','2'=>'贸易型','3'=>'生产型'),array('class'=>'form-control input-xs','empty'=>'请选择公司性质'))?>
              </div>
              <div class="form-group">
                <label class="control-label" for="sale-region">销售区域：</label>
                <?php echo CHtml::dropDownList('Editdetailinfo[saleregion]',$info['saleregion'],array('1'=>'内销','2'=>'外销'),array('class'=>'form-control input-xs','empty'=>'请选择销售区域'))?>
              </div>
              <div class="form-group">
                <label class="control-label" for="company-type">主营产品：</label>
                <?php echo CHtml::checkBoxList('Editdetailinfo[mainproduct]',$info['mainproduct'],array('1'=>'钱包','2'=>'男包','3'=>'女包','4'=>'其它'),array('template'=>'<label class="checkbox-inline">{input}{label}</label>','separator'=>''))?>
              </div>
              <div class="form-group">
                <label class="control-label" for="people-number">生产人数：</label>
                <input type="text" name="Editdetailinfo[peoplenumber]" class="form-control input-xs" id="people-number" value="<?php echo $info['peoplenumber']?>" maxlength="8"/>
              </div>
              <div class="form-group">
                <label class="control-label" for="output-value">年产值：</label>
                <input type="text" name="Editdetailinfo[outputvalue]" class="form-control input-xs" id="output-value" value="<?php echo $info['outputvalue']?>" maxlength="8"/>
                <span>万元</span>
              </div>
              <div class="form-group">
                <label class="control-label" for="brand">产品品牌：</label>
                <input type="text" name="Editdetailinfo[brand]" class="form-control input-xs" id="brand" value="<?php echo $info['brand']?>" maxlength="20"/>
              </div>
              <div class="form-group">
                <label class="control-label">有无档口：</label>
                <?php echo CHtml::radioButtonList('Editdetailinfo[stalls]',$info['stalls'],array('1'=>'有','2'=>'无'),array('template'=>'<label class="radio-inline">{input}{label}</label>','separator'=>''))?>
                <div class="form-group-offset form-margin-top">
                  <input type="text" name="Editdetailinfo[stallsaddress]" class="form-control input-xs" placeholder="档口地址" value="<?php echo $info['stallsaddress']?>"/>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label">有无工厂：</label>
                <label class="radio-inline"><input type="radio" name="Editdetailinfo[factory]" value="1" <?php echo ($info['factory']== 1 )?'checked':''?>/>有</label>
                <label class="radio-inline"><input type="radio" name="Editdetailinfo[factory]" value="2" <?php echo ($info['factory']== 2 )?'checked':''?>/>无</label>
                <div class="form-group-offset form-margin-top">
                	<?php echo CHtml::dropDownList('Editdetailinfo[factoryatt]',$info['factoryatt'],array('1'=>'自建','2'=>'购买','3'=>'租赁'),array('class'=>'form-control input-xs','empty'=>'请选择工厂类型'))?>
                </div>
              </div>
              <div class="form-group form-secant"></div>
              <div class="form-group contacts-hd">
                <label class="control-label">内部联系人</label>
              </div>
              <div class="form-group form-contacts">
                <label class="control-label">总经理：</label>
                <input type="text" name="Editdetailinfo[gm][name]" class="form-control input-xs" placeholder="名字" value="<?php echo $info['gm']['name']?>" maxlength="10"/>
                <input type="text" name="Editdetailinfo[gm][phone]" class="form-control input-xs" placeholder="手机" value="<?php echo $info['gm']['phone']?>" maxlength="11"/>
                <span>
                  <label class="radio-inline"><input type="radio" name="Editdetailinfo[gm][sex]" value="1" <?php if( isset($info['gm']['sex']) && $info['gm']['sex'] == 1 ) { echo 'checked'; } ?>/>男</label>
                  <label class="radio-inline"><input type="radio" name="Editdetailinfo[gm][sex]" value="2" <?php if( isset($info['gm']['sex']) && $info['gm']['sex'] == 2 ) { echo 'checked'; }?>/>女</label>
                </span>
              </div>
              <div class="form-group form-contacts">
                <label class="control-label">采购经理：</label>
                <input type="text" name="Editdetailinfo[pdm][name]" class="form-control input-xs" placeholder="名字" value="<?php echo $info['pdm']['name']?>" maxlength="10" />
                <input type="text" name="Editdetailinfo[pdm][phone]" class="form-control input-xs" placeholder="手机" value="<?php echo $info['pdm']['phone']?>" maxlength="11" />
                <span>
                  <label class="radio-inline"><input type="radio" name="Editdetailinfo[pdm][sex]" value="1" <?php if( isset($info['pdm']['sex']) && $info['pdm']['sex'] == 1 ) { echo 'checked'; } ?>/>男</label>
                  <label class="radio-inline"><input type="radio" name="Editdetailinfo[pdm][sex]" value="2" <?php if( isset($info['pdm']['sex']) && $info['pdm']['sex'] == 2 ) { echo 'checked'; } ?>/>女</label>
                </span>
              </div>
              <div class="form-group form-contacts">
                <label class="control-label">设计人员：</label>
                <input type="text" name="Editdetailinfo[designers][name]" class="form-control input-xs" placeholder="名字" value="<?php echo $info['designers']['name']?>" maxlength="10" />
                <input type="text" name="Editdetailinfo[designers][phone]" class="form-control input-xs" placeholder="手机" value="<?php echo $info['designers']['phone']?>" maxlength="11" />
                <span>
                  <label class="radio-inline"><input type="radio" name="Editdetailinfo[designers][sex]" value="1" <?php if( isset($info['designers']['sex']) && $info['designers']['sex'] == 1 ) { echo 'checked'; } ?>/>男</label>
                  <label class="radio-inline"><input type="radio" name="Editdetailinfo[designers][sex]" value="2" <?php if( isset($info['designers']['sex']) && $info['designers']['sex'] == 2 ) { echo 'checked'; } ?>/>女</label>
                </span>
              </div>
              <div class="form-group form-contacts">
                <label class="control-label">财务经理：</label>
                <input type="text" name="Editdetailinfo[cfo][name]" class="form-control input-xs" placeholder="名字" value="<?php echo $info['cfo']['name']?>" maxlength="10" />
                <input type="text" name="Editdetailinfo[cfo][phone]" class="form-control input-xs" placeholder="手机" value="<?php echo $info['cfo']['phone']?>" maxlength="11" />
                <span>
                  <label class="radio-inline"><input type="radio" name="Editdetailinfo[cfo][sex]" value="1" <?php if( isset($info['cfo']['sex']) && $info['cfo']['sex'] == 1 ) { echo 'checked'; } ?>/>男</label>
                  <label class="radio-inline"><input type="radio" name="Editdetailinfo[cfo][sex]" value="2" <?php if( isset($info['cfo']['sex']) && $info['cfo']['sex'] == 2 ) { echo 'checked'; } ?>/>女</label>
                </span>
              </div>
              <div class="form-group form-group-offset">
                <button class="btn btn-success btn-xs" type="submit">保存</button>
              </div>
            </form>
          </div>
        </div>
      </div>
	  </div>
<script>
  seajs.use('app/member/setting/js/detail.js');
</script>
<div class="form-group">
<input type="hidden" name="MemberAddress[addressId]" value="<?php echo $data->addressId?>" />
  <label class="control-label" for="name"><span class="text-red">*</span>收货人姓名：</label>
  <input name="MemberAddress[name]" class="form-control input-xs" id="name" type="text" value="<?php echo $data->name?>">
</div>
<div class="form-group">
  <label class="control-label" for="address"><span class="text-red">*</span>省份：</label>
  <div class="inline-block area-select">
    <select name="province" class="form-control input-xs province">
      <option value="default">请选择省份</option>
    </select>
    <select name="city" class="form-control input-xs city">
      <option value="default">请选择市</option>
    </select>
    <select name="county" class="form-control input-xs county">
      <option value="default">请选择区/县</option>
    </select>
    <input type="hidden" name="MemberAddress[areaId]" class="form-control input-xs" value="<?php echo $data->areaId?>" />
  </div>
</div>
<div class="form-group">
  <label class="control-label" for="street-address"><span class="text-red">*</span>地址：</label>
  <input name="MemberAddress[address]" class="form-control input-xs" id="street-address" type="text"  value="<?php echo $data->address?>">
</div>
<div class="form-group">
  <label class="control-label" for="phone"><span class="text-red">*</span>手机：</label>
  <input name="MemberAddress[mobile]" class="form-control input-xs" id="phone" type="text" value="<?php echo $data->mobile?>">
</div>
<div class="form-group">
  <label class="control-label" for="landline">固定电话：</label>
  <input name="MemberAddress[tel]" class="form-control input-xs" id="landline" type="text"  value="<?php echo $data->tel?>">
</div>
<div class="form-group">
  <label class="control-label" for="zip-code">邮政编码：</label>
  <input name="MemberAddress[zip]" class="form-control input-xs" id="zip-code" type="text" value="<?php echo $data->zip?>">
</div>
<div class="form-group form-group-offset">
  <button class="btn btn-warning btn-xs" type="submit" data-loading="保存中...">保存信息</button>
</div>
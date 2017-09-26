<link rel="stylesheet" href="/app/member/setting/css/style.css"/>

	<div class="pull-right frame-content">
		<div class="frame-tab">
      <ul class="clearfix list-unstyled frame-tab-hd">
        <li class="active"><a href="javascript:">新增客户</a></li>
      </ul>
      <div class="frame-tab-bd add-client frame-tab-bd-active">
        <div class="required">（说明：以下均为必填项）</div>
			  <div class="form-horizontal">

			    <form action="" method="post">
			      <div class="form-group">
              <label class="control-label" for="companyname">公司名称：</label>
              <input type="text" name="info[companyname]" class="form-control input-xs" id="companyname" data-help="不能为空" maxlength="50"/>
            </div>
            <div class="form-group area">
              <label class="control-label">公司地址：</label>
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
                <input type="text" name="info[areaId]" />
              </div>
            </div>
            <div class="form-group">
              <label class="control-label" for="address"></label>
              <input type="text" name="info[address]" class="form-control input-xs" id="address" data-help="不能为空" maxlength="80"/>
            </div>
            <div class="form-group">
              <label class="control-label" for="contactPerson">联系人：</label>
              <input type="text" name="info[contactPerson]" class="form-control input-xs" id="contactPerson" data-help="不能为空" maxlength="10" />
            </div>
            <div class="form-group">
			        <label class="control-label" for="phone">手机号码：</label>
			        <input type="text" name="info[phone]" class="form-control input-xs" id="phone" data-help="不能为空" maxlength="11" />
			      </div>
			      <div class="form-group">
			        <label class="control-label" for="password">登录密码：</label>
			        <input type="password" name="info[password]" class="form-control input-xs" id="password" data-help="不能为空" maxlength="16" />
			      </div>
				  <div class="form-group">
			        <label class="control-label" for="repassword">确认密码：</label>
			        <input type="password" name="info[repassword]" class="form-control input-xs" id="repassword" data-help="不能为空" maxlength="16" />
			      </div>
			      <div class="form-group form-group-offset">
			        <button class="btn btn-success btn-xs" type="submit" data-loading="保存中...">下一步，完善客户资料</button>
			      </div>
			    </form>
			  </div>
    	</div>
    </div>
	</div>
<script>
  seajs.use('app/member/setting/js/add.js');
</script>
<link rel="stylesheet" href="/app/member/setting/css/style.css"/>
<div class="pull-right frame-content">
    <div class="frame-box repassword">
      <div class="form-horizontal">
        <form action="" method="post">
          <div class="form-group">
            <label class="control-label" for="oldpassword">旧密码：</label>
            <input type="password" name="passwordForm[oldpassword]"
              class="form-control input-xs append-help no-success-help"
              id="oldpassword" data-help="请输入6-16个字符，密码需字母和数字组合" maxlength="16" />
          </div>
          <div class="form-group">
            <label class="control-label" for="newpassword">新密码：</label>
            <input type="password" name="passwordForm[password]"
              class="form-control input-xs" id="newpassword"
              data-help="请输入6-16个字符，密码需字母和数字组合" maxlength="16"/>
          </div>
          <div class="form-group">
            <label class="control-label" for="agapassword">再次输入新密码：</label>
            <input type="password" name="passwordForm[repassword]"
              class="form-control input-xs" id="agapassword"
              data-help="请再次输入密码" maxlength="16"  />
          </div>
          <div class="form-group form-group-offset">
            <button class="btn btn-success btn-xs" type="submit" data-loading="保存中...">保存</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<script>seajs.use('app/member/setting/js/repassword.js');</script>

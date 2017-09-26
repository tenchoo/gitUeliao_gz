<link rel="stylesheet" href="/app/member/setting/css/style.css"/>
<div class="pull-right frame-content">
      <div class="frame-box paypassword">
        <div class="form-horizontal">
          <p>当前您所使用的手机号码为：<strong><?php echo $phone; ?></strong></p>
          <form action="" method="post">
            <div class="form-group">
              <div class="form-group-offset"><button class="btn btn-cancel btn-xs send-code" type="button">点击发送验证码</button></div>
              <div class="form-group-offset text-minor message">此服务免费，如果您在1分钟内没有收到验证码，请检查您填写的手机号码是否正确或重新发送。</div>
            </div>
            <div class="form-group">
              <label class="control-label" for="verifyCode">验证码：</label>
              <input type="text" name="passwordForm[verifyCode]" class="form-control code no-success-help input-xs" id="verifyCode" maxlength="6"/>
            </div>
            <div class="form-group form-group-offset">
              <button class="btn btn-success btn-xs" type="submit" data-loading="跳转中...">下一步</button>
            </div>
          </form>
        </div>
      </div>
 </div>
  <script>
	seajs.use('app/member/setting/js/changepaypassword.js');
</script>
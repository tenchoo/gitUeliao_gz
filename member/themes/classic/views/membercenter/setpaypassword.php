<link rel="stylesheet" href="/app/member/setting/css/style.css"/>
<?php  if( empty( $phone ) ) { ?>
<div class="pull-right frame-content">
	<div class="frame-box frame-message-box">
  	<div class="success-message">
    	<h2 class="success-message-title"><i class="icon icon-xl icon-info"></i>设置支付密码前需要验证手机！</h2>
			<p class="success-message-link"><span id='setouttime'>5</span>秒后将跳转至验证手机页面，或现在去<span class="success-message-next"><a href="/membercenter/phonechange" class="text-link">验证手机</a></span></p>
    </div>
  </div>
</div>
<script>
	seajs.use('app/member/setting/js/paypassword.js',function(s){
	  s.refresh();
	});
</script>
<?php } else {?>
<div class="pull-right frame-content">
      <div class="frame-box paypassword">
        <div class="form-horizontal">
          <form action="" method="post">
            <div class="form-group">
              <label class="control-label" for="paypassword">支付密码：</label>
              <input type="password" name="passwordForm[paypassword]" class="form-control input-xs" id="paypassword" maxlength="16"/>
            </div>
            <div class="form-group">
              <label class="control-label" for="repassword">再次输入支付密码：</label>
              <input type="password" name="passwordForm[repassword]" class="form-control input-xs" id="repassword" maxlength="16"/>
            </div>
            <div class="form-group clearfix code-wrap">
              <label class="control-label pull-left" for="verifyCode">验证码：</label>
              <input type="text" name="passwordForm[verifyCode]" class="form-control code no-success-help input-xs pull-left" id="verifyCode" maxlength="6"/>
              <img src="<?php echo $this->siteUrl();?>/ajax/default/index/action/captcha" width="80" height="28" alt="点击刷新验证码" class="pull-left refreshcode" title="点击刷新验证码">
              <span class="pull-left text-minor refreshcode help-after-this">看不清换一张</span>
            </div>
            <div class="form-group form-group-offset">
              <button class="btn btn-success btn-xs" type="submit" data-loading="保存中...">保存</button>
            </div>
          </form>
        </div>
      </div>
 </div>
 <script>
	seajs.use('app/member/setting/js/paypassword.js');
</script>
<?php } ?>
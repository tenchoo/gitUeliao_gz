<link rel="stylesheet" href="/app/member/setting/css/style.css"/>
  <div class="pull-right frame-content">
    <div class="frame-box frame-message-box">
    	<div class="success-message">
    		<h2 class="success-message-title"><i class="icon icon-xl icon-success"></i>恭喜您，密码修改成功！</h2>
				<p class="success-message-link"><span id='setouttime'>5</span>秒后将返回登录页面，或现在去<span class="success-message-next">
				<?php echo CHtml::link('重新登录' , ApiClient::model('member')->createUrl('/user/logout',array('to'=>'login')),array('class'=>'text-link') );?>
				</span></p>
    	</div>
    </div>
  </div>
<script type="text/javascript">
	seajs.use('app/member/setting/js/repassword.js',function(s){
  	s.refresh('<?php echo ApiClient::model('member')->createUrl('/user/logout',array('to'=>'login'))?>');
	});
</script>
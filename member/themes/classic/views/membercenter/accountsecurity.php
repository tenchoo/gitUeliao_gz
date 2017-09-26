<link rel="stylesheet" href="/app/member/setting/css/style.css"/>
  <div class="pull-right frame-content">   
    <div class="frame-box security">
      <ul class="list-unstyled">
        <li>
        	<span class="pull-right"><a class="btn btn-xs btn-cancel" href="/membercenter/changepassword">修改密码</a></span>
        	<span class="login"><i class="icon icon-lg icon-success"></i><span>登录密码</span></span>
       		<span class="text-minor">建议您定期修改密码以保证账号安全。</span>
        </li>
        <!--<?php if( $user['showEmail'] ) { ?>
        <li>
        	<span class="pull-right"><a class="btn btn-xs btn-cancel" href="/membercenter/emailcheck">修改邮箱</a></span>
        	<span class="login"><i class="icon icon-lg icon-success"></i><span>邮箱验证</span></span>
        	<span class="text-minor">你验证的邮箱地址为：<?php echo $user['showEmail'];?></span>
        </li>
        <?php } else{ ?>
        <li>
        	<span class="pull-right"><a class="btn btn-xs btn-success" href="/membercenter/emailcheck">立即验证</a></span>
        	<span class="login"><i class="icon icon-lg icon-info"></i><span>邮箱验证</span></span>
        	<span class="text-minor">验证邮箱后您可以直接用邮箱登录。</span>
        </li>
        <?php }?>-->
        <?php if( $user['showphone'] ) { ?>
        <li>
        	<span class="pull-right"><a class="btn btn-xs btn-cancel" href="/membercenter/phonecheck">修改号码</a></span>
        	<span class="login"><i class="icon icon-lg icon-success"></i><span>修改号码</span></span>
        	<span class="text-minor">你验证的手机号码为：<?php echo $user['showphone'];?></span>
        </li>
        <?php } else{ ?>
        <li>
        	<span class="pull-right"><a class="btn btn-xs btn-success" href="/membercenter/phonechange">立即验证</a></span>
        	<span class="login"><i class="icon icon-lg icon-info"></i><span>手机验证</span></span>
        	<span class="text-minor">验证后可接收订单、退款、账户余额等变动提醒，保障您的账户及资金安全。</span>
        </li>
        <?php }?>
        <?php if( $user['paypassword'] ) { ?>
        <li>
        	<span class="pull-right"><a class="btn btn-xs btn-cancel" href="/membercenter/changepaypassword">修改密码</a></span>
        	<span class="login"><i class="icon icon-lg icon-success"></i><span>支付密码</span></span>
        	<span class="text-minor">建议您定期更改密码以保护账户安全</span>
        </li>
        <?php } else{ ?>
        <li>
        	<span class="pull-right"><a class="btn btn-xs btn-success" href="/membercenter/setpaypassword">设置密码</a></span>
        	<span class="login"><i class="icon icon-lg icon-info"></i><span>支付密码</span></span>
        	<span class="text-minor">为了您在支付过程中更加安全，请启用支付密码。</span>
        </li>
        <?php }?>
      </ul>
    </div>
  </div>
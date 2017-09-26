<link rel="stylesheet" href="/modules/button/css/style.css"/>
<link rel="stylesheet" href="/modules/icon/css/style.css"/>
<link rel="stylesheet" href="/modules/form/css/style.css"/>
<link rel="stylesheet" href="/app/member/account/css/style.css"/>
<div class="container head">
  <h1 class="logo pull-left">
    <a href="<?php echo $this->homeUrl;?>"><img src="/app/home/image/logo.png" width="213" height="58" alt="优易料"/></a>
  </h1>
  <ol class="pull-left step list-inline text-bold">
    <li>
      <i>1</i>填写注册信息
    </li>
    <li class="active">
      <i>2</i>注册成功
    </li>
  </ol>
</div>
<div class="container">
	<div class="form-group-offset">
		<div class="success-message">
  		<h2 class="success-message-title"><i class="icon icon-xl icon-success"></i>恭喜您，注册成功！</h2>
    	<p class="success-message-link">您的注册会员名为：<strong class="text-warning"><?php echo Yii::app()->user->name;?></strong><span class="success-message-next">现在去：<a href="<?php echo $this->createUrl('/membercenter/info')?>" class="text-link">完善会员资料</a><a href="<?php echo $this->createUrl('/site/index')?>" class="text-link">回到首页</a></span></p>
		</div>
	</div>
</div>
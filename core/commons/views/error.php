<link rel="stylesheet" href="/app/member/account/css/login.css" />
<h1 class="container logo">
  <a href="<?php echo $this->homeUrl?>"><img src="/app/home/image/logo.png" width="213" height="58" alt="优易料"/></a>
</h1>
<div class="container">
  <div class="pull-left image-notice" data-ad="/ajax?action=spread&mark=notfound">
  </div>
  <div class="pull-right error-notice">
    <h1>抱歉！页面无法访问......</h1>
    <p><span>可能因为：</span></p>
    <p><?php echo $code.' : <span>'.$message.'<span>';?></p>
    <div class="error-url">您可以访问：<a href="<?php echo $this->homeUrl?>">商城首页</a></div>
  </div>
</div>

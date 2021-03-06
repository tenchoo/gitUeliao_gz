<!doctype html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="renderer" content="webkit|ie-stand|ie-comp">
  <title>登录</title>
  <meta name="keywords" content=""/>
  <meta name="description" content=""/>
  <link rel="stylesheet" href="/themes/classic/statics/libs/bootstrap/3.3.5/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="/themes/classic/statics/common/style.css"/>
  <script src="/themes/classic/statics/libs/seajs/2.3.0/sea.js"></script>
  <script src="/themes/classic/statics/libs/seajs/2.3.0/seajs-css.js"></script>
  <script src="/themes/classic/statics/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="/themes/classic/statics/libs/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  <script src="/themes/classic/statics/common/init.js"></script>
</head>
<body>
  <link rel="stylesheet" href="/themes/classic/statics/app/login/css/style.css"/>
  <div class="container">

    <form class="form-signin" method="post">
      <h2 class="form-signin-heading">请登录</h2>
      <?php if(isset($message)):?>
      <div class="alert alert-danger"><?php echo $message;?></div>
      <?php endif;?>
      <label for="account" class="sr-only">帐号</label>
      <input type="text" name="username" id="account" class="form-control" placeholder="帐号" required autofocus>
      <label for="password" class="sr-only">密码</label>
      <input type="password" name="password" id="password" class="form-control" placeholder="密码" required>
      <button class="btn btn-lg btn-success btn-block" type="submit">登录</button>
    </form>

  </div>
</body>
</html>
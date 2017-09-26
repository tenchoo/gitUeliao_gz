<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit|ie-stand|ie-comp">
    <title><?php echo $this->getPageTitle();?></title>
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
<?php if( Yii::app()->session['alertSuccess'] ){ unset(Yii::app()->session['alertSuccess']); ?>
    <div class="alert alert-success alert-dismissible fade in do-success" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
        <strong>操作成功！</strong>
    </div>
    <script>setTimeout(function(){
            $('.do-success').find('.close').click();
        },3500);</script>
<?php } ?>

<div class="clearfix head">
    <h1 class="pull-left"><a href="javascript:"><img src="/themes/classic/statics/app/home/image/logo.png" width="184" height="52" alt=""/></a></h1>
    <div class="pull-right admin-info">
        <?php if( Yii::app()->user->getIsGuest() === false ){?>
            <?php echo '欢迎，'.CHtml::link( Yii::app()->user->getState('username'));?>
            <a href="javascript:">修改密码</a>
            <?php echo CHtml::link( '退出登录', $this->createUrl('/sign/logout') );?>
        <?php }?>
    </div>
</div>
<ul class="clearfix head-nav list-inline list-unstyled">
    <?php $this->widget('application.widgets.navigate');?>
</ul>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 side-menu">
            <div class="panel-group">
                <?php $this->widget('application.widgets.menuItems',array('index'=>$this->index));?>
            </div>
        </div>
        <div class="col-md-10 content-wrap">
            <?php $this->widget('application.widgets.current');?>
            <?php echo $content;?>
        </div>
    </div>
</div>
<div class="foot">
    <p>版权所有：深圳市指易达电子商务有限公司 2015-2020</p>
</div>
</body>
</html>
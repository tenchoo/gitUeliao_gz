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
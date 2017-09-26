<div class="clearfix head">
    <h1 class="pull-left"><a href="javascript:"><img src="/themes/classic/statics/app/home/image/logo.png" width="184" height="52" alt=""/></a></h1>
    <div class="pull-right admin-info">
        <?php if( Yii::app()->user->getIsGuest() === false ){?>
            <?php echo '欢迎，'.CHtml::link( Yii::app()->user->getState('username'), $this->createUrl('rightbar'),['target'=>'frameContent','title'=>'访问我的仪表盘']);?>
            <?php echo CHtml::link('修改密码',$this->createUrl('changepassword'),['target'=>'frameContent']); ?>
            <?php echo CHtml::link('设置打印机',$this->createUrl('printer'),['target'=>'frameContent']); ?>
            <?php echo CHtml::link('退出登录', $this->createUrl('/sign/logout'),['target'=>'_parent']);?>
        <?php }?>
    </div>
</div>
<ul class="clearfix head-nav list-inline list-unstyled">
    <?php
    if(Yii::app()->user->getState('isAdmin')==1) {
        foreach($navigate as $item){
        echo '<li>'.CHtml::link( $item->title, $this->createUrl('leftbar',['id'=>$item->id]), ['target'=>'menubar'] ).'</li>';
       }
    }
    ?>
</ul>
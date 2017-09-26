<?php if( Yii::app()->user->hasFlash('success') ){?>
    <div class="form-group clearfix">
    <div class="col-md-offset-2 col-md-6 alert alert-success alert-dismissible fade in" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
  <div class="">数据提交成功!</div>
  </div>
  </div>
<script type="text/javascript">
<!--
$(function(){
	setTimeout(function(){window.location="<?php echo Yii::app()->user->getFlash('success');?>";},1500);
});
//-->
</script>
<?php } ?>

<?php if( Yii::app()->session->get('alertSuccess') ){ Yii::app()->session->remove('alertSuccess'); ?>
    <div class="alert alert-success alert-dismissible fade in do-success" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
        <strong>操作成功！</strong>
    </div>
    <script>setTimeout(function(){
            $('.do-success').find('.close').click();
        },3500);</script>
<?php } ?>
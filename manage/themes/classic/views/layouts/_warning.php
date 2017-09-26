<?php if( Yii::app()->user->hasFlash('warning') ){?>
    <div class="form-group clearfix">
    <div class="col-md-offset-2 col-md-6 alert alert-warning alert-dismissible fade in" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
    
  <div class="">
	<?php echo Yii::app()->getUser()->getFlash('warning'); ?>
  </div>
  </div>
  </div>
  <?php } ?>
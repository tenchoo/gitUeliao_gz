  <!-- 非浮动层报错提示 -->
    <?php if( $error =$this->getError()){?>
    <div class="form-group clearfix">
    <div class="col-md-offset-2 col-md-6 alert alert-danger alert-dismissible fade in" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
    
  <div class="">
	<?php if(is_array( $error )){
		foreach( $error as $val ) {
		echo $val['0'].'<br/>';
	}}else{
		echo $error.'<br/>';
	}?>
  </div>
  </div>
  </div>

  <?php } ?>
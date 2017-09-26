<!-- 浮动层报错提示 -->
<?php if( $error =$this->getError()){
	if(is_array( $error )){
		$error = current( $error );
	}		
?> 
	<div class="alert alert-danger alert-dismissible fade in  do-success" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
        <strong><?php echo $error;?></strong>
	</div>
	<script>setTimeout(function(){
            $('.do-success').find('.close').click();
        },4500);</script>
<?php } ?>
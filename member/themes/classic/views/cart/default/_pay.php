<?php if( is_array( $payModels ) && !empty( $payModels )){ ?>
  <div class="order-payModel">
  <div class="c-hd">支付方式</div>
  <div class="pay-way">
      <?php foreach ( $payModels as $key=>$payval ){ ?>
		  <label class="radio-inline">
  			<input class="radio" name="payModel" value="<?php echo $payval['paymentId']?>" type="radio" <?php if($key=='0'){ echo 'checked';}?>><span><?php echo $payval['paymentTitle']?></span>
  		 </label>
        <?php }?>
	 </div>
</div>
<?php }?>
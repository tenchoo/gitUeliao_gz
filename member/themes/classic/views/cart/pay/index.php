<link rel="stylesheet" href="/app/cart/css/style.css"/>
 <br><br>
   <div class="container">
    <div class="cart-step">
      <ul class="list-unstyled">
        <li class="first done done-b"><span class="one">1.查看购物车</span></li>
        <li class="done-a"><span class="two">2.确认订单信息</span></li>
        <li class="cur"><span class="three">3.付款</span></li>
        <li><span class="four">4.确认收货</span></li>
        <li class="last"><span class="five">5.评价</span></li>
      </ul>
    </div>
    <div class="payment">
      <div class="order-detail">
        <div class="price pull-right">
          <b class="text-warning"><?php echo number_format($totalPrice ,2);?></b> 若有改价，请<a class="text-link" href="javascript:void(location.reload());" title="刷新">刷新</a>
        </div>
        <div class="title pull-left">
		 <?php echo $payTitle;?>
          <?php //echo $model['0']->products['0']->title;?><a class="text-link item" href="javascript:" title="订单详情">订单详情</a>
          <div class="detail hide">
            <i></i>
			  <?php foreach ( $model as $val ) {?>
			<ul class="list list-unstyled">
              <li><span class="tit">商品名称：</span><span><?php echo $val->products['0']->title;?>等</span></li>
              <li><span class="tit">交易金额：</span><span><?php echo Order::priceFormat( $val->realPayment );?>元</span></li>
              <li><span class="tit">购买时间：</span><span><?php echo $val->createTime;?></span></li>
              <li><span class="tit">收货地址：</span><span><?php echo $val->address;?>，( <?php echo $val->name;?> 收 ) <?php echo $val->tel;?></span></li>
              <li><span class="tit">交易号：</span><span><?php echo $val->orderId;?></span></li>
            </ul>
			<?php } ?>
          </div>
        </div>
      </div>
      <div class="form-horizontal">
        <form action="" method="post">
          <ul class="balance list-unstyled">
			<?php foreach ($payModels as $key=>$payval ){ ?>
			 <li <?php if( $key =='4' ) { ?>class="express"<?php };?><?php if( $key =='5' ) { ?>class="online"<?php };?>>
				<div class="hd">
					<label class="radio-inline">
					<?php if($key!='5'){ ?>
						<input type="radio" name="Pay[bank]" value="<?php echo $payval['paymentId'];?>" />
					<?php }?>
						<?php echo $payval['paymentTitle'];?>
					</label>
					<?php if( $key =='4' ) { ?>
					<span class="express-compnay">
					  <span class="text-red">*</span> 请选择物流公司：
					  <?php foreach ( $logistics as $cokey=>$coval ){ ?>
					   <label class="radio-inline">
							<input type="radio" name="Pay[logistics]" value="<?php echo $cokey;?>" disabled><?php echo $coval;?>
						</label>
					   <?php  } ?>
					</span>
					<?php  } ?>
				</div>
		<?php switch( $key ){
				case '2':	?>
				<div class="bd">
                付款凭证：<input type="text" class="form-control input-xs image-url"  readonly="readonly">
                <span class="uploader uploader-button">
                  <button type="button" class="btn btn-cancel btn-xs">上传</button>
                </span>
              </div>
		<?php 		break;
				case '3':	?>
				<div class="bd">
                <?php if( isset ( $payval['methods'] )){ ?>
                <ul class="list-unstyled">
				<?php	foreach ( $payval['methods'] as $method ){?>
					 <li>
						<span>开户行：<?php echo $method['paymentTitle'];?></span>
						<span>收款人：<?php echo isset($method['paymentSet']['payment_user'])?$method['paymentSet']['payment_user']:'';?>
						</span>收款帐号：<?php echo isset($method['paymentSet']['payment_id'])?$method['paymentSet']['payment_id']:'';?></li>
				<?php } ?>
				 </ul>
				<?php }?>
                付款凭证：<input type="text" class="form-control input-xs image-url"  readonly="readonly">
                <span class="uploader uploader-button">
                  <button type="button" class="btn btn-cancel btn-xs">上传</button>
                </span>
              </div>
		<?php // break;
			//	case '4':	?>
				<!--div class="bd">
                付款凭证：<input type="text" class="form-control input-xs image-url" name="Pay[paymentVoucher]">
                <span class="uploader uploader-button">
                  <button type="button" class="btn btn-cancel btn-xs">上传</button>
                </span>
              </div-->
		<?php 		break;
				case '5':	?>
				<?php if( isset ( $payval['methods'] )){ ?>
				<div class="pay-method bd">
                <ul class="clearfix list-unstyled">
				<?php	foreach ( $payval['methods'] as $method ){
					if( $method['paymentId'] =='7'){ $paybank = '1';}else{
				?>
                  <li>
                    <label class="radio-inline">
    				  <input type="radio" name="Pay[bank]" value="<?php echo $method['paymentId'];?>" class="radio-inline"/>
                      <img src="<?php $this->imageUrl($method['logo']);?>" alt="<?php echo $method['paymentTitle'];?>" width="140" height="40">
                    </label>
                  </li>
					<?php  }} ?>
				</ul>
				<?php if(isset($paybank) && $paybank=='1'){ ?>
				<ul class="clearfix list-unstyled">
				 <li><label class="radio-inline"><input name="Pay[bank]" value="ICBCB2C" class="radio-inline" id="ICBCB2C" type="radio"><img src="<?php echo $this->res(false)?>/app/cart/image/paylogo/icbcb2c.jpg" alt="" width="140" height="40"></label></li>
          <li><label class="radio-inline"><input name="Pay[bank]" value="CCB" class="radio-inline" id="CCB" type="radio"><img src="<?php echo $this->res(false)?>/app/cart/image/paylogo/ccb.jpg" alt="" width="140" height="40"></label></li>
          <li><label class="radio-inline"><input name="Pay[bank]" value="CMB" class="radio-inline" id="CMB" type="radio"><img src="<?php echo $this->res(false)?>/app/cart/image/paylogo/cmb.jpg" alt="" width="140" height="40"></label></li>
          <li><label class="radio-inline"><input name="Pay[bank]" value="COMM" class="radio-inline" id="COMM" type="radio"><img src="<?php echo $this->res(false)?>/app/cart/image/paylogo/comm.jpg" alt="" width="140" height="40"></label></li>
          <li><label class="radio-inline"><input name="Pay[bank]" value="ABC" class="radio-inline" id="ABC" type="radio"><img src="<?php echo $this->res(false)?>/app/cart/image/paylogo/abc.jpg" alt="" width="140" height="40"></label></li>
          <li><label class="radio-inline"><input name="Pay[bank]" value="GDB" class="radio-inline" id="GDB" type="radio"><img src="<?php echo $this->res(false)?>/app/cart/image/paylogo/gdb.jpg" alt="" width="140" height="40"></label></li>
          <li><label class="radio-inline"><input name="Pay[bank]" value="CIB" class="radio-inline" id="CIB" type="radio"><img src="<?php echo $this->res(false)?>/app/cart/image/paylogo/cib.jpg" alt="" width="140" height="40"></label></li>
          <li><label class="radio-inline"><input name="Pay[bank]" value="CEBBANK" class="radio-inline" id="CEBBANK" type="radio"><img src="<?php echo $this->res(false)?>/app/cart/image/paylogo/cebbank.jpg" alt="" width="140" height="40"></label></li>
          <li><label class="radio-inline"><input name="Pay[bank]" value="CITIC" class="radio-inline" id="CITIC" type="radio"><img src="<?php echo $this->res(false)?>/app/cart/image/paylogo/citic.jpg" alt="" width="140" height="40"></label></li>
          <li><label class="radio-inline"><input name="Pay[bank]" value="SPDB" class="radio-inline" id="SPDB" type="radio"><img src="<?php echo $this->res(false)?>/app/cart/image/paylogo/spdb.jpg" alt="" width="140" height="40"></label></li>
          <li><label class="radio-inline"><input name="Pay[bank]" value="BOCB2C" class="radio-inline" id="BOCB2C" type="radio"><img src="<?php echo $this->res(false)?>/app/cart/image/paylogo/bocb2c.jpg" alt="" width="140" height="40"></label></li>
          <li><label class="radio-inline"><input name="Pay[bank]" value="SDB" class="radio-inline" id="SDB" type="radio"><img src="<?php echo $this->res(false)?>/app/cart/image/paylogo/sdb.jpg" alt="" width="140" height="40"></label></li>
            </ul>
			<?php  } ?>
				</div>
				<?php  } ?>
		<?php 		break;

			 }
		?>
			</li>
			<?php } ?>
          </ul>
          <div class="pay-password text-center">
            <button class="btn btn-warning" type="submit">确认付款</button>
          </div>
          <input type="hidden" name="Pay[paymentVoucher]">
        </form>
      </div>
    </div>
  </div>

<script>
<?php if( $error =$this->getError()){ ?>
  seajs.use('modules/dialog/js/dialog.js',function(dialog){
    dialog.alert('<?PHP echo $error; ?>',{type:'error'});
  });
<?PHP } ?>
 seajs.use('app/cart/js/payment.js');
</script>
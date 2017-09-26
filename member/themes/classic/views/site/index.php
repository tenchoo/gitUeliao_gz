<link rel="stylesheet" href="/app/member/home/css/style.css"/>
<div class="pull-right frame-content">
	   		<div class="user">
	   			<div class="pull-left user-pic">
				<?php if(empty($info['icon'])){?>
				<img src="<?php echo $this->res();?>/app/home/image/face.png" width="60" height="60"  alt=""/>
				<?php }else{ ?>
				<img src="<?php $this->imageUrl($info['icon'],100);?>" width="60" height="60"  alt=""/>
				<?php }?>
					
				</div>
				<strong><?php echo $info['nickName']?></strong>
				<!-- <img src="/common/placeholder.png" width="40" height="15"  alt=""/> -->
	   		</div>
	   		<!--div class="pay">
	   			<ul class="list-unstyled">
	   				<li><span>付款 </span><strong>0</strong></li>
	   				<li><span>待确认收货 </span><strong>0</strong></li>
	   				<li class="text-warning"><span>待评价 </span><strong>2</strong></li>
	   				<li><span>退款 </span><strong>0</strong></li>
	   			</ul>
	   		</div-->
	   		<div class="hst">
	   			<strong>浏览过的商品</strong>
	   		</div>
	   		<div class="commodity">
		   		<ul class="list-unstyled text-center">
				<?php $this->widget('ViewProductList');?>
		   		</ul>
	   		</div>
	    </div>
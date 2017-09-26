<link rel="stylesheet" href="/modules/button/css/style.css">
<link rel="stylesheet" href="/modules/icon/css/style.css">
<link rel="stylesheet" href="/app/product/detail/css/style.css">
  <div class="container-wrap">
    <div class="container">
      <div class="pull-left preview-wrap">
        <div class="preview">
		 <span class="<?php echo $product['saleType'];?>"></span>
          <img src="<?php $this->imageUrl($product['mainPic'],600);?>" width="320" height="320" alt="">
        </div>
        <ul class="thumb clearfix list-unstyled">
          <?php foreach( $product['pictures'] as $key=> $val ){ ?>
          <li data-src="<?php $this->imageUrl($val,600);?>" <?php if ($key==0){?> class="active"<?php }?>><img src="<?php $this->imageUrl($val,50);?>" width="40" height="40" alt=""></li>
          <?php } ?>
        </ul>
        <div class="clearfix share-fav">
          <div class="pull-left share">
            <div class="pull-left share-txt">分享到：</div>
            <div class="bdsharebuttonbox"><a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信"></a><a href="#" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a><a href="#" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a><a href="#" class="bds_more" data-cmd="more">更多</a></div>
          </div>
          <!-- <a class="pull-left fav" href="javascript:"><i class="ico-fav pull-left"></i>收藏该商品</a> -->
        </div>
      </div>
	  <?php if( $product['state'] =='selling' && !empty( $product['specStock'] ) ) { ?>
      <div class="pull-left product-info">
        <form action="<?php echo $this->memberUrl.'/cart/default/tailbuynow';?>" method="post"
			  data-action="<?php echo $this->memberUrl.'/cart/default/tailbuynow';?>"
			  data-cart="<?php echo $this->memberUrl.'/cart/default/addtail';?>" >
          <h1><?php echo '【 '.$product['serialNumber'].' 】 '.$product['title']?></h1>
		<?php $this->beginContent('_'.$product['saleType'],array('product' => $product,'colorgroups'=>$colorgroups ));$this->endContent();?>
          <div class="form-offset">
            <button type="submit" class="btn btn-warning btn-xl" <?php if(empty($product['specStock'])) { echo 'disabled';}?>>立即购买</button>
            <button type="button" class="btn btn-success add-cart btn-xl" <?php if(empty($product['specStock'])) { echo 'disabled';}?>>加入购物车</button>
          </div>
          <input type="hidden" value="<?php echo $product['productId']?>" name="productId"/>
		  <input type="hidden" value="<?php echo $product['tailId']?>" name="tailId"/>
        </form>
      </div>
	  <?php }else{ ?>
	   <div class="pull-left product-info">
        <form action="" method="post">
          <h1><?php echo $product['title']?></h1>
          <div class="clearfix text-minor prices">
            <div class="pull-left retail">
				<span>此产品已下架或已售完</span>
            </div>
          </div>
		 </div>
	  <?php } ?>
      <div class="pull-right service-wrap">
        <div class="service">
          <span class="pull-left">客服：</span>
          <ul class="list-unstyled">
		   <?php $this->widget('CSList', array()); ?>
          </ul>
        </div>
        <div class="qr">
          <span class="text-minor">手机购买</span>
		  <?php $murl = Yii::app()->params['domain_mobile'].'/#product/detail/id:'.$product['productId'];?>
		   <img src="<?php echo Yii::app()->request->hostInfo.$this->createUrl('/product/prcode',array('data'=>urlencode($murl)))?>" alt="扫描二维码">
        </div>

		<?php if( !empty($product['craftList'])){ ?>
        <div class="more-tech">
          <h3>更多特殊工艺产品</h3>
          <ul class="list-unstyled">
			<?php foreach ( $product['craftList'] as $clist ){ ?>
			<li><a href="<?php echo $this->createUrl('/product/detail',array('id'=>$clist['productId']) );?>"><span></span><?php echo $clist['title'];?></a></li>
			<?php }?>
          </ul>
        </div>
	<?php }?>

      </div>
    </div>
  </div>
  <div class="container">
    <div class="pull-right product-detail">
      <ul class="clearfix text-center list-unstyled detail-tab">
        <li class="active"><a href="javascript:">图文展示</a></li>
        <li><a href="javascript:">产品特性</a></li>
        <li><a href="javascript:">成交<strong class="text-warning"><?php echo $product['dealCount']?></strong></a></li>
        <li><a href="javascript:">客户反馈<strong class="text-warning"><?php echo $product['commentCount']?></strong></a></li>
      </ul>
      <div class="detail-tab-bd">
        <div class="tab-bd-item active">
          <div class="detail-content">
            <?php echo $product['content']?>
          </div>
        </div>
        <div class="tab-bd-item">
          <ul class="clearfix text-muted list-unstyled attr-group">
            <?php foreach( $setgroups as $key=>$setval ){ ?>
            <li>
              <dl>
              <dt><?php echo $setval;?></dt>
              <?php if(isset($product['attr'][$key]) && is_array($product['attr'][$key])){
                foreach ( $product['attr'][$key] as $val){ ?>
              <dd><?php echo $val['title'].'：'.$val['attrValue'];?></dd>
              <?php }}?>
              </dl>
            </li>
            <?php } ?>
          </ul>

          <div class="state"><?php $this->widget('widgets.PieceWidget', array('mark' => 'product_m'));?></div>
    		<?php if(!empty($product['testResults'])){?>
    		   <div class="detail-test">
               <?php echo $product['testResults']?>
              </div>
    		 <?php } ?>
        </div>
        <div class="tab-bd-item" data-rel="<?php echo $this->createUrl('deals',array('id'=>$product['tailId'],'unitName'=>$product['unitName']))?>">
        </div>
        <div class="tab-bd-item" data-rel="<?php echo $this->createUrl('comment',array('id'=>$product['tailId'],'unitName'=>$product['unitName']))?>">
        </div>
      </div>
    </div>
    <div class="pull-left product-recommend">
      <h2>相似产品推荐</h2>
      <ul class="list-unstyled">
        <?php
        $this->widget('SameProductList', array(
			'productId'=>$product['productId'],
            )
        );
        ?>
      </ul>
    </div>
  </div>
  <script type="text/html" id="count">
  <span class="num">{{count}}</span><?php echo $product['unitName']?>
  <span class="split">|</span>
  <span class="num">{{price}}</span>元
  </script>
  <script type="text/html" id="selected">
  {{each list}}
  <li data-rel="{{$value.rel}}">
    <div class="info">
      <span class="c" style="background:{{$value.code}}">{{$value.img}}</span>
      <div class="t">{{$value.title}} {{$value.code}}</div>
    </div>
    <div class="stock">{{$value.num}}<?php echo $product['unitName']?></div>
    <input type="hidden" value="{{$value.num}}" name="cart[{{$value.stockid}}]"/>
  </li>
  {{/each}}
  </script>
  <script>seajs.use('app/product/detail/js/detail.js');</script>
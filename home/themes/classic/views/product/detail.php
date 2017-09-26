<link rel="stylesheet" href="/modules/button/css/style.css">
<link rel="stylesheet" href="/modules/icon/css/style.css">
<link rel="stylesheet" href="/app/product/detail/css/style.css">
  <div class="container-wrap">
    <div class="container">
      <div class="pull-left preview-wrap">
        <div class="preview">
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
          <a class="pull-left fav" href="javascript:"><i class="ico-fav pull-left"></i>收藏该商品</a>
        </div>
      </div>
	  <?php if( $product['state'] =='0') { ?>
      <div class="pull-left product-info">
        <form action="<?php echo $this->memberUrl.'/cart/default/buynow';?>" method="post"
			  data-action="<?php echo $this->memberUrl.'/cart/default/buynow';?>"
			  data-cart="<?php echo $this->memberUrl.'/cart/default/add';?>" >
          <h1><?php echo '【 '.$product['serialNumber'].' 】 '.$product['title']?></h1>
          <div class="clearfix text-minor prices">
            <div class="pull-left retail">
              <span>零售价</span>
              <strong class="price" data-price="<?php echo $product['price']*100?>"><?php echo $product['price']?></strong>元/<?php echo $product['unitName']?>
            </div>
            <div class="pull-right batch">
              <span>大货价</span>
              <strong class="price"><?php echo $product['tradePrice']?></strong>元/<?php echo $product['unitName']?>
            </div>
          </div>
          <div class="form-group">
            <span class="pull-left text-minor">累计成交</span>
            <div class="form-offset transaction-count">
              <strong class="text-warning"><?php echo ($product['dealCount']>=100)?'≥100':$product['dealCount']?>
			  </strong><span><?php echo $product['unitName']?>成交</span>
              <strong class="text-warning"><?php echo $product['commentCount']?></strong>条客户反馈
            </div>
          </div>
		  <?php if( isset($product['crafts']) ){ ?>
		   <div class="form-group tech">
            <span class="pull-left text-minor">特殊工艺</span>
            <div class="form-offset">
			<?php foreach ($product['crafts'] as $craft ){ ?>
			<?php if(!empty($craft['icon'])){ ?>
			 <img src="<?php echo $this->res(false),$craft['icon'];?>"  alt="<?php echo $craft['title']?>"  width="20" height="20"/>
			<?php }else{ ?>
			<span> <?php echo $craft['title']?></span>
			<?php }}?>
            </div>
          </div>
		  <?php }?>

          <div class="form-group color">
            <span class="pull-left text-minor">颜色</span>
            <div class="form-offset">
              <input type="text" class="serial" placeholder="输入颜色编号">
              <button type="button" class="search"><i></i></button>
            </div>
          </div>
          <div class="form-offset colors less">
            <ul class="clearfix list-unstyled group">
              <?php foreach( $colorgroups as $key=>$setval ){ ?>
              <li data-group="<?php echo $key;?>"><?php echo $setval;?></li>
              <?php } ?>
            </ul>
            <ul class="list list-unstyled">
              <?php if(is_array($product['specStock'])){
					foreach($product['specStock'] as $pval){
               ?>
              <li data-stockid="<?php echo $pval['stockId'];?>"
				  data-rel="<?php echo $pval['relation'];?>"
				  data-group="<?php echo $pval['colorSeriesId'];?>"
				  data-title="<?php echo $pval['title'];?>"
				  data-code="#<?php echo $pval['code'];?>"
				  data-img="<?php if(!empty( $pval['picture'] )) { ?>
					<?php echo Yii::app()->params['domain_images'],$pval['picture'];?>
					<?php } ?>">
                <div class="info">
                  <span class="c" style="background:#<?php echo $pval['code'];?>"><?php if(!empty( $pval['picture'] )) { ?>
                  <img src="<?php echo Yii::app()->params['domain_images'],$pval['picture'];?>" alt="" width="28" height="28">
                <?php } ?></span>
                  <div class="t"><?php echo $pval['title'].' '.$pval['serialNumber'];?></div>
                </div>
                <div class="stock">
				<?php
				//若业务员登录就正常显示
				$userType =  Yii::app()->user->getState('usertype');
				if( $pval['total']>=300 && $userType != tbMember::UTYPE_SALEMAN  ) {
						$pval['total'] = '≥300';
				}
				echo $pval['total'].$product['unitName'] ;
				?>
				可售</div>
                <div class="control">
                  <button type="button" disabled class="minus dis"><i class="icon icon-cart icon-minus"></i></button>
				  <input type="text" value="0" min="0" max="99999999" maxlength="8" class="int-only" />
				  <button type="button" class="plus"><i class="icon icon-cart icon-plus"></i></button>
                </div>
              </li>
              <?php  } }?>
            </ul>
			<?php if( count($product['specStock'])>5) {?>
            <div class="clearfix"><a href="javascript:" class="more"><i></i></a></div>
			 <?php  }?>
            <ul class="clearfix hide list selected list-unstyled">
            </ul>
          </div>
          <div class="clearfix form-offset hide text-warning selected">
            <div class="pull-right list text-center">
              <a href="javascript:">已选清单<i class="arr"></i></a>
            </div>
            <div class="count">
              <span class="num">0</span><?php echo $product['unitName']?>
              <span class="split">|</span>
              <span class="num">0.00</span>元
            </div>
          </div>
          <div class="form-offset">
            <button type="submit" class="btn btn-warning btn-xl">立即购买</button>
            <button type="button" class="btn btn-success add-cart btn-xl">加入购物车</button>
          </div>
          <input type="hidden" value="<?php echo $product['productId']?>" name="productId"/>
        </form>
      </div>
	  <?php }else{ ?>
	   <div class="pull-left product-info">
        <form action="" method="post">
          <h1><?php echo $product['title']?></h1>
          <div class="clearfix text-minor prices">
            <div class="pull-left retail">
				<span>此产品已下架</span>
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
		   <img src="<?php echo Yii::app()->request->hostInfo.$this->createUrl('prcode',array('data'=>urlencode($murl)))?>" alt="扫描二维码">
        </div>

		<?php if( !empty($product['craftList'])){ ?>
        <div class="more-tech">
          <h3>更多特殊工艺产品</h3>
          <ul class="list-unstyled">
			<?php foreach ( $product['craftList'] as $clist ){ ?>
			<li><a href="<?php echo $this->createUrl('detail',array('id'=>$clist['productId']) );?>"><span></span><?php echo $clist['title'];?></a></li>
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
        <!--li><a href="javascript:">成交<strong class="text-warning"><?php //echo $product['dealCount']?></strong></a></li-->
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
        <!-- <div class="tab-bd-item" data-rel="<?php echo $this->createUrl('deals',array('id'=>$product['productId'],'unitName'=>$product['unitName']))?>">
        </div> -->
        <div class="tab-bd-item" data-rel="<?php echo $this->createUrl('comment',array('id'=>$product['productId'],'unitName'=>$product['unitName']))?>">
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
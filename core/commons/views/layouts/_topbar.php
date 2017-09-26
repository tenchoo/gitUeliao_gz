<?php
$cartTotal = tbCart::model()->getTotalNum( Yii::app()->user->id,true );
?>

  <div class="top-bar">
    <div class="container">
	  <?php if(!Yii::app()->user->isGuest){?>
	  <div class="pull-left lunar-wel">
        <div class="pull-left top-wel"><span>您好</span><span><a href="<?php echo $this->memberUrl;?>" class="text-warning"><?php echo Yii::app()->user->getstate('nickName')?></a></span><span>，欢迎来到指易达商城！</span></div>
        <div class="pull-left top-sign">
          <a href="<?php echo $this->memberUrl.'/user/logout.html';?>" class="text-warning">退出登录</a>
        </div>
      </div>
	  <?php }else{?>
      <div class="pull-left lunar-wel">
        <div class="pull-left top-wel">您好，欢迎来到优易料商城！</div>
        <div class="pull-left top-sign">
          <a href="<?php echo $this->memberUrl.'/login.html';?>">登录</a>
          <a href="<?php echo $this->memberUrl.'/user/reg.html';?>">免费注册</a>
        </div>
      </div>
	  <?php }?>
      <div class="pull-right sign-nav">
        <div class="pull-left top-home"><a href="<?php echo $this->homeUrl;?>">商城首页</a></div>
        <div class="pull-left top-box top-my-account" data-hover="top-box-hover">
          <div class="top-hd">
            <a href="<?php echo $this->memberUrl;?>">我的账户<span class="arr"></span></a>
          </div>
          <div class="top-bd">
            <ul class="list-unstyled">
              <li><a href="<?php echo $this->memberUrl.'/order';?>">已买到货品</a></li>
              <li><a href="<?php echo $this->memberUrl.'/order/default/index.html?type=6';?>">待付款货品</a></li>
              <li><a href="<?php echo $this->memberUrl.'/message.html';?>">系统消息</a></li>
            </ul>
          </div>
        </div>
        <div class="pull-left top-box top-cart" data-hover="top-box-hover top-cart-hover" id="J_cart"
		data-count="<?php echo $cartTotal;?>">
          <div class="top-hd">
            <a href="<?php echo $this->memberUrl.'/cart.html';?>">购物车<span class="count">
			<?php echo $cartTotal;?>
			</span><span class="arr"></span></a>
          </div>
          <div class="top-cart-bd">
              <?php //$this->widget('widgets.cartTopbar');?>
          </div>
        </div>
        <script type="text/html" id="cart-list">
          <ul class="top-cart-list list-unstyled">
            {{each detail}}<li class="clearfix">
              <a href="<?php echo $this->homeUrl;?>/product/detail-{{$value.productId}}.html" class="pull-left"><img data-src="{{$value.mainPic}}" alt="{{$value.title}}" width="50" height="50"/></a>
              <div class="pull-right">
                <span class="price">×{{$value.num}}</span>
                <a href="<?php echo $this->memberUrl;?>/cart/default/delete.html?cartId={{$value.cartId}}" class="del">删除</a>
              </div>
              <a href="<?php echo $this->homeUrl;?>/product/detail-{{$value.productId}}.html" class="t" title="{{$value.title}}">{{$value.title}}</a>
              <span class="text-muted">{{$value.relation}}</span>
              <span class="pull-left text-warning">￥{{$value.price}}</span>
            </li>{{/each}}
          </ul>
          <div class="clearfix top-cart-count">
            <button data-href="<?php echo $this->memberUrl.'/cart.html';?>" type="button" class="top-cart-btn pull-right">去结算</button>
            共计：<span class="price">￥{{totalPrice}}</span>
          </div>
        </script>
        <script>seajs.use('modules/topbar/js/cart.js');</script>
        <div class="pull-left top-box top-serivce" data-hover="top-box-hover">
          <div class="top-hd">
            <a href="<?php echo $this->homeUrl.'/help';?>">客户服务<span class="arr"></span></a>
          </div>
          <div class="top-bd">
            <ul class="list-unstyled">
              <li><a href="javascript:">买家帮助</a></li>
              <li><a href="javascript:">卖家帮助</a></li>
              <li><a href="javascript:">商城规则</a></li>
              <li><a href="javascript:">客服中心</a></li>
            </ul>
          </div>
        </div>
        <div class="pull-left top-box top-nav" data-hover="top-box-hover">
          <div class="top-hd">
            <a href="javascript:">网站导航<span class="arr"></span></a>
          </div>
          <div class="clearfix top-bd">
            <ul class="list-unstyled">
              <li><a href="<?php echo $this->homeUrl;?>">首页</a></li>
              <li><a href="<?php echo $this->homeUrl.'/help';?>">帮助中心</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

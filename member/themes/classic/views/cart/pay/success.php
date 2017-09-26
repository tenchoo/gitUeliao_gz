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
    <br><br>
    <div class="bd evaluation">
      <div class="form-group-offset">
        <div class="success-message">
          <h2 class="success-message-title">
            <i class="icon icon-xl icon-success"></i>恭喜您,<?php echo $title;?>成功！
          </h2>
          <p class="success-message-link">现在去：
		  <a href="<?php echo $this->createUrl('/order/');?>" class="text-link">查看订单</a>
		  <a href="<?php echo $this->createUrl('/cart/');?>" class="text-link">返回购物车</a>
		  <a href="<?php $api = new ApiClient('www','',false);echo $api->getDomain();?>" class="text-link">回到首页</a></p>
        </div>
      </div>
    </div>
  </div>
  <br>
  <br>
  <br>
  <br>
<link rel="stylesheet" href="<?php echo $this->res()?>/app/cart/css/style.css"/>
 <div class="container">
    <div class="cart-step">
      <ul class="list-unstyled">
        <li class="first cur"><span class="one">1.查看购物车</span></li>
        <li><span class="two">2.确认订单信息</span></li>
        <li><span class="three">3.付款</span></li>
        <li><span class="four">4.确认收货</span></li>
        <li class="last"><span class="five">5.评价</span></li>
      </ul>
    </div>
    <br>
    <div class="frame-list-bd">
      <form action="/cart/default/confirm" method="post">
      <table class="cart-index">
        <?php if( !empty($list) ){?>
        <thead>
          <tr>
            <th class="title">商品信息</th>
            <th width="220">单价（元）</th>
            <th width="100">数量</th>
            <th width="190">金额（元）</th>
            <th width="100">操作</th>
          </tr>
        </thead>
		  <tbody class="list-page-body">
        <?php foreach ( $list as $val ){?>
          <tr class="list-body-bd" data-cartid="<?php echo $val['cartId']?>" data-price="<?php echo $val['price']*100?>" data-total="<?php echo $val['num']*$val['price']*100;?>">
            <td class="title">
              <div class="c-img product-img pull-left">
        			  <a href="<?php echo $val['url'];?>" target="_blank">
        				<img src="<?php echo $this->imageUrl($val['mainPic'],50);?>" width="50" height="50" alt="<?php echo $val['title'];?>" />
        				</a>
              </div>
              <div class="product-title">
        				<a href="<?php echo $val['url'];?>" target="_blank">
        					<?php echo '【'.$val['serialNumber'].'】'.$val['title'];?>
        				</a>
              </div>
              <p class="text-minor"><?php echo  $val['relation'];?></p>
            </td>
            <td class="price"><?php echo $val['price']?></td>
			<?PHP if( $val['state']!='0' ) { ?>
				<td colspan="2">产品已失效</td>
			<?php }else{ ?>
            <td class="num">
			<?php if( array_key_exists('saleType',$val) &&  $val['saleType'] == 'whole' ){?>
				<?php echo $val['num'];?>
			<?php }else { ?>
              <button type="button"<?php if($val['num'] <= 1){?> disabled class="minus dis"<?php }else{?> class="minus" <?php }?>><i class="icon icon-cart icon-minus"></i></button>
			  <input type="text" class="form-control int-only input-xs" value="<?php echo $val['num'];?>" min="1" max="99999999" maxlength="8">
			   <button type="button" class="plus"><i class="icon icon-cart icon-plus"></i></button>
			 <?php }?>
			</td>
            <td class="text-warning total"><?php echo Order::priceFormat( $val['totalPrice'] );?></td>
			<?php } ?>
            <td><?php echo CHtml::link('删除',array('/cart/default/delete','cartId'=>$val['cartId']))?></td>
          </tr>
          <?php }?>
        </tbody>
		 <?php }else { ?>
         <tbody class="list-page-body">
          <tr class="spacing">
            <td colspan="5" class="text-center">购物车暂无产品!</td>
          </tr>
         </tbody>
        <?php }?>
      </table>
      <?php if( !empty($list) ){?>
      <br>
      <div class="clearfix total-info">
        <div class="pull-right">
          <span class="total-bor totalnum">数量总计：<?php echo Order::priceFormat( $totalItems );?>件</span> <span class="total-price">合计（不含运费）：<b class="totalprice text-warning"><?php echo Order::priceFormat( $totalPrice );?></b> 元
          </span> <button class="btn btn-warning" type="submit">结算</button>
        </div>
      </div>
      <?php }?>
     </form>
    </div>
</div>
  <div class="else-pro container">
    <h3 class="hd">购买过此商品的还购了...</h3>
    <div class="bd pro_list">
      <ul class="list-unstyled">
	  <?php $this->widget('SaleProductList');?>
      </ul>
    </div>
  </div>
  <script>
      seajs.use('app/cart/js/cart.js');
</script>
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
              <strong class="text-warning"><?php echo $product['dealCount']?></strong><span><?php echo $product['unitName']?>成交</span>
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
		  <?php if(empty($product['specStock'])) { ?>
			<div class="form-group color">
            <span class="pull-left text-minor"></span>
            <div class="form-offset">产品已售完</div>
			</div>

		  <?php }else{ ?>
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
              <li data-stockid="<?php echo $pval['singleNumber'];?>"
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
                <div class="stock"><?php echo $pval['total'];?><?php echo $product['unitName']?>可售</div>
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
			<?php }?>
            <ul class="clearfix hide list selected list-unstyled"></ul>
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
		<?php } ?>
  <div class="main">
    <div class="container">
	    <div class="pull-left sidebar">
        <?php $this->widget('application.widgets.CategoryMenu');?>
	      <div class="sub-history">
	        <h2>最近浏览...</h2>
	        <ul class="list-unstyled">
	        <?php foreach ($trace as $item){
				if( $item->saletype !== 0 ){
					$url = $this->createUrl('tailproduct/detail',array('id'=>$item->productId));
				}else{
					$url = $this->createUrl('product/detail',array('id'=>$item->productId));
				}
			?>
	          <li>
	            <div class="c-img"><a href="<?php echo $url;?>"><img src="<?php echo $this->imageUrl($item->picture,100);?>" alt="" width="90" height="90"/></a></div>
	            <a href="<?php echo $url;?>" class="t"><?php echo $item->serial;?></a>
	          </li>
	          <?php }?>
	        </ul>
	      </div>
	      <!-- <div><a href="javascript:"><img src="/app/home/image/product.jpg" alt="" width="230" height="248"/></a></div> -->
	    <br><br>
	    </div>
	    <div class="pull-right column">
	      <div class="attr-filter">
	        <div class="clearfix hd">
	          <div class="pull-left key"><?php if(!is_null($current)) echo $current->title;?></div>
	          <div class="pull-left attr-sel">
	          	<?php foreach ( $selected as $item ){
	          		echo $item;
	          	}?>
	          </div>
	          <div class="pull-right total">共有<strong><?php echo $total;?></strong>件</div>
	        </div>
	        <ul class="bd list-unstyled">
	          <?php foreach( $propertys as $property ){?>
	          <li class="clearfix item hidden">
	            <div class="pull-left attr-name"><?php echo $property['title'];?>：</div>
	            <a href="javascript:" class="pull-right control">更多<span class="arr arr-down"></span></a>
	            <ul class="clearfix list-unstyled">
	              <?php $this->createPropertyUrl( $property ); ?>
	            </ul>
	          </li>
	          <?php } ?>
	        </ul>
	      </div>
	      <div class="term-filter">
	        <div class="pull-left">
	        <?php $this->widget('orderBar');?>
	        </div>
	        <div class="pull-right">
	        <?php $this->widget('application.widgets.PageNav', array(
		        'pages' => $pages,
		        'style' => 'hideNumber',
	        	'prevText' => '<span class="ico ico-prev"></span>上一页',
	        	'nextText' => '下一页<span class="ico ico-next"></span>'
		        )
		    );?>
	        </div>
	      </div>
	      <div class="product-list">
	      	<?php
	      	$showPagers = true;
	      	if(!$products):
	      	$showPagers = false;
	      	?>
	        <div class="text-center empty">
	          <p>没有找到相关产品！</p>
	        </div>
	        <?php endif;?>
		      <ul class="list-unstyled">
		      <?php foreach( $products as $product ):
		      	$productTitle = sprintf("【 %s 】%s",$product->serial,$product->title);
				if( $product->saletype !== 0 ){
					$url = $this->createUrl('tailproduct/detail',array('id'=>$product->productid));
					$tag = ( $product->saletype == 1 )?'retail':'whole';
					$tag = '<span class="'.$tag.'"></span>';
				}else{
					$url = $this->createUrl('product/detail',array('id'=>$product->productid));
					$tag = '';
				}
		      ?>
		        <li>
		          <div class="c-img"><a href="<?php echo $url;?>" target="_blank" title="<?php echo CHtml::encode($productTitle);?>"><img src="<?php echo $product->picture;?>" alt="<?php echo CHtml::encode($productTitle);?>" width="200" height="200"/></a>
					<?php echo $tag;?>
				  </div>
		          <a href="<?php echo $url;?>" class="t" target="_blank" title="<?php echo CHtml::encode($productTitle);?>"><?php echo CHtml::encode($productTitle);?></a>
		          <span class="price">&yen;<?php echo $product->price;?></span>
		        </li>
		      <?php endforeach;?>
		      </ul>
	      </div>

	      <?php if($showPagers):?>
	      <div class="text-center page">
	      <?php $this->widget('application.widgets.PageNav', array(
		        'pages' => $pages,
		        'maxLinkCount' => 10, //显示分页数量
		        )
		    );?>
	      </div>
	      <?php endif;?>
	    </div>
    </div>
  </div>
  <script>
    seajs.use('app/home/js/cate.js');
  </script>
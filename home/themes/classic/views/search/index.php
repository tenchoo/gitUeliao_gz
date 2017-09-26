<div class="main">
    <div class="container">
        <div class="pull-left sidebar">
            <?php $this->widget('application.widgets.CategoryMenu');?>
            <br><br>
        </div>
        <div class="pull-right column">
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
                        $productTitle = sprintf("【 %s 】%s",$product['serialNumber'],$product['title']);
                        ?>
                        <li>
                            <div class="c-img"><a href="<?php echo $this->createUrl('product/detail',array('id'=>$product['productId']))?>" target="_blank" title="<?php echo CHtml::encode($productTitle);?>"><img src="<?php $this->imageUrl($product['mainPic'],200);?>" alt="<?php echo CHtml::encode($productTitle);?>" width="200" height="200"/></a></div>
                            <a href="<?php echo $this->createUrl('product/detail',array('id'=>$product['productId']))?>" class="t" target="_blank" title="<?php echo CHtml::encode($productTitle);?>"><?php echo CHtml::encode($productTitle);?></a>
                            <span class="price">&yen;<?php echo $product['price'];?></span>
                        </li>
                    <?php endforeach;?>
                </ul>
            </div>
        </div>
    </div>
</div>
<script>
    seajs.use('app/home/js/home.js');
</script>
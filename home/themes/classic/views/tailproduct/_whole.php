<div class="clearfix text-minor tail-prices">
  <div class="pull-left">
    <span>单价</span>
    <strong data-price="<?php echo $product['price']*100?>"><?php echo $product['price']?></strong>元/<?php echo $product['unitName']?>
    <br>
    <span>数量</span>
    <strong><?php echo number_format($product['totalNum'],1); ?></strong><?php echo $product['unitName']?>
  </div>
  <div class="pull-left total">
    总金额
    <strong class="price"><?php echo number_format($product['totalPrice'],2); ?></strong>元
  </div>
</div>
<div class="form-group">
<span class="pull-left text-minor">促销</span>
<div class="form-offset">整批销售</div>
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
<div class="form-group colors whole">
  <ul class="list-unstyled list">
    <li class="clearfix head">
      <div class="info">颜色</div>
      <!--div class="area">所在区域</div-->
      <div class="stock">数量</div>
    </li>
<?php foreach($product['specStock'] as $pval){ ?>
    <li class="clearfix">
      <div class="info">
        <span style="background:#<?php echo $pval['code'];?>" class="c">
<?php if(!empty( $pval['picture'] )) { ?>
        <img src="<?php echo Yii::app()->params['domain_images'],$pval['picture'];?>" alt="" width="28" height="28">
      <?php } ?>
</span>
        <div class="t"><?php echo $pval['title'].' '.$pval['serialNumber'];?></div>
      </div>
      <!--div class="pull-left area">广州</div-->
      <div class="stock"><?php echo $pval['total'];?><?php echo $product['unitName']?></div>
    </li>
<?php } ?>
  </ul>
<?php if( count($product['specStock'])>5) {?>
  <div class="clearfix arr"><a class="more" href="javascript:"><i></i></a></div>
<?php }?>
</div>
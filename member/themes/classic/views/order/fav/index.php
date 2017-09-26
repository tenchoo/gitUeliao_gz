<link rel="stylesheet" href="/app/member/fav/css/style.css"/>
<div class="pull-right frame-content">
  <div class="frame-tab">
    <ul class="clearfix list-unstyled frame-tab-hd">
      <li class="active">
        <a href="javascript:">商品收藏</a>
      </li>
    </ul>
  </div>
  <div class="clearfix page-wrap">
    <div class="pull-right">
      <?php $this->widget('widgets.ZPagerNavigate', array('pages' => $pages,'type' => "mini",));?>
    </div>
    <label class="checkbox-inline">
      <input type="checkbox" name="" value="">全选
    </label>
    <button type="button" class="btn btn-xs btn-cancel batch-del">批量删除</button>
  </div>

  <ul class="clearfix list-unstyled list">
   <?php foreach($list as $val){
	   $url = $this->homeUrl.'/product/detail-'.$val['productId'].'.html';
   ?>
    <li>
      <a href="<?php echo $url;?>" target="_blank"><?php echo CHtml::link( CHtml::image($this->imageUrl($val['mainPic'],200,false), $val['title'] ,array('width'=>'160px') ),$url ,array('target'=>'_blank') );?></a>
      <br>
      <label class="checkbox-inline"><input type="checkbox" value="<?php echo $val['productId'];?>"><?php echo $val['price'];?></label>
      <br>
      <a href="<?php echo $url;?>" target="_blank" class="t"><?php echo '【'.$val['serialNumber'].'】'.$val['title'];?></a>
    </li>
   <?php  }?>
  </ul>
  <div class="clearfix page-wrap">
    <div class="pull-right">
      <?php $this->widget('widgets.ZPagerNavigate', array('pages' => $pages,'type' => "mini",));?>
    </div>
    <label class="checkbox-inline">
      <input type="checkbox" name="" value="">全选
    </label>
    <button type="button" class="btn btn-xs btn-cancel batch-del">批量删除</button>
  </div>
</div>
<script>
  seajs.use('app/member/fav/js/fav.js');
</script>
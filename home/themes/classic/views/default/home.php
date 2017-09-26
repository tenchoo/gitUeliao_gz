<link rel="stylesheet" href="/modules/focusplay/css/style.css" />
<div class="main">
  <div class="container hot-new-wrap">
  	<div class="pull-left hot-new">
  	<?php $this->widget('widgets.PieceWidget', array('mark' => 'index_hotsearch'));?>
  	<?php $this->widget('widgets.PieceWidget', array('mark' => 'index_news'));?>
  	</div>
  	<div class="pull-right focus" data-ad="/ajax?action=spread&mark=index_content1">
  	</div>
  </div>
 <?php foreach (  $data as $key=>$val ){ ?>
	<div class="container<?php if( $key == '0' ){ ?> new-rec <?php } ?>">
    <h2 <?php if( $key >'0' ){ ?> class="home-list-hd"<?php } ?>><?php echo $val['title']?></h2>
    <ul class="list-unstyled clearfix home-list">
		<?php foreach ( $val['products'] as $pval ){
			$sound = '';
			if( $key == '0' ){
				$sound = tbProductSound::model()->findMain( $pval['productId'] );
			}
		?>
    	<li>
    		<a href="<?php echo $pval['url'];?>">
    			<img src="<?php echo $this->imageUrl($pval['mainPic'],200);?>" width="160" height="160" alt="<?php echo $pval['title'];?>" rel-sound="<?php $this->imageUrl($sound); ?>" >
    		</a>
    		<a href="<?php echo $pval['url'];?>" class="t">【<?php echo $pval['serialNumber'];?>】<?php echo $pval['title'];?></a>
    		<span class="price">¥<?php echo $pval['price'];?></span>
    	</li>
	<?php }?>
    </ul>
  </div>
 <?php }?>
 <br>
 <br>
  <script>
    //seajs.use('app/home/js/home.js');
  </script>
<style>
.panel-default li a{color:#444}
.panel-default li a b{color:#f60}
.content-wrap .panel{margin-bottom:0;border-radius:0;border-bottom:0}
.panel>.list-group .list-group-item, .panel>.panel-collapse>.list-group .list-group-item {
    border-width:0;
}
.content-wrap .panel .panel-heading{font-weight:700}
</style>
<script type="text/javascript">
$('.content-wrap').find('.panel:last').css('border-bottom','1px solid #ddd');
</script>
<div class="col-md-12 content-wrap">
	<h2 class="h3">待处理任务</h2>
<?php foreach ( $group as $k=>$val){
		if( !array_key_exists($k,$menus) ) continue;
?>
	<div class="panel panel-default">
  	<div class="panel-heading"><?php echo $val;?></div>
  	<ul class="list-group">
<?php
	$c = count($menus[$k]) - 1;
	foreach ( $menus[$k] as $k2=>$_menu){
		if( $k2%4 == 0 ){
?>
  	  <li class="list-group-item clearfix">
<?php }?>
  	    <span class="col-md-3">
			<a href="<?php echo $this->createUrl($_menu['route']);?>">
			<?php echo $_menu['title'];?>
			（<b><?php echo $_menu['totalNum'];?></b>）
			</a>
		</span>
<?php if( $k2%4 == 3 || $k2 == $c ){ ?>
	</li>
<?php }
	}?>
  	</ul>
  </div>
<?php }?>
<br>
<br>
</div>
<?php $this->beginContent('//layouts/_success');$this->endContent();?>
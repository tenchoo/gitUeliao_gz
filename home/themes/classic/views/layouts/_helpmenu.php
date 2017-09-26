<div class="pull-left">
	<h2>帮助首页</h2>
	<ul class="list-unstyled frame-help-menu">
	<?php
	$pactive = isset($this->menu[$this->activeId])?$this->menu[$this->activeId]['parentId']:'';
	foreach ($this->menu as $menu){
		if($menu['parentId'] >'0') continue;
	?>
		<li class="item <?php if( $this->activeId == $menu['categoryId'] || $menu['categoryId'] == $pactive){echo 'active';}?>">
		<?php if(isset($menu['childs'])){ ?>
			<h3 class="clearfix">
			<span class="pull-left"><?php echo $menu['title']?></span>
			<span class="pull-right"><i></i></span>
			</h3>
			<ul class="list-unstyled">
			<?php foreach ($menu['childs'] as $cval){  ?>
			<li  <?php if($this->activeId == $cval['categoryId']){?>class="active"<?php }?> ><a href="<?php echo $this->createUrl('/help/category',array('id'=>$cval['categoryId']));?>"><?php echo $cval['title']?></a></li>
			<?php }?>
			</ul>
		<?php }else{ ?>
			<a href="<?php echo $this->createUrl('/help/category',array('id'=>$menu['categoryId']));?>">
			<h3 class="clearfix">
			<span class="pull-left"><?php echo $menu['title']?></span>
			</h3>
			</a>
		<?php }?>
		</li>
	<?php }?>
    </ul>
</div>
<script>seajs.use('app/help/js/help.js');</script>
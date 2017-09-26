<?php $tabs = $this->tabs(); ?>
<ul class="nav nav-tabs">
<?php foreach($tabs as $val ){ ?>
	 <li role="presentation" class="<?php echo $val['class'];?>">
		<a href="<?php echo $val['url'];?>"><?php echo $val['title'];?></a>
	 </li>
<?php }?>
</ul>
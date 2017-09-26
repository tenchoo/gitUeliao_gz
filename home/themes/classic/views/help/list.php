<div class="pull-right frame-content">
	<div class="frame-box frame-help">
		<div class="help-top">
			<span><i class="inline-block"></i>
			<a href="<?php echo $this->createUrl('index')?>">首页</a>
			<?php foreach ($tabs as $val){ ?>
			 >
			<a href="<?php echo $this->createUrl('category',array('id'=>$val['id']))?>">
				<?php echo $val['title']?>
			</a>
			<?php }?>
			</span>
		</div>
		<div class="padd">
			<ul class="list-unstyled">
			<?php foreach ($list as $val){ ?>
				<li><a href="<?php echo $this->createUrl('detail',array('id'=>$val['helpId']));?>"><?php echo $val['title'];?></a></li>
			<?php }?>
			</ul>
		</div>
	</div>
	<div class="frame-help-bottom">
		<div class="frame-list-bd">
			<div class="pull-left frame-center">
			<?php $this->widget('widgets.ZPagerNavigate', array('pages' => $pages,'maxLinkCount' => 10));?>
			</div>
		</div>
	</div>
</div>
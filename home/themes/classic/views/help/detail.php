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
			<div class="text-center bott">
				<span class="font-18"><?php echo $title;?></span>
			</div>
			<div><?php echo $content;?></div>
		</div>
	</div>
</div>
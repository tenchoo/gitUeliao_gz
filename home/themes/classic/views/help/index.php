<div class="pull-right frame-content">
<div class="frame-box frame-help">
	<div class="help-top">
		<h3>快速引导</h3>
	</div>
	<div class="padd">
	<?php $this->widget('widgets.PieceWidget', array('mark' => 'helpindex'));?>
	</div>
</div>
<br />
<div class="frame-box">
	<div class="help-top">
		<h3>常见问题</h3>
	</div>
	<div class="padd">
	<?php foreach ($list as $val){ ?>
		<div class="frame-help-left inline-block">
			<ul class="list-unstyled">
			<?php foreach ($val as $dval){ ?>
			<li><a href="<?php echo $this->createUrl('detail',array('id'=>$dval['helpId']))?>"><?php echo $dval['title']?></a></li>
			<?php }?>
			</ul>
		</div>
	<?php }?>
	</div>
</div>
</div>
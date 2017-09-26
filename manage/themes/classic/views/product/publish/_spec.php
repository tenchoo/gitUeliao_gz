<?php if ( is_array ( $speclist ) && !empty($speclist)){ ?>
 <div>
	<span class="h4">规格设置</span></div> <br>
	<?php foreach ( $speclist as $val ) { ?>
 <div class="form-group">
	<label class="control-label col-md-2">
		<span class="text-danger">*</span>选择<?php echo $val['specName'];?>：
	</label>
	<div class="control-div col-md-10 col-lg-8">
		<div class="colors">
			<ul class="clearfix list-unstyled color-group">
				<li class="all"><label class="checkbox-inline">
					<input type="checkbox" name=""/>全选</label>
				</li>
				<li data-group="all" class="active">全部</li>
			<?php if( $val['isColor'] ){ foreach( $colorgroups as $key=>$setval ){ ?>
				<li data-group="<?php echo $key;?>"><?php echo $setval;?></li>
			<?php }}?>
			</ul>
			<ul class="clearfix list-unstyled color-items">
				<!--输出全部规格-->
			<?php foreach( $val['specvalue'] as $vval ){
				 $key = $vval['specvalueId'];
				if( array_key_exists( $key, $specs ) ){
					$pic = $specs[$key]['picture'];
					$checked = 'checked';
					$disabled = null;
				}else{
					$pic = $checked = null;
					$disabled = 'disabled';
				}
			?>
			<li data-group="<?php echo $vval['colorSeriesId'];?>"
				data-relation="<?php echo $vval['specId'].':'.$key;?>"
				data-code="#<?php echo $vval['code'];?>"
				data-picture="<?php echo $pic;?>"
				data-title="<?php echo $vval['title'];?>"
				data-serialNumber="<?php echo $vval['serialNumber'];?>">
				<label class="checkbox-inline">
					<input type="checkbox" value="<?php echo $key;?>" <?php echo $checked;?>/>
					<span class="c" style="background:#<?php echo $vval['code'];?>"></span>
					<!--是否有规格图片-->
					<input type="hidden" value ="<?php echo $pic;?>" name="specPic[<?php echo $vval['specId'];?>][<?php echo $key;?>]" <?php echo $disabled;?> class="pic"/> <!--规格图片地址-->
					<?php echo $vval['title'];?> <?php echo $vval['serialNumber'];?>
				</label>
			</li>
		<?php }?>
			</ul>
		</div>
	</div>
</div>
<?php }?>
<div class="form-group">
	<label class="control-label col-md-2">已选择：</label>
	<div class="control-div spec-set col-md-10 col-lg-8">
		<ul class="clearfix colors-checked list-unstyled"></ul>
	</div>
</div>
<?php }?>
<div class="hide spec-data"></div>
<script id="spec" type="text/html">
	{{each list}}
	<li class="hide" data-relation="{{$value.relation}}">
	<div class="uploader">
		<button type="button" style="background:{{$value.code}}">{{$value.img}}</button>
	</div>
	<span class="text">{{$value.color}} {{$value.num}}</span>
	</li>
	{{/each}}
</script>

<script id="specData" type="text/html">
	{{each list}}
	<input type="hidden" name="relation[{{$index}}]"  value="{{$value.relation}}"/>
	{{/each}}
</script>
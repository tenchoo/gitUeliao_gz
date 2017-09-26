<link rel="stylesheet" href="/themes/classic/statics/app/role/css/style.css">

<?php $this->beginContent('//layouts/_error');$this->endContent();?>
<form method="post">
<input type="hidden" name="done" value="<?php echo $done;?>" />
<ul class="auth-manage list-unstyled">
<?php foreach( $menus as $navigate ):?>
<li>
  <div>
  <a href="javascript:" class="glyphicon glyphicon-plus"></a>
  <label class="checkbox-inline"><?php echo CHtml::checkBox("menuId[]",$navigate->isAssign($roleId),['value'=>$navigate->id,'id'=>'taskId'.$navigate->id]);?><?php echo $navigate->title;?></label></div>
  <ul class="list-unstyled  hide">
    <?php foreach( $navigate->childrens as $group ):?>
    <li>
      <div>
	  <a href="javascript:" class="glyphicon glyphicon-plus"></a>
	  <label class="checkbox-inline"><?php echo CHtml::checkBox("menuId[]",$group->isAssign($roleId),['value'=>$group->id,'id'=>'taskId'.$group->id]);?><?php echo $group->title;?></label></div>
      <ul class="list-unstyled  hide">
        <?php foreach( $group->childrens as $menu ):?>
        <li>
            <div>
			<!-- a href="javascript:" class="glyphicon glyphicon-plus"></a-->
			<label class="checkbox-inline" title="<?php echo $menu->title;?>">
            <?php echo CHtml::checkBox("menuId[]",$menu->isAssign($roleId),['value'=>$menu->id,'id'=>'taskId'.$menu->id]);?>
			<?php echo $menu->title;?>
		</label></div>
            <?php if($menu->childrens):?>
            <ul class="list-inline">
                <?php foreach( $menu->childrens as $action ):?>
                    <li>
                        <label class="checkbox-inline" title="<?php echo $action->title;?>">
                            <?php echo CHtml::checkBox("menuId[]",$action->isAssign($roleId),['value'=>$action->id,'id'=>'taskId'.$action->id]);?>
                            <?php echo $action->title;?>
                        </label>
                    </li>
                <?php endforeach;?>
            </ul>
            <?php endif;?>
		</li>
        <?php endforeach;?>
      </ul>
    </li>
    <?php endforeach;?>
  </ul>
</li>
<?php endforeach;?>
</ul>
<br>
<div class="form-group">
  <div class="col-md-10">
    <button type="submit" class="btn btn-success">保存</button>
  </div>
</div>
<br><br>
</form>
<script>
  seajs.use('statics/app/role/js/role.js');
</script>
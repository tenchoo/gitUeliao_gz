<form method="post">
	<label>上级菜单：</label><?php echo CHtml::dropDownList('parentId', $selected, $menus);?>
	<label>菜单名称：</label><input name="form[title]" value="<?php $this->val('title');?>" /><?php $this->showError('title');?>
	<label>路由地址：</label><input name="form[route]" value="<?php $this->val('route');?>" /><?php $this->showError('route');?>
	<input type="submit" />
</form>
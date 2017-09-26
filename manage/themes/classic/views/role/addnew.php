<form method="post">
	<label>角色组名称：</label><input name="form[roleName]" value="<?php $this->val('roleName')?>" /><span><?php $this->showError('roleName');?></span>
	<input type="submit" />
</form>
<form method="post">
	<label>登陆帐号：</label><input name="form[account]" value="<?php $this->val('account');?>" /><?php $this->showError('account');?>
	<label>登陆密码：</label><input type="password" name="form[password]" /><?php $this->showError('password');?>
	<label>用户名：</label><input name="form[username]" value="<?php $this->val('username');?>" /><?php $this->showError('username');?>
	<label>电子邮件：</label><input name="form[email]" value="<?php $this->val('email');?>" /><?php $this->showError('email');?>
	<input type="submit" />
</form>
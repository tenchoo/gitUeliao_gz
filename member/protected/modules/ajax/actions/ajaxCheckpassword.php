<?php
/**
 * ajax 检验密码强度
 * @author liang
 * @version 0.1
 * @param string password
 * @package CAction
 */
class ajaxCheckpassword extends CAction{
	public function run() {
		$password = Yii::app()->request->getPost('password');
		$score = PasswordScore::getScore($password);
		$json = new AjaxData(true,'passwordScore',$score);
		echo $json->toJson();
		Yii::app()->end();
	}
}
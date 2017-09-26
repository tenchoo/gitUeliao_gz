<?php
/**
 * ajax 检查随机图文验证码 
 * @author liang
 * @version 0.1
 * @param string verify 验证码
 * @use 登录页
 * @package CAction
 */
class ajaxCheckverify extends CAction {
	public function run() {
		$verify=Yii::app()->request->getPost('verify');
		$theverify=Yii::app()->user->getState('VerifyCode');
		$state = false;
		$data = '';

		if( $verify && strcasecmp( $verify,$theverify ) === 0 ) {
			$state = true;
		} else {
			$data['VerifyCode'][0] =  Yii::t ( 'user', 'Verification code is not correct' );
		}
		$json=new AjaxData($state,null,$data);
		echo $json->toJson();
		Yii::app()->end();
	}
}
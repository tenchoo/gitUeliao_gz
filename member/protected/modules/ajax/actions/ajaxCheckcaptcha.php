<?php
/**
 * ajax 检查验证码是否正确
 * @author liang
 * @version 0.1
 * @param string account 验证码接收对象
 * @param string verifyCode 验证码
 * @use 注册 忘记密码
 * @package CAction
 */
class ajaxCheckcaptcha extends CAction {
	public function run() {
		$account=Yii::app()->request->getPost('account');
		$verifyCode=Yii::app()->request->getPost('verifyCode');
		$json = new AjaxData(false);		

		if( empty( $verifyCode ) ){
			$data['verifyCode'][0] =  Yii::t ( 'reg', 'Please fill out the verification code' );
		} else {
			if( OpCaptcha::checkCaptcha( $account,$verifyCode ) ) {
				$json->setState( true );
				$data='';
			} else {
				$data['verifyCode'][0] = Yii::t ( 'reg', 'Verification code is not correct' );
			}
		}

		$json->setData( $data );
		echo $json->toJson();
		Yii::app()->end();
	}
}
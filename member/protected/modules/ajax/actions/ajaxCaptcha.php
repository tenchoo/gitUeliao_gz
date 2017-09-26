<?php
/**
 * ajax验证码事件
 * @author yagas
 * @version 0.1
 * @package CCaptchaAction
 */
class ajaxCaptcha extends CCaptchaAction {
	
	/**
	 * 生成随机验证码字符
	 * @return string
	 */
	private function _randCode() {
		$randStr = str_shuffle( 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' );
		$code    = substr($randStr,0,4);
		Yii::app()->user->setState( 'VerifyCode', $code );
		return $code;
	}
	
	public function run() {
		$this->fixedVerifyCode = $this->_randCode();
		//背景色
		$this->backColor       = 0xFFFFFF;
		//验证码位数
		$this->minLength = $this->maxLength = Yii::app()->params['captha_length'];
		//验证码字体
		$this->fontFile = Yii::getPathOfAlias('application.data') . DS . Yii::app()->params['captha_font'];
		return parent::run();
	}
}
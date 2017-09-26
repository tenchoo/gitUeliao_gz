<?php
/**
 * ajax 检查是否已登录,若登录返回登录基本信息
 * @author liang
 * @version 0.1
 * @param int $orderId
 * @package CAction
 */
class ajaxChecklogin extends CAction{
	private $_state = false ;
	private $_message;
	private $_data ;

	public function run() {
		if( Yii::app()->user->getIsGuest() ) {
			$this->_message  = Yii::t('user','You do not log in or log out');
		}else{
			$this->_state = true;
			$data['memberId'] = Yii::app()->user->id ;
			$data['nickName'] = Yii::app()->user->getstate('nickName');
			$data['icon'] = Yii::app()->user->getstate('icon');
			$this->_data = $data;
		}

		$json=new AjaxData($this->_state,$this->_message,$this->_data);
		echo $json->toJson();
		Yii::app()->end();
	}
}
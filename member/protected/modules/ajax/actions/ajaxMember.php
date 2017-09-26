<?php
/**
 * ajax 回调查找member信息
 * @author liang
 * @version 0.1
 * @package CAction
 */
class ajaxMember extends CAction{
	private $_state = false ;
	private $_data;
	private $_message;
	private $_memberId;

	public function run() {
		if( Yii::app()->user->getIsGuest() ) {
			$this->_message = Yii::t('user','You do not log in or log out');
			goto end;
		}

		$userType = Yii::app()->user->getState('usertype');
		if( $userType == 'member'){
			goto end;
		}

		$this->_memberId = Yii::app()->request->getParam('memberId');
		if($this->_memberId){
			$model = tbMember::model()->findByPk($this->_memberId);
			if( $model ){
				$data['memberId'] = $model->memberId;
				$data['userId'] = $model->userId;
				$data['level'] = $model->level;
				$data['priceType'] = $model->priceType;
				$data['phone'] = $model->phone;
				$data['nickName'] = $model->nickName;
				$data['payModel'] = $model->payModel;
				$data['monthlyType'] = $model->monthlyType;
				$this->_state = true;
				$this->_data = $data;
			}
		}

		end:
		$json=new AjaxData($this->_state,$this->_message,$this->_data);
		echo $json->toJson();
		Yii::app()->end();
	}

}
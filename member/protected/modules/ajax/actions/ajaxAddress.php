<?php
/**
 * ajax 订单确认地址栏操作
 * @author liang
 * @version 0.1
 * @package CAction
 */
class ajaxAddress extends CAction{
	private $_state = false ;
	private $_data;
	private $_message;

	public function run() {
		if( Yii::app()->user->getIsGuest() ) {
			$this->_message = Yii::t('user','You do not log in or log out');
			goto end;
		}

		$userType = Yii::app()->user->getState('usertype');
		if( $userType == 'member'){
			$memberId =  Yii::app()->user->id;
		}else{  //业务员登录
			$memberId = Yii::app()->request->getParam('memberId');
			if( !is_numeric($memberId) || $memberId < 1 ){
				$this->_message = Yii::t('user','memberId must');
				goto end;
			}
		}

		$optype = Yii::app()->request->getParam('optype');
		if( method_exists ( $this,$optype ) ) {
			$this->$optype( $memberId );
		}

		end:
		$json=new AjaxData($this->_state,$this->_message,$this->_data);
		echo $json->toJson();
		Yii::app()->end();
	}

	/**
	* 取得地址信息
	* @param integer $memberId
	*/
	public function getlist( $memberId ){
		$address = tbMemberAddress::model()->getAll( $memberId );
		$result = array();
		foreach( $address as $key=>$val ){
			$result[$key] = $val->attributes;
			$result[$key]['cityinfo'] = tbArea::getAreaStrByFloorId( $val->areaId );
		}
		$this->_data = $result;
		$this->_state = true;
	}

	/**
	* 取得单个地址信息
	* @param integer $memberId
	*/
	public function getinfo( $memberId ){
		$id = Yii::app()->request->getParam('id');
		$address = tbMemberAddress::model()->findOne($id ,$memberId );
		if( $address ){
			$this->_state = true;
			$this->_data = $address->attributes;
		}else{
			$this->_message = 'NO Data';
		}
	}

	/**
	* 新增/编辑地址
	* @param integer $memberId
	*/
	public function edit( $memberId ){
		$address  = Yii::app()->request->getPost( 'address' );
		if( empty( $address ) ){
			$this->_message = 'NO Data';
			return false;
		}

		$model = new tbMemberAddress();
		if( empty( $address['addressId'] ) ){
			$model->memberId = $memberId;
			//最多只能有10个地址
			$count = $model-> getCount( $memberId );
			if( $count >= 10 ){
				$this->_message = Yii::t('msg','Receive the goods address 10');
				return false;
			}
		}else{
			$model = $model->findOne( $address['addressId'],$memberId );
			if( !$model ){
				$this->_message = 'the address is no exists';
				return false;
			}
		}
		unset($address['addressId']);
		$model->attributes = $address;
		if($model->save()) {
			$this->_state = true;
			$this->_message = 'success';
			$this->_data = $model->attributes;
			$this->_data['cityinfo'] = tbArea::getAreaStrByFloorId( $model->areaId );
		}else{
			$this->_data = $model->getErrors();
		}
	}
}
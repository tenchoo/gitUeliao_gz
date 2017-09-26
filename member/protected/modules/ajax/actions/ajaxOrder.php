<?php
/**
 * ajax 订单管理
 * @author liang
 * @version 0.1
 * @param int $orderId
 * @package CAction
 */
class ajaxOrder extends CAction{
	private $_state = false ;
	private $_message;
	private $_data = '' ;


	public function run() {
		if( Yii::app()->user->getIsGuest() ) {
			$this->_message = Yii::t('user','You do not log in or log out');
			goto end;
		}

		$optype = Yii::app()->request->getParam('optype');
		if( method_exists ( $this,$optype ) ) {
			$this->$optype();
		}

		end:
		$json=new AjaxData($this->_state,$this->_message,$this->_data);
		echo $json->toJson();
		Yii::app()->end();
	}

	/**
	* 上传凭证
	* @param integer $paymemtId
	* @param string $voucher
	*/
	public function upload(){
		$id = Yii::app()->request->getPost('paymemtId');
		$voucher = Yii::app()->request->getPost('voucher');
		$order = new Order();
		$result = $order->uploadVoucher( $id,$voucher );
		if( $result == 'true' ){
			$this->_state = true;
		}else{
			if(is_array($result)) $result = current($result);
			$this->_message = $result['0'];
		}
	}

	/**
	* 取消订单
	* @param integer $orderId  订单ID
	* @param integer $closeReason  订单关闭理由
	*/
	public function cancleorder(){
		$orderId = Yii::app()->request->getPost('orderId');
		$closeReason = Yii::app()->request->getPost('closeReason');
		$userType =  Yii::app()->user->getState('usertype');

		if( $userType =='saleman' ){
			$whoClose = '1';
		}else{
			$whoClose = '0';
		}

		$result = Order::cancleOrder( $orderId ,$whoClose, $closeReason );
		if( $result ===  true ){
			$this->_state = true;
		}else{
			if(is_array($result)) $result = current($result);
			$this->_message = $result['0'];
		}
	}

	/**
	* 业务员-留货单申请延期，最后一天可以申请,申请延期后订单需重新审核
	* @access 申请延期
	* @param integer $orderId	订单号
	* @output json
	*/
	public function delaykeep(){
		$userType =  Yii::app()->user->getState('usertype');
		if( $userType !='saleman' ){
			$this->_message = Yii::t('user','Only sales man can do this action');
			return ;
		}

		$orderId = Yii::app()->request->getParam('orderId');
		$this->_state = tbOrderKeepDelay::delay( $orderId,$this->_message );
	}

	/**
	* 通知发货
	* @access 通知发货
	* @param integer $packingId
	* @output json
	*/
	/* public function noticedelivery(){
		$userType =  Yii::app()->user->getState('usertype');
		if( $userType !='saleman' ){
			$this->_message = Yii::t('user','Only sales man can do this action');
			return ;
		}

		$packingId = Yii::app()->request->getPost('packingId');
		$result = Order::noticeDelivery( $packingId );
		if( $result == 'true' ){
			$this->_state = true;
		}else{
			$this->_data = $result;
		}
	} */

	/**
	* 删除订单
	* @param integer $orderId  订单ID
	* @param integer $who    1 买家操作删除,2 卖家操作删除
	*/
	/* public function delorder(){
		$orderId = Yii::app()->request->getPost('orderId');
		$result = Order::del( $orderId );
		$who = (int) Yii::app()->request->getPost('who');
		$data = $this->_obj->delorder( $this->_orderId , $who );
		if( $data == 'true' ){
			$this->_state =  true;
		}
	} */



}
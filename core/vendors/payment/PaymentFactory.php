<?php
/**
 * 支付接口工厂类
 * @author morven
 *
 */
abstract class PaymentFactory {

	/**
	 * 支付接口
	 * @param unknown $params 支付信息
	 * @param unknown $payid 支付接口ID
	 * @param unknown $memberId 会员ID
	 */
	public function pay($params,$payid,$memberId){
		if($memberId!=0){//商家接口
			$payment = CmpPayment::model()->findByAttributes(array('paymentId'=>$payid,'memberId'=>$memberId));
		}else{
			$payment = tbPayMent::model()->findByPk($payid);
		}
		//实例化支付类
		//var_dump($payment->class_name);exit;
		$alipay = new $payment->class_name();
		
		//配置
		$alipay->pay_config = $payment->paymentSet;	
		return $alipay->buildForm($params);
	}
	
	/**
	 * 取所有的支付方式
	 * @return multitype:static
	 */
	public function findAll(){
		$payment = tbPayMent::model()->findAll('available=1');
		return $payment;
	}
	
	
	/**
	 * 生成付款单
	 * @param string $orderids 订单ID
	 * @param integer $price  支付价格
	 * @param integer $type  支付类型
	 */
	public function setPaymentOrder( $orderids, $price=0 , $type=0 ){
		$model = new tbOrdPayment();
		$model->ordpaymentId = uniqid();
		$model->memberId = Yii::app()->user->id;
		$model->orderIds = $orderids;
		$model->type = $type;
		$model->price = $price;//
		$model->state = 0;
		if($model->save()){
			return $model->ordpaymentId;
		}else{
			return false;
		}
	
	}
	/**
	 * 更新支付单状态
	 * @param unknown $ordpaymentId 支付单号
	 * @param number $state 状态 0:待付款,1:已付款,3:作废,4:删除
	 */
	public function setPaymentState($ordpaymentId,$state=1){
		$model = tbOrdPayment::model()->findByPk($ordpaymentId);
		$model->state = $state;
		$model->update();
		return true;
	}

}

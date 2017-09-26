<?php
/**
 * 线下支付
 * @author morven
 * @package PaymentFactory
 */

class OfflinePayment extends PaymentFactory {
	
	
	/**
	 * 支付接口
	 * @param unknown $params 支付信息
	 * @param unknown $payid 支付接口ID
	 * @param unknown $memberId 会员ID
	 */
	public function pay($params,$payid,$memberId){
		if($zm_id!=0){//商家接口
			$payment = CmpPayment::model()->findByAttributes(array('zpy_id'=>$zpy_id,'memberId'=>$memberId));
		}else{
			$payment = $this->findByPk($zpy_id);
		}
		//实例化支付类
		$alipay = new $payment->class_name();
		//配置
		$alipay->pay_config = $payment->zpy_config;
		return $alipay->buildForm($param);
	}
	
}

?>
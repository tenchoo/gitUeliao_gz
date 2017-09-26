<?php
/**
 *
 * 微信回调类
 * @author liang
 *
 */
class PaymentWxNotify extends WxPayNotify
{

	/**
	 *
	 * 回调方法入口，重写父方法,
	 * 回调时只是对回调的数据进行必要的处理，接收数据完成则返回true,返回false会不断重复回调。
	 * 注意：
	 * 1、微信回调超时时间为2s，建议用户使用异步处理流程，确认成功之后立刻回复微信服务器
	 * 2、微信服务器在调用失败或者接到回包为非确认包的时候，会发起重试，需确保你的回调是可以重入
	 * @param array $data 回调解释出的参数
	 * @param string $msg 如果回调处理失败，可以将错误信息输出到该方法
	 * @return true回调出来完成不需要继续回调，false回调处理未完成需要继续回调
	 */
	public function NotifyProcess($data, &$msg)
	{
		//写回调日志
		Yii::log( "call back:" . json_encode($data), 'info', 'wxpay.Notify');

		if( !array_key_exists("transaction_id", $data) ){
            $msg = "输入参数不正确";
			Yii::log( "return back: 缺少transaction_id，" . $msg, 'info', 'wxpay.Notify');
            return false;
        }

		if($data['return_code'] != 'SUCCESS' || $data['result_code'] != 'SUCCESS'){
			return true;
		}

		$payment = tbOrdPayment::model()->findByPk( $data['out_trade_no'] );
		if( $payment ){
			$ids  = explode(',',$payment->orderIds);
			if( $data['total_fee'] != $payment->price*100 ){ //支付金额相符时更新订单的支付状态
				$msg = "支付金额不相符";
				Yii::log( "return back:" . $msg, 'info', 'wxpay.Notify');
				return true;
			}

			$payForm = new OrderPayForm();
			$result = $payForm -> onlinePay( $payment ,$data['result_code'],$data['transaction_id'] );
			if( !$result ){
				$msg = '更新支付状态失败';
				$msg .= json_encode( $payForm->getErrors() );
				Yii::log( "return back:" . $msg, 'info', 'wxpay.Notify');
				return false;
			}

			Yii::log( "return back: true" , 'info', 'wxpay.Notify');
		}
		return true;
	}
}
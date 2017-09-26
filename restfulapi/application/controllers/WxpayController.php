<?php
/**
* 订单微信支付接口
*/
DEFINE ( "WXPAY_APP",TRUE );

class WxpayController extends Controller {

	public function init() {
		Yii::import('libs.vendors.WxPay.WxPayDataBase',true);
		Yii::import('libs.vendors.WxPay.*');
	}


	/**
	* 服务器端把签名，商户信息之类的处理完，返回给APP端，APP调用微信。
	* 根据微信文档生成APP端需要的信息,APP端调用微信客户端支付完成后,微信会主动把支付结果发到回调地址，也可以主动向微信查询结果
	*
	*/
	public function actionIndex(){

		$this->authenticateToken();
		$this->authenticateMemberInfo();

		$tradeNo = Yii::app()->request->getQuery("tradeNo");

		$model = tbOrdPayment::model()->findByPk( $tradeNo );
		if( !$model ){
			$this->showJson(false,'the paymemt you require does not exists');
		}

		$motifyUrl = Yii::app()->request->hostInfo.'/wxpay/notify';
		$body = '订单编号：'.$model->orderIds;
		$fee = $model->price*100;//以分为单位

		//②、统一下单,out_trade_no、body、total_fee、trade_type必填
		$input = new WxPayUnifiedOrder();
		$input->SetBody( $body ); //商品或支付单简要描述
	//	$input->SetAttach("attach"); //附加数据
		$input->SetOut_trade_no( $tradeNo );//商户系统的订单号
		$input->SetTotal_fee( $fee ); //总金额,单位为分
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 600));
	//	$input->SetGoods_tag("tag");//商品标记，代金券或立减优惠功能的参数
		$input->SetNotify_url( $motifyUrl );//设置回调地址
		$input->SetTrade_type("APP");//JSAPI，NATIVE，APP
		$payInfo = WxPayApi::unifiedOrder($input);

		if( $payInfo['return_code'] == 'FAIL' ){
			$this->showJson(false,$payInfo['return_msg']);
		}

		$notify = new AppPay();
		$appApiParameters = $notify->GetAppApiParameters( $payInfo );//生成提交给app的一些参数
		$data = json_decode( $appApiParameters,true );
		unset( $data['appid'] );
		$this->showJson(true,'success',$data );
	}


	/**
	* 回调函数。
	* 根据微信文档生成APP端需要的信息,APP端调用微信客户端支付完成后,微信会主动把支付结果发到回调地址，也可以主动向微信查询结果
	*
	*/
	public function actionNotify(){
		$notity = new PaymentWxNotify();
		$result = $notity->Handle(false);
		header("Content-type:text/xml");
		echo $result;
		exit;
	}

}
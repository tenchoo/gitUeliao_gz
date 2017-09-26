<?php
/**
 * 支付接口
 * @author morven
 * @package PaymentFactory
 */

class PaymentStorage {


	private $instance;

	public $type;

	public function __construct($type='online') {
		$this->type = $type;
	}

	/**
	 * 创建实例类
	 * @param string $type 调用实例类型
	 * @throws CHttpException
	 */
	public function create() {
		$className = ucfirst($this->type) . 'Payment';
		if( class_exists($className,true) ) {
			$f = new ReflectionClass( $className );
			$this->instance = $f->newInstance();
			return;
		}
		throw new CHttpException(500,'No Class Instance');
	}

	/**
	 * 提交支付
	 * @param unknown $params
	 * @param unknown $payid
	 * @param string $memberId
	 */
	public function pay($params=array(),$payid,$memberId=null){
		$this->create();

		return $this->instance->pay($params,$payid,$memberId);
	}

	/**
	 * 生成付款单
	 * @param unknown $orderids 订单ID,多个以逗号分隔
	 * @param unknown $price 付款价格
	 * @param number $type 支付类型 0:订单,1:充值
	 */
	public function setPaymentOrder($orderids , $price , $type=0){
		$this->create();
		return $this->instance->setPaymentOrder($orderids , $price , $type);
	}

	/**
	 * 更新付款单状态
	 * @param unknown $ordpaymentId 付款单号
	 * @param number $state 状态 0:待付款,1:已付款,3:作废,4:删除
	 */
	public function setPaymentState( $ordpaymentId , $state =1 ){
		$this->create();
		return $this->instance->setPaymentState( $ordpaymentId , $state );
	}

	/**
	 * 取支付方式
	 */
	public function findAll(){
		$this->create();
		return $this->instance->findAll();
	}

	/**
	 * 重构发送的数据
	 * @param unknown $model 支付数据
	 * @param unknown $payid 支付方式
	 */
	public function Restructure($model,$payid,$defaultbank = 'ICBCBTB'){

		$out_trade_no	= $model->ordpaymentId;
		$total_fee		= $model->price;
		$subject		=  $model->title;
		$body		= '';

		$params = array();
		$payment = tbPayMent::model()->findByPk($payid);      //取后台的配置
		switch ($payment->class_name) {
			case 'AlipayDirect'://支付宝即时到账
				$params = array(
						"out_trade_no"  => $out_trade_no,//商户订单号
						"subject"   => $subject,//订单名称
						"total_fee" => $total_fee,//付款金额
						"body"  => $body, //订单描述
						"show_url"  => Yii::app()->request->hostInfo.'/order',//商品展示地址
						//"anti_phishing_key" => time(),//防钓鱼时间戳
						//"exter_invoke_ip"   => Yii::app()->request->userHostAddress,//客户端的IP地址
				);
				break;
			case 'AlipayEscow':
				$params = array(
						"out_trade_no"  => $out_trade_no,//商户订单号
						"subject"   => $subject,//订单名称
						"price" => $total_fee,//付款金额
						"quantity"  => '1',//商品数量
						"logistics_fee" => '0',//物流费用
						"logistics_type"    => 'EXPRESS',//物流类型，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
						"logistics_payment" => 'SELLER_PAY',//物流支付方式SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）
						"body"  => $body, //订单描述
						"show_url"  => Yii::app()->request->hostInfo.'/order',//商品展示地址
						"receive_name"  => '',//收货人姓名
						"receive_address" => '',//收货人地址
						"receive_zip"   => '', //收货人邮编
						"receive_phone" => '',//收货人电话号码
						"receive_mobile"    => '',//收货人手机号码
				);
				break;
			case 'AlipayBank':
				$params = array(
						"out_trade_no"  => $out_trade_no,//商户订单号
						"subject"   =>$subject,//订单名称
						"total_fee" => $total_fee,//付款金额
						"body"  => $body, //订单描述
						"show_url"  => Yii::app()->request->hostInfo.'/order',//商品展示地址
						"paymethod" => 'bankPay',
						"defaultbank"   => $defaultbank,
						//"anti_phishing_key" => time(),//防钓鱼时间戳
						//"exter_invoke_ip"   => Yii::app()->request->userHostAddress,//客户端的IP地址
				);
				break;
			case 'TenpayEscow':
				 $params = array(
                         "out_trade_no"  => $out_trade_no,//商户订单号
                         "subject"   => $subject,//订单名称
                         "total_fee" => $total_fee*100,//付款金额（包含运费），以分为单位 */
                         "body"  => $body, //订单描述
                       );
				break;
		}
		return $params;
	}
}

?>
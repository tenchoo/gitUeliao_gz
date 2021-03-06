<?php
/* *
 * 功能：纯担保交易接口接入页
 * 版本：3.3
 * 修改日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************注意*************************
 * 如果您在接口集成过程中遇到问题，可以按照下面的途径来解决
 * 1、商户服务中心（https://b.alipay.com/support/helperApply.htm?action=consultationApply），提交申请集成协助，我们会有专业的技术工程师主动联系您协助解决
 * 2、商户帮助中心（http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9）
 * 3、支付宝论坛（http://club.alipay.com/read-htm-tid-8681712.html）
 * 如果不想使用扩展功能请把扩展功能参数赋空值。
 */

class AlipayEscow extends CComponent
{
	//字符编码格式 目前支持 gbk 或 utf-8
    private $input_charset = "utf-8";
    //签名方式 不需修改
    private $sign_type = "MD5";
    //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
    private $transport = "http";
    //ca证书路径地址，用于curl中ssl校验
    //请保证cacert.pem文件在当前文件夹目录中
    private $cacert = '/protected/extensions/payment/key/escow/cacert.pem';

    //安全检验码，以数字和字母组成的32位字符
    public $key = "";
    //合作身份者id，以2088开头的16位纯数字
    public $partner = "";
    
    //卖家支付宝帐户
    public $seller_email = "";
    //页面跳转同步通知页面路径
    public $return_url = "/alipay/return";
    //服务器异步通知页面路径
    public $notify_url = "/alipay/notify";
    //商品展示URL
    public $show_url = "";
    //配置
    public $pay_config;

    public function __construct(){
    	$this->init();
    }
    
    public function init()
    {
        Yii::import('libs.vendors.payment.class.*');
    }

    //支付提交
    public function buildForm($request)
    {
    	$alipaySubmit = new AlipaySubmit($this->buildConfig());
        $params = array(
        		"service" => "create_partner_trade_by_buyer",
        		"partner" => trim($this->pay_config['payment_id']),//合作身份者id
        		"payment_type"	=> '1',//支付类型
        		"notify_url"	=> ApiClient::model('member')->createUrl($this->notify_url),//服务器异步通知页面路径
        		"return_url"	=> ApiClient::model('member')->createUrl($this->return_url),//页面跳转同步通知页面路径
        		"seller_email"	=> trim($this->pay_config['payment_user']),//卖家支付宝帐户
        		"out_trade_no"	=> '',//商户订单号
        		"subject"	=> '',//订单名称
        		"price"	=> '',//付款金额
        		"quantity"	=> '',//商品数量
        		"logistics_fee"	=> '',//物流费用
        		"logistics_type"	=> 'EXPRESS',//物流类型，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
        		"logistics_payment"	=> 'SELLER_PAY',//物流支付方式SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）
        		"body"	=> '', //订单描述
        		"show_url"	=> '',//商品展示地址
        		"receive_name"	=> '',//收货人姓名
        		"receive_address"	=> '',//收货人地址
        		"receive_zip"	=> '', //收货人邮编
        		"receive_phone"	=> '',//收货人电话号码
        		"receive_mobile"	=> '',//收货人手机号码
        		"_input_charset"	=> trim(strtolower($this->input_charset))//字符编码格式 目前支持 gbk 或 utf-8
        );
        $params = array_merge($params, $request);
        ksort($params);
        reset($params);
        //建立请求
        $html_text = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>支付宝纯担保交易接口接口</title></head>';
        $html_text .= '<span>正在跳转到支付宝支付中,5秒后不能跳转请点确认</span>';
        $html_text .= $alipaySubmit->buildRequestForm($params,"get", "确认");
		echo $html_text;
    }
    
    //发货
    public function setGoods($params){
    	$parameter = array(
    			"service" => "send_goods_confirm_by_platform",
    			"partner" => trim($this->pay_config['payment_id']),//合作身份者id
    			"trade_no"	=> '',//支付宝交易号
    			"logistics_name"	=> '',//物流公司名称
    			"invoice_no"	=> '', //物流发货单号
    			"transport_type"	=> 'EXPRESS',//物流类型，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
    			"_input_charset"	=> trim(strtolower($this->input_charset))//字符编码格式 目前支持 gbk 或 utf-8
    	);
    	$params = array_merge($parameter, $params);
    	ksort($params);
    	reset($params);
    	//建立请求
    	$alipaySubmit = new AlipaySubmit($this->buildConfig());
    	$html_text = $alipaySubmit->buildRequestHttp($params);
		$doc = !empty($html_text)?xml2array($html_text):'';
		return $doc;
    }

    //服务器异步通知页面路径
    public function verifyNotify()
    {
    	$alipayNotify = new AlipayNotify($this->buildConfig());
    	$verify_result = $alipayNotify->verifyNotify();
    	 return $verify_result;
    }

    //页面跳转同步通知
    public function verifyReturn()
    {
    	$alipay_config = $this->buildConfig();
    	$alipayNotify = new AlipayNotify($this->buildConfig());
    	$verify_result = $alipayNotify->verifyReturn();
        return $verify_result;
    }

    //构造配置
    public function buildConfig(){
    	return $alipay_config = array(
	    			'partner'=>trim($this->pay_config['payment_id']),
			    	'key'=> trim($this->pay_config['payment_key']),
			    	'sign_type'=>$this->sign_type,
			    	'input_charset'=>$this->input_charset,
			    	'cacert'=>getcwd().$this->cacert,
			    	'transport'=>$this->transport,
			    );
    }
}


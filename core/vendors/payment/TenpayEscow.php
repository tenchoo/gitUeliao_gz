<?php
/* *
 * 功能：财付通交易接口接入页
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

class TenpayEscow extends CComponent
{
	//字符编码格式 目前支持 gbk 或 utf-8
    private $input_charset = "utf-8";
     //签名方式 不需修改
    private $sign_type = "MD5";

    public $spname="财付通双接口";

    //财付通商户号
    public $partner = "1217061901"; 

    //财付通密钥
    public $key = "ad2f926e6f4ef8b0cc8f94d9dcf69415";                                          

    public $server ="https://gw.tenpay.com/gateway/pay.htm";

    public $trade_mode = '1';//交易模式（1.即时到帐模式，2.中介担保模式，3.后台选择（卖家进入支付中心列表选择））

    //页面跳转同步通知页面路径
    public $return_url = "/alipay/payReturn";          //显示支付结果页面,*替换成payReturnUrl.php所在路径

    public $notify_url = "/alipay/payNotify";          //支付完成后的回调处理页面,*替换成payNotifyUrl.php所在路径

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

         /* 创建支付请求对象 */
        $reqHandler = new RequestHandler();
        $reqHandler->init();
        $reqHandler->setKey(trim($this->pay_config['payment_key']));
        $reqHandler->setGateUrl($this->server);

        //----------------------------------------
        //设置支付参数 
        //----------------------------------------
        $reqHandler->setParameter("partner", trim($this->pay_config['payment_id']));
        $reqHandler->setParameter("out_trade_no", $request['out_trade_no']);//订单号
        $reqHandler->setParameter("total_fee", $request['total_fee']);  //总金额
        $reqHandler->setParameter("return_url", ApiClient::model('member')->createUrl($this->return_url) );
        $reqHandler->setParameter("notify_url", ApiClient::model('member')->createUrl($this->notify_url) );
        $reqHandler->setParameter("body", $request['body']);
        $reqHandler->setParameter("bank_type", "DEFAULT");        //银行类型，默认为财付通
        //用户ip
        $reqHandler->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']);//客户端IP
        $reqHandler->setParameter("fee_type", "1");               //币种
        $reqHandler->setParameter("subject",mb_substr($request['subject'],0,30,'utf-8'));          //商品名称，（中介交易时必填）

        //系统可选参数
        $reqHandler->setParameter("sign_type", "MD5");            //签名方式，默认为MD5，可选RSA
        $reqHandler->setParameter("service_version", "1.0");      //接口版本号        
        $reqHandler->setParameter("input_charset", "utf-8");      //字符集
        $reqHandler->setParameter("sign_key_index", "1");         //密钥序号
        //$reqHandler->createSign();

        //业务可选参数
        $reqHandler->setParameter("attach", "");                  //附件数据，原样返回就可以了
        $reqHandler->setParameter("product_fee", "");             //商品费用
        $reqHandler->setParameter("transport_fee", "0");          //物流费用
        $reqHandler->setParameter("time_start", date("YmdHis"));  //订单生成时间
        $reqHandler->setParameter("time_expire", "");             //订单失效时间
        $reqHandler->setParameter("buyer_id", "");                //买方财付通帐号
        $reqHandler->setParameter("goods_tag", "");               //商品标记
        $reqHandler->setParameter("trade_mode",$this->trade_mode);              //交易模式（1.即时到帐模式，2.中介担保模式，3.后台选择（卖家进入支付中心列表选择））
        $reqHandler->setParameter("transport_desc","");              //物流说明
        $reqHandler->setParameter("trans_type","1");              //交易类型
        $reqHandler->setParameter("agentid","");                  //平台ID
        $reqHandler->setParameter("agent_type","");               //代理模式（0.无代理，1.表示卡易售模式，2.表示网店模式）
        $reqHandler->setParameter("seller_id","");               //卖家的商户号           
        //请求的URL
        $reqUrl = $reqHandler->getRequestURL();
        //获取debug信息,建议把请求和debug信息写入日志，方便定位问题
        /**/
        $debugInfo = $reqHandler->getDebugInfo();

        $params = $reqHandler->getAllParameters();        
        
        $url = $reqHandler->getGateUrl();
        //dump($params);
        //dump($request);
        //$params = array_merge($params, $request);        
        //dump($params);

        //建立请求
        $html_text = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>财付通交易接口接口</title></head>';
        $html_text .= '<span>正在跳转到财付通支付中,5秒后不能跳转请点确认</span>';
        $html_text .= $this->buildRequestForm($params,"post", "确认",$url);        
        echo $html_text;       
    }
    
    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     * @param $method 提交方式。两个值可选：post、get
     * @param $button_name 确认按钮显示文字
     * @return 提交表单HTML文本
     */
    function buildRequestForm($para_temp, $method, $button_name, $url) {
        //待请求参数数组
        $para = $para_temp;
       
        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$url."' method='".$method."'>";
        while (list ($key, $val) = each ($para)) {
            $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }

        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml."<input type='submit' value='".$button_name."'></form>";
        
        $sHtml = $sHtml."<script>document.forms['alipaysubmit'].submit();</script>";
        
        return $sHtml;
    }


    //服务器异步通知页面路径
    public function verifyNotify()
    {
        /* 创建支付应答对象 */
         $resHandler = new ResponseHandler();
         $resHandler->setKey(trim($this->pay_config['payment_key']));
         return $resHandler;
    }

    //页面跳转同步通知
    public function verifyReturn()
    {
        /* 创建支付应答对象 */
         $resHandler = new ResponseHandler();
         $resHandler->setKey(trim($this->pay_config['payment_key']));
        return $resHandler;
    }

    //构造配置
    public function buildConfig(){
        return $alipay_config = array(
                    'partner'=>trim($this->pay_config['payment_id']),
                    'key'=> trim($this->pay_config['payment_key']),
                    'sign_type'=>$this->sign_type,
                    'input_charset'=>$this->input_charset,
                );
    }
}


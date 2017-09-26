<?php
/* *
 * 功能：招商银行交易接口接入页
 * 版本：3.3
 * 修改日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************注意*************************
 接口地址:https://netpay.cmbchina.com/netpayment/BaseHttp.dll?PrePayC
 测试接口:https://netpay.cmbchina.com/netpayment/BaseHttp.dll?TestPrepayC1
参数说明：
        BranchID:   商户开户分行号，请咨询开户的招商银行分支机构；
        CoNo:       商户号，6位数字，由银行在商户开户时确定；
        BillNo:     定单号，6位或10位数字，由商户系统生成，一天内不能重复；
        Amount:     定单总金额，格式为：xxxx.xx元；
        Date:       交易日期，格式：YYYYMMDD。
        MerchantUrl: 返回地址;

支付结果通知命令格式型如：       
http://www.merchant.com/path/ProcResult.dll?Succeed=..&BillNo=..&Amount=..&Date=..&Msg=..&signature=..

其中，path和ProcResult.dll由商户任意确定，并且支付命令中可包含多个path，即可有path1/path2/path3。

        参数说明：
    Succeed：    取值Y(成功)或N(失败)；
        BillNo:     定单号(由支付命令送来)；
    Amount:     实际支付金额(由支付命令送来)；
        Date:       交易日期(主机交易日期)；
Msg:    银行通知用户的支付结果消息。信息的前38个字符格式为：4位分行号＋6位商户号＋8位银行接受交易的日期＋20位银行流水号；可以利用交易日期＋银行流水号对该定单进行结帐处理；
    Signature:  银行用自己的Private Key对通知命令的签名。

*/

class BankCMB extends CComponent
{   
    //字符编码格式 目前支持 gbk 或 utf-8
    private $input_charset = "utf-8";
    //商户开户分行号
	public $BranchID = "0755";
    //商户号，6位数字，由银行在商户开户时确定；
    public $CoNo = "100717";

    public $server ="https://netpay.cmbchina.com/netpayment/BaseHttp.dll?PrePayC1";

    //页面跳转同步通知页面路径
    public $MerchantUrl = "/alipay/BankCMB";

    //商品展示URL
    public $show_url = "";

    public $MerchantPara ='';    //商户需要银行在支付结果通知中转发的商户参数。
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
    	     
        //print_r($request);exit;
        $params = array(            
            "BranchID" => trim($this->pay_config['payment_user']),//合作身份者id 
            "CoNo"  => trim($this->pay_config['payment_id']),//卖家支付宝帐户     
        	"BillNo"  => '',//商户订单号                              
            "Amount" => '',//付款金额
            "Date" => date('Ymd',time()),
        	"MerchantUrl"	=> ApiClient::model('member')->createUrl($this->MerchantUrl),//返回地址        	
        );
       // print_r($params);exit;
        $params = array_merge($params, $request);
        //建立请求
        $html_text = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>招商银行交易接口接口</title></head>';
        $html_text .= '<span>正在跳转到招商银行支付中,5秒后不能跳转请点确认</span>';
        $html_text .= $this->buildRequestForm($params,"POST", "确认");
        echo $html_text;       
    }
    
    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     * @param $method 提交方式。两个值可选：post、get
     * @param $button_name 确认按钮显示文字
     * @return 提交表单HTML文本
     */
    function buildRequestForm($para_temp, $method, $button_name) {
        //待请求参数数组
        $para = $para_temp;
        
        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$this->server."' method='".$method."'>";
        while (list ($key, $val) = each ($para)) {
            $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }

        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml."<input type='submit' value='".$button_name."'></form>";
        
        $sHtml = $sHtml."<script>document.forms['alipaysubmit'].submit();</script>";
        
        return $sHtml;
    }

    //构造配置
    public function buildConfig(){
    	return $alipay_config = array(
	    			'BranchID'=>trim($this->pay_config['payment_user']),
			    	'CoNo'=> trim($this->pay_config['payment_id']),			    	
			    	'input_charset'=>$this->input_charset,			    	
			    );
    }
    
}


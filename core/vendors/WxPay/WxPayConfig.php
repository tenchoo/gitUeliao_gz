<?php
/**
* 	配置账号信息
*/
 if ( defined("WXPAY_APP") ){

	class WxPayConfig
	{
		//APP 支付参数
		 const APPID = 'wxa977c958277012e3';
		 const APPSECRET = '0c6cb460333e6c0cb88e5f060ce03ae0';
		 const MCHID = '1324583201';
		 const KEY = '8896jjKKLITF778826465fdAERfsy752';

		const SSLCERT_PATH = './cert/apiclient_cert.pem';
		const SSLKEY_PATH = './cert/apiclient_key.pem';
		const CURL_PROXY_HOST = "0.0.0.0";//"10.152.18.220";
		const CURL_PROXY_PORT = 0;//8080;
		const REPORT_LEVENL = 1;

	}

 }else{

	class WxPayConfig
	{
		//微信公众号支付参数
		const APPID = 'wx29bd74e0b911f9bf';
		const MCHID = '1300433201';
		const KEY = 'pjklASEY658cf239jdjdfhnvb76id534';
		const APPSECRET = '63323005ed2ba07d7a51c0ec804bc77f';
		const ENCODINGAESKEY = 'ch4Kw8O81LfA8R2Fs95uOGlKclho5HKNshYnLwjR94j';//encodingaeskey
		const TOKEN = 'Ky09sf1m6v88Wejk635gf9p52vb';

		const SSLCERT_PATH = './cert/apiclient_cert.pem';
		const SSLKEY_PATH = './cert/apiclient_key.pem';
		const CURL_PROXY_HOST = "0.0.0.0";//"10.152.18.220";
		const CURL_PROXY_PORT = 0;//8080;
		const REPORT_LEVENL = 1;

	}

 }

//class WxPayConfig
//{


	//=======【基本信息设置】=====================================
	//
	/**
	 * TODO: 修改这里配置为您自己申请的商户信息
	 * 微信公众号信息配置
	 *
	 * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
	 *
	 * MCHID：商户号（必须配置，开户邮件中可查看）
	 *
	 * KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
	 * 设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
	 *
	 * APPSECRET：公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），
	 * 获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN
	 * @var string
	 */
	/* const APPID = 'wx6ad72904f5231588';
	const MCHID = '1239560602';
	const KEY = 'wxL8i52de337zE5e5e5dA6ok5lm7O3vc';
	const ENCODINGAESKEY = 'HdOabFRgQWrF9ENNDUbKXWRLajOVw1cEZJYRv24xNKm';//encodingaeskey

	const TOKEN = 'pejqgy1397550037';
	const APPSECRET = '19435d3c29f63c227ea843b4e18c30ed'; */

	//微信公众号支付参数
/* 	const APPID = 'wx29bd74e0b911f9bf';
	const MCHID = '1300433201';
	const KEY = 'pjklASEY658cf239jdjdfhnvb76id534';
//	const APPSECRET = '63323005ed2ba07d7a51c0ec804bc77f';
	const ENCODINGAESKEY = 'ch4Kw8O81LfA8R2Fs95uOGlKclho5HKNshYnLwjR94j';//encodingaeskey
	const TOKEN = 'Ky09sf1m6v88Wejk635gf9p52vb';	 */

	//APP 支付参数
	 // const APPID = 'wxa977c958277012e3';
	 // const APPSECRET = '0c6cb460333e6c0cb88e5f060ce03ae0';
	 // const MCHID = '1324583201';
	 // const KEY = '8896jjKKLITF778826465fdAERfsy752';

/* 	公众号APPID :  wx29bd74e0b911f9bf
微信支付商户号 : 1300433201
密钥： pjklASEY658cf239jdjdfhnvb76id534
AppSecret(应用密钥): 63323005ed2ba07d7a51c0ec804bc77f
Token:  Ky09sf1m6v88Wejk635gf9p52vb
EncodingAESKey:   ch4Kw8O81LfA8R2Fs95uOGlKclho5HKNshYnLwjR94j */

	//=======【证书路径设置】=====================================
	/**
	 * TODO：设置商户证书路径
	 * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
	 * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
	 * @var path
	 */
	// const SSLCERT_PATH = './cert/apiclient_cert.pem';
	// const SSLKEY_PATH = './cert/apiclient_key.pem';

	//=======【curl代理设置】===================================
	/**
	 * TODO：这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
	 * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
	 * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
	 * @var unknown_type
	 */
	// const CURL_PROXY_HOST = "0.0.0.0";//"10.152.18.220";
	// const CURL_PROXY_PORT = 0;//8080;

	//=======【上报信息配置】===================================
	/**
	 * TODO：接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
	 * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
	 * 开启错误上报。
	 * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
	 * @var int
	 */
	// const REPORT_LEVENL = 1;
//}

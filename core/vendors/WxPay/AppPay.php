<?php
/**
* APP支付实现类 自定义，非官方文档
* @author liang
* @time 2016-03-24
*/
class AppPay
{
	/**
	*
	* 参数数组转换为url参数
	* @param array $urlObj
	*/
	private function ToUrlParams($urlObj)
	{
		$buff = "";
		foreach ($urlObj as $k => $v)
		{
			$buff .= $k . "=" . $v . "&";
		}

		$buff = trim($buff, "&");
		return $buff;
	}



	/**
	*
	* 生成直接支付url，支付url有效期为2小时,模式二
	* @param UnifiedOrderInput $input
	*/
	public function GetPayPrepayId($input)
	{
		if($input->GetTrade_type()=="APP")
		{
			$result = WxPayApi::unifiedOrder($input);
			return $result;
		}
	}

	/*生成APP提交数据*/

	public function GetAppApiParameters($UnifiedOrderResult)
	{

		if(!array_key_exists("appid", $UnifiedOrderResult) || !array_key_exists("prepay_id", $UnifiedOrderResult) || $UnifiedOrderResult['prepay_id'] == "")
		{
			throw new WxPayException("参数错误");
		}

		$appapi = new WxPayAppApiPay();
		$appapi->SetAppid($UnifiedOrderResult["appid"]);
		$appapi->SetPartnerId($UnifiedOrderResult["mch_id"]);
		$appapi->SetPrepayId($UnifiedOrderResult["prepay_id"]);

		$timeStamp = time();
		$appapi->SetTimeStamp($timeStamp);

		$appapi->SetNonce_str(WxPayApi::getNonceStr());
		$appapi->SetPackage("Sign=WXPay");
		$appapi->SetSign($appapi->MakeSign());

		$back_arr=$appapi->GetValues();

		$back_arr['prepay_id']=$UnifiedOrderResult["prepay_id"];
		$parameters = json_encode($appapi->GetValues());
		return $parameters;
	}
}


/**
 * APP支付数据组装
 */
class WxPayAppApiPay extends WxPayDataBase
{
	/**
	* 设置微信分配的公众账号ID
	* @param string $value
	**/
	public function SetAppid($value)
	{
		$this->values['appid'] = $value;
	}
	/**
	* 获取微信分配的公众账号ID的值
	* @return 值
	**/
	public function GetAppid()
	{
		return $this->values['appid'];
	}
	/**
	* 判断微信分配的公众账号ID是否存在
	* @return true 或 false
	**/
	public function IsAppidSet()
	{
		return array_key_exists('appid', $this->values);
	}


	/**
	* 设置微信支付分配的商户号
	* @param string $value
	**/
	public function SetPartnerId($value)
	{
		$this->values['partnerid'] = $value;
	}
	/**
	* 获取微信支付分配的商户号的值
	* @return 值
	**/
	public function GetPartnerId()
	{
		return $this->values['partnerid'];
	}
	/**
	* 判断微信支付分配的商户号是否存在
	* @return true 或 false
	**/
	public function IsPartnerIdSet()
	{
		return array_key_exists('partnerid', $this->values);
	}


	/**
	* 设置package
	* @param string $value
	**/
	public function SetPackage($value)
	{
		$this->values['package'] = $value;
	}
	/**
	* 获取package
	* @return 值
	**/
	public function GetPackage()
	{
		return $this->values['package'];
	}
	/**
	* 判断package
	* @return true 或 false
	**/
	public function IsPackageSet()
	{
		return array_key_exists('package', $this->values);
	}


	/**
	* 设置微信生成的预支付单号。
	* @param string $value
	**/
	public function SetPrepayId($value)
	{
		$this->values['prepayid'] = $value;
	}
	/**
	* 获取微信生成的预支付单号。
	* @return 值
	**/
	public function GetPrepayId()
	{
		return $this->values['prepayid'];
	}
	/**
	* 判断微信生成的预支付单号是否存在
	* @return true 或 false
	**/
	public function IsPrepayIdSet()
	{
		return array_key_exists('prepayid', $this->values);
	}


	/**
	* 设置随机字符串，不长于32位。推荐随机数生成算法
	* @param string $value
	**/
	public function SetNonce_str($value)
	{
		$this->values['noncestr'] = $value;
	}
	/**
	* 获取随机字符串，不长于32位。推荐随机数生成算法的值
	* @return 值
	**/
	public function GetNonce_str()
	{
		return $this->values['noncestr'];
	}
	/**
	* 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
	* @return true 或 false
	**/
	public function IsNonce_strSet()
	{
		return array_key_exists('noncestr', $this->values);
	}

	/**
	* 设置时间
	* @param string $value
	**/
	public function SetTimeStamp($value)
	{
		$this->values['timestamp'] = $value;
	}
	/**
	* 获取时间
	* @return 值
	**/
	public function GetTimeStamp()
	{
		return $this->values['timestamp'];
	}
	/**
	* 判断时间是否存在
	* @return true 或 false
	**/
	public function IsTimeStamp()
	{
		return array_key_exists('timestamp', $this->values);
	}

}
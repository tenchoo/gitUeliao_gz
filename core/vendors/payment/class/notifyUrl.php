<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>


<?php

	/*
		windows 环境打开openssl方法：
		1、在php.ini中 
		extension=php_openssl.dll去掉前面的注释 
		2、复制php安装目录中的： 
		libeay32.dll 
		ssleay32.dll 
		至c:\windows\system32 
		3、复制php_openssl.dll至c:\windows\system32 
		4、重启php
	*/



	class Rsa{
		var $publicKey;
		var $debugInfo;
		
		/**
		初始化秘钥
		*/
		function initKey(){
			$content = file_get_contents('d:/wamp/cert/cacert.pem');
			$this->publicKey = openssl_pkey_get_public($content);
		}
		
		/**
		签名验证
		*/
		function getSslVerify($data,$sign){
			$sig = base64_decode($sign);
			if (openssl_verify($data, $sig, $this->publicKey) == 1){
				$this->debugInfo = "签名验证成功";
				return true;
			}
			$this->debugInfo = "签名验证失败";
			return false;
		}
	}
?>


<?php
	
	$rsa = new Rsa();
	$rsa -> initKey();
	
	//获取表单中的 XML 数据
	$xmlData	= base64_decode($_POST['xml']);
	
	//获取签名数据
	if($rsa->getSslVerify($xmlData,$_POST['sign'])){
		//如果验签成功执行
		$simpleXmlData = simplexml_load_string($xmlData);
		$attributes = $simpleXmlData->attributes();
		$businessCode = (string)$attributes['code'];
		echo $rsa->debugInfo.'<br/>';
		echo '交易码:'.$businessCode.'<br/>';
		
		$attributes = $simpleXmlData->xpath("group/data[@name='NotifyTyp']");
		$attributes = $attributes[0]->attributes();
		$notifyTyp = (string)$attributes['value'];
		echo '通知类型:';
		if($notifyTyp == "0")
		{
			echo '付款';
		}else if($notifyTyp == "1")
		{
			echo '退款';
		}
		echo '<br/>';
		
		
		$attributes = $simpleXmlData->xpath("group/data[@name='ActDat']");
		$attributes = $attributes[0]->attributes();
		$actDat = (string)$attributes['value'];
		echo '通知时间:'.$actDat.'<br/>';
		
		
		$attributes = $simpleXmlData->xpath("group/data[@name='payOrdNo']");
		$attributes = $attributes[0]->attributes();
		$payOrdNo= (string)$attributes['value'];
		echo '支付系统交易号:'.$payOrdNo.'<br/>';
		
		$attributes = $simpleXmlData->xpath("group/data[@name='merOrderId']");
		$attributes = $attributes[0]->attributes();
		$merOrderId= (string)$attributes['value'];
		echo '外部交易号:'.$merOrderId.'<br/>';
		
		$attributes = $simpleXmlData->xpath("group/data[@name='merOrderAmt']");
		$attributes = $attributes[0]->attributes();
		$merOrderAmt= (string)$attributes['value'];
		echo '订单总金额:'.$merOrderAmt.'<br/>';
		
		
		$attributes = $simpleXmlData->xpath("group/data[@name='merOrderStatus']");
		$attributes = $attributes[0]->attributes();
		$merOrderStatus = (string)$attributes['value'];
		echo '交易状态:';
		if($merOrderStatus == "00"){
			echo '成功';
		}else if($merOrderStatus == "00"){
			echo '失败';
		}
		echo '<br/>';
		
		
		$attributes = $simpleXmlData->xpath("group/data[@name='merNo']");
		$attributes = $attributes[0]->attributes();
		$merNo = (string)$attributes['value'];
		echo '卖家ID:'.$merNo.'<br/>';
	}else{
		//如果验签失败执行
		echo $rsa->debugInfo;
		return;
	}
?>
</body>
</html>


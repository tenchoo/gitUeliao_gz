<?php
/**
 * 发送手机验证码
 * @author morven
 * @package SendCode
 */
class PhoneCode extends SendCode {

	public $mobilesms;



	/**
	 *  发送
	 * @param string $account 账号
	 * @param string $data 发送数据
	 * @param int $type 类型0:邮件1:手机
	 */
	public function send($account,$data=null,$state=1){
		//保存数据
		$return = $this->sendSMS($account,$data);
		if(is_array($data)){
			$str = $data['body'];
		}else{
			$str = $data;
		}
		$this->save( $account, $str,$state );
		return true;
	}

	/**
	 * 发送短信
	 * @param int $mobile 手机号码
	 * @param string $content 短信内容
	 * @param string $time　时间
	 * @param unknown_type $mid
	 * @return boolean
	 */
	function sendSMS($mobile,$content,$time='',$mid='')
	{
		// $value = Yii::app()->params['mobconfig'];//取手机配置
		// $mobilesms = Yii::app()->params['mobilesms'];//取网关类型

		$config =  tbConfig::model()->get('mobile_sms');
		$value = unserialize($config);
		$this->mobilesms = $mobilesms = $value['type'];

		$http = $value['Host'];
		if($value['md5']=='true'){
			$pwd = md5($value['password']);
		}else{
			$pwd = $value['password'];
		}
		if($mobilesms==1){

			$data = array
			(
					'action'=>'sendsms',
					'username'=>$value['username'],					//用户账号
					'userpwd'=>$pwd,	               //MD5位32密码
					'mobiles'=>$mobile,				//号码
					'content'=>$content.'[步云科技]',			//内容
					'timing'=>$time,		                //发送定时时间，可以为空，格式为yyyyMMddHHmmss，总共是14个字
					//'extid'=>$mid,					//子扩展号
					//'encode'=>Yii::app()->charset  //编码
			);
			$re = $this->postSMS($http,$data);			//POST方式提交
		}elseif($mobilesms==2){//商脉
			$data = array(
				'sn'=>$value['username'], ////替换成您自己的序列号
				 'pwd'=>strtoupper($pwd), //直接填写客服提供的序列号密码(SDK-DLS-010-XXXXXX)为序列号,(XXXXXX)为密码
				 'mobile'=>$mobile,//手机号 多个用英文的逗号隔开 post理论没有长度限制.推荐群发一次小于等于10000个手机号
				 'content'=>iconv( "UTF-8", "gb2312//IGNORE" ,$content),
				 'ext'=>'',
				 'rrid'=>'',//默认空 如果空返回系统生成的标识串 如果传值保证值唯一 成功则返回传入的值
				 'stime'=>''//定时时间 格式为2011-6-29 11:09:21
			);
			$re = $this->postSMS($http,$data);			//POST方式提交
		}elseif($mobilesms==3){
			$data = array(
					'account'=>$value['username'], ////账号
					'password'=>$pwd, //提交账户密码 （可以明文密码或使用32位MD5加密）
					'mobile'=>$mobile,//接收号码，只能提交1个号码
					'content'=>$content,	//信息内容,通常为67汉字以内，超过限制字数会被分拆，同时扣费会被累计
			);

			$re = $this->post($data,$http);			//POST方式提交
		}
		if( trim($re) >= '1' ){
			return true;//发送成功
		}
		else{
			return false;//发送失败
		}

	}
	/**
	 * 以POST格式发送数据
	 * @param srting $url POST地址
	 * @param array $data 提交数据
	 * @return string
	 */
	function postSMS($url,$data='')
	{
		$row = parse_url($url);
		$host = $row['host'];
		$port = !empty($row['port']) ? $row['port']:'80';
		$file = $row['path'];
		$flag = 0;
		$post='';
		foreach ((array)$data as $key=>$value) {
			if ($flag!=0) {
				$post .= "&";
				$flag = 1;
			}
			$post.= $key."=";
			$post.= urlencode($value);
			$flag = 1;
		}
		$len = strlen($post);
		$fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
		if (!$fp) {
			return "$errstr ($errno)\n";
		} else {
			$receive = '';
			$out = "POST $file HTTP/1.1\r\n";
			$out .= "Host: $host\r\n";
			$out .= "Content-type: application/x-www-form-urlencoded\r\n";
			$out .= "Connection: Close\r\n";
			$out .= "Content-Length: $len\r\n\r\n";
			$out .= $post."\r\n";//添加post的字符串
			fputs($fp,$out); //发送post的数据
			$inheader = 1;
			while (!feof($fp)) {
				$receive = fgets($fp,1024); //去除请求包的头只显示页面的返回数据
			}
			fclose($fp);
			$receive = $this->Handle($receive);
			return $receive;
		}
	}

	/**
	 * 返回结果处理
	 * @param unknown $data 接口返回数据
	 */
	function Handle($data){
		//$mobilesms = Yii::app()->params['mobilesms'];//取网关类型
		$return = 0;
		switch ($this->mobilesms) {
			case 1:
				 if($data>=1){
				 	$return = 1;
				 }
			break;
			case 2:
		 		if($data>=1){
				 	$return = 1;
				 }
				break;
			case 3:
				$data = $this->xml_to_array($data);
				if(isset( $data['SubmitResult']['code'] ) && $data['SubmitResult']['code']==2){
					$return = 1;
				}else{
					$return = -1;
				}
				break;
			default:
				;
			break;
		}

		return $return;
	}

	/**
	 * 处理XML为ARRAY
	 * @param unknown $xml
	 * @return unknown
	 */
	function xml_to_array($xml){
		$reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
		$arr = '';
		if(preg_match_all($reg, $xml, $matches)){
			$count = count($matches[0]);
			for($i = 0; $i < $count; $i++){
				$subxml= $matches[2][$i];
				$key = $matches[1][$i];
				if(preg_match( $reg, $subxml )){
					$arr[$key] = $this->xml_to_array( $subxml );
				}else{
					$arr[$key] = $subxml;
				}
			}
		}
		return $arr;
	}

	function post($curlPost,$url){
		$flag = 0;
		$post='';
		foreach ((array)$curlPost as $key=>$value) {
			if ($flag!=0) {
				$post .= "&";
				$flag = 1;
			}
			$post.= $key."=";
			$post.= urlencode($value);
			$flag = 1;
		}
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_NOBODY, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
		$return_str = curl_exec($curl);
		curl_close($curl);
		$receive = $this->Handle($return_str);
		return $receive;
	}

}

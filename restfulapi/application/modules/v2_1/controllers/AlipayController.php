<?php

class AlipayController extends CController
{
	public function init(){
		parent::init();
		Yii::import('libs.vendors.payment.*');
	}

	// 支付宝服务器异步通知页面路径
	public function actionNotify() {
		$info = empty($_POST)?$_GET:$_POST;
		$method = empty($_POST)?'GET':'POST';

		//写回调日志
		Yii::log( "call back: method:$method \n time:".date('Y-m-d H:i:s'). json_encode($info),  CLogger::LEVEL_INFO, 'alipay.Notify');

		if( !array_key_exists ('out_trade_no',$info) ){
			//写回调日志
			Yii::log( 'exit : fail',  CLogger::LEVEL_INFO, 'alipay.Notify');
			exit('fail');
		}

		$state = 'fail';
		$trade_status = $info['trade_status'];//返回状态

		if( $trade_status != 'TRADE_SUCCESS'){
			$result = '支付状态为：'.$trade_status;
			goto end;
		}



		$trade_no = $info['trade_no'];//交易号
		$total_fee = $info['total_fee'];//返回金额
		$orders = $this->loadModel( $info['out_trade_no'] );

		if( !$orders ){
			$result = '交易号不存在';
			goto end;
		}
		if( $total_fee != $orders->price ){
			$result = '交易失败！订单金额与付款金额不相同';
			goto end;
		}

		$payForm = new OrderPayForm();
		$result = $payForm -> onlinePay( $orders ,$trade_status,$trade_no);
		if( $result == 'true' ){
			$state = $result = 'success';
		}else{
			$result = 'fail,交易失败！';
		}

		end:
		echo $state;

		Yii::log( "\n return '" . $state.' '.$result . "';\n",  CLogger::LEVEL_INFO, 'alipay.Notify');
		exit;

		$handle = fopen(Yii::app()->basePath.'../../alipay.txt', "wb");
		$info = empty($_POST)?$_POST:$_GET;
   		fwrite($handle,"<?php \n return array(\n\t'" . implode("',\n\t'", $info) . "'\n);\n?>");
   		fclose($handle);
   		//print_r($_GET);
   		//exit;

		$orderid = $_POST['out_trade_no'];//订单号
		$time = time();
		$orders = $this->loadModel($orderid);
		//实例化支付类
		$alipay = new $orders->payment->class_name();
		//配置
		$alipay->pay_config = $orders->payment->config;


		if ($alipay->verifyNotify()) {
			$order_id = $_POST['out_trade_no'];//商户订单号
			$trade_no = $_POST['trade_no'];//支付宝交易号
			$total_fee = $_POST['total_fee'];//
			//交易状态
			$trade_status = $_POST['trade_status'];
			if($orders->state　>=　2){
				echo 'success';
				exit;
			}
			if( $total_fee!= $orders->amount ){
				echo "fail";
			}
			if($alipay->pay_config['class_name']=='AlipayEscow'){
				if($trade_status == 'WAIT_SELLER_SEND_GOODS') {
					$this->updateOrderStatus($order_id,2,$trade_no,$total_fee);
					echo 'success';
				}else if($trade_status == 'WAIT_BUYER_CONFIRM_GOODS') {

					//该判断表示卖家已经发了货，但买家还没有做确认收货的操作
					$this->updateOrderStatus($order_id,4);
					dump($alipay);exit;
				}else if($trade_status == 'TRADE_FINISHED') {
					//该判断表示买家已经确认收货，这笔交易完成
					$this->updateOrderStatus($order_id,5);
					echo 'success';
				}else {
					$this->updateOrderStatus($order_id,2,$trade_no,$total_fee);
					echo 'success';
				}
			}else{
				if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS'|| $trade_status == 'WAIT_SELLER_SEND_GOODS') {	//判断即时到帐


					$this->updateOrderStatus($order_id,2,$trade_no,$total_fee);
					echo 'success';
				}else {
					$this->updateOrderStatus($order_id,2,$trade_no,$total_fee);
					echo 'success';
				}
			}
		} else {
			echo "fail";
            exit();
		}
	}

	//支付宝页面跳转同步通知页面路径
	public function actionReturn() {
		if( !empty( $_GET )) {
			//写回调日志
			Yii::log( "actionReturn call back: method:$method \n time:".date('Y-m-d H:i:s'). json_encode($_GET),  CLogger::LEVEL_INFO, 'alipay.Notify');
		}

		$orderid = $_GET['out_trade_no'];//订单号
		$orders = $this->loadModel($orderid);

		$trade_no = $_GET['trade_no'];//交易号
		$total_fee = $_GET['total_fee'];//返回金额
		$trade_status = $_GET['trade_status'];//返回状态
		$is_success = $_GET['is_success']; //是否成功

		if( $total_fee != $orders->price ){
			throw new CHttpException(404,'交易失败！订单金额与付款金额不相同');
			exit();
		}

		$payForm = new OrderPayForm();
		$result = $payForm -> onlinePay( $orders ,$trade_status,$trade_no);
		if( $result == 'true' ){
			$url=$this->createUrl('/cart/pay/success');
			$this->redirect($url);
		}else{
			throw new CHttpException(404,'交易失败！请联系客服');
		}
		exit;

		$time = time();
		//实例化支付类
		$alipay = new $orders->payment->class_name();
		//配置
		$alipay->pay_config = $orders->payment->config;
		if($orders->state　>=2){
			$url=$this->createUrl('/order/default/list');
			$this->redirect($url);
		}
		if ($alipay->verifyReturn()) {
			$trade_no = $_GET['trade_no'];//交易号
			$total_fee = $_GET['total_fee'];//返回金额
			$trade_status = $_GET['trade_status'];//返回状态

			if( $total_fee != $orders->price ){
				throw new CHttpException(404,'交易失败！订单金额与付款金额不相同');
				exit();
			}
			if($alipay->pay_config['class_name']=='AlipayEscow'){//担保支付
				if($trade_status == 'WAIT_SELLER_SEND_GOODS') {
					$this->updateOrderStatus($orderid,2,$trade_no,$total_fee);
					$url=$this->createUrl('/order/default/list');
					$this->redirect($url);
				}else if($trade_status == 'WAIT_BUYER_CONFIRM_GOODS') {
					//该判断表示卖家已经发了货，但买家还没有做确认收货的操作
					$this->updateOrderStatus($orderid,4);
					$url=$this->createUrl('/order/default/list');
					$this->redirect($url);
				}else if($trade_status == 'TRADE_FINISHED') {
					//该判断表示买家已经确认收货，这笔交易完成
					$this->updateOrderStatus($orderid,5);
					$url=$this->createUrl('/order/default/list');
					$this->redirect($url);
					echo 'success';
				}else {
					$this->updateOrderStatus($orderid,2,$trade_no,$total_fee);
					$url=$this->createUrl('/order/default/list');
					$this->redirect($url);
				}
			}else{
				if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
					$this->updateOrderStatus($orderid,2,$trade_no,$total_fee);
					$url=$this->createUrl('/order/default/list');
					$this->redirect($url);
				}
				else {
					$this->updateOrderStatus($orderid,2,$trade_no,$total_fee);
					$url=$this->createUrl('/order/default/list');
					$this->redirect($url);
				}
			}
		} else {
			//echo "fail";
			throw new CHttpException(404,'交易失败！请联系客服');
            exit();
		}
	}

	///////////页面功能说明///////////////
	// 支付宝无线快捷支付异步通知接口
	// 创建该页面文件时，请留心该页面文件中无任何HTML代码和空格
	// 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面
	// TRADE_FINISHED(表示交易已经成功结束)
	/////////////////////////////////////
	public function actionApkNotify(){
		//获取notify_data
		//不需要解密，是明文的格式
		$notify_data = "notify_data=" . $_POST["notify_data"];
	//	$notify_data = 'notify_data=<notify><partner>2088901555002542</partner><discount>0.00</discount><payment_type>1</payment_type><subject>指易达商城购物支付款</subject><trade_no>2013081729489980</trade_no><buyer_email>zhangmenglin5@tom.com</buyer_email><gmt_create>2013-08-17 16:09:43</gmt_create><quantity>1</quantity><out_trade_no>20130817088579</out_trade_no><seller_id>2088901555002542</seller_id><trade_status>TRADE_FINISHED</trade_status><is_total_fee_adjust>N</is_total_fee_adjust><total_fee>0.01</total_fee><gmt_payment>2013-08-17 16:09:44</gmt_payment><seller_email>service@zeeeda.com</seller_email><gmt_close>2013-08-17 16:09:44</gmt_close><price>0.01</price><buyer_id>2088102015378801</buyer_id><use_coupon>N</use_coupon></notify>';

		//获取sign签名
		$sign = $_POST["sign"];
	//	$sign = 'NSPDgljMcRZBRMadEv8DcbmynZh6yoXHt/BQ7zrWWa7hKjxmwwLggpJXuRZgO00CL/Z00zgUAegzqgvfXrDYahUwotzuefCLc3BbpXOQuYRcCUELYyt3o8tJUC830XDy+Mo9fhxE/BjKvIcRsaqDn0L3QzxBAedqyLz5YoMCUGk=';
		echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';

		//实例化接口类
		$apkPay = new AlipayApk();

		//验证签名
		$isVerify = $apkPay->verify($notify_data, $sign);

		//如果验签没有通过,验证解密不通过，暂不执行
		if(!$isVerify){
			echo "fail";
			return;
		}
		else{echo "true";}

		//获取交易状态
		$trade_status = $apkPay->getDataForXML($_POST["notify_data"] , '/notify/trade_status');

		//判断交易是否完成
		if($trade_status == "TRADE_FINISHED"){
			echo "success";
			//取订单号
			$order_id = $apkPay->getDataForXML($_POST["notify_data"] , '/notify/out_trade_no');

			//支付宝交易号
			$trade_no = $apkPay->getDataForXML($_POST["notify_data"] , '/notify/trade_no');
//			echo $order_id."<br>".$trade_no;exit;
			//在此处添加您的业务逻辑，作为收到支付宝交易完成的依据
			$this->updateOrderStatus($order_id,$trade_no,2);
		}
		else{
			echo "fail";
		}
	}

	/*
     * 财付通异步通知
     */
	public function actionpayNotify()
	{
		$order_id = Yii::app()->request->getParam('out_trade_no');//订单号
		$orders = $this->loadModel($order_id);
		$product = $orders->orderProduct['0']->attributes;//取订单产品
		//判断是否多店铺
		if( F::sitetype()==2 ){
		//配置
		$pay_config = $orders->cmppayment->zpy_config;

		}else{

		//配置
		$pay_config = $orders->payment->zpy_config;

		}

		if($pay_config['class_name']!='TenpayEscow'){
			echo "fail";
		}
		//dump($pay_config['zpy_payment_id']);exit;

	/* 创建支付应答对象 */
		$resHandler = new ResponseHandler();
		$resHandler->setKey($pay_config['zpy_payment_key']);

	//判断签名
		if($resHandler->isTenpaySign()) {

	//通知id
		$notify_id = $resHandler->getParameter("notify_id");

	//通过通知ID查询，确保通知来至财付通
	//创建查询请求
		$queryReq = new RequestHandler();
		$queryReq->init();
		$queryReq->setKey($pay_config['zpy_payment_key']);
		$queryReq->setGateUrl("https://gw.tenpay.com/gateway/simpleverifynotifyid.xml");
		$queryReq->setParameter("partner", $pay_config['zpy_payment_id']);
		$queryReq->setParameter("notify_id", $notify_id);

	//通信对象
		$httpClient = new TenpayHttpClient();
		$httpClient->setTimeOut(5);
	//设置请求内容
		$httpClient->setReqContent($queryReq->getRequestURL());

	//后台调用
		if($httpClient->call()) {
	//设置结果参数
			$queryRes = new ClientResponseHandler();
			$queryRes->setContent($httpClient->getResContent());
			$queryRes->setKey($pay_config['zpy_payment_key']);

		if($resHandler->getParameter("trade_mode") == "1"){

	//判断签名及结果（即时到帐）
	//只有签名正确,retcode为0，trade_state为0才是支付成功
		if($queryRes->isTenpaySign() && $queryRes->getParameter("retcode") == "0" && $resHandler->getParameter("trade_state") == "0") {
				log_result("即时到帐验签ID成功");
	//取结果参数做业务处理
				$out_trade_no = $resHandler->getParameter("out_trade_no");
	//财付通订单号
				$transaction_id = $resHandler->getParameter("transaction_id");
	//金额,以分为单位
				$total_fee = $resHandler->getParameter("total_fee");
	//订单号
				$trade_no = $resHandler->getParameter('trade_no');//交易号
	//如果有使用折扣券，discount有值，total_fee+discount=原请求的total_fee
				$discount = $resHandler->getParameter("discount");

				//------------------------------
				//处理业务开始
				//------------------------------

				//处理数据库逻辑
				//注意交易单不要重复处理
				//注意判断返回金额
				if( $total_fee == $orders->zo_amount*100){	//判断订单金额是否相等
					$orders->zo_trade_no = $transaction_id;//写入财付通订单号
					$orders->save();

					$this->updateOrderStatus($order_id,$trade_no,2);
					OrdMsg::model()->setOrdMsg($product['zm_id'],$order_id,2);
					echo 'success';
					exit;
				}else{
					echo "fail";
					exit;
				}
				//------------------------------
				//处理业务完毕
				//------------------------------
				log_result("即时到帐后台回调成功");
				echo "success";

			} else {
	//错误时，返回结果可能没有签名，写日志trade_state、retcode、retmsg看失败详情。
	//echo "验证签名失败 或 业务错误信息:trade_state=" . $resHandler->getParameter("trade_state") . ",retcode=" . $queryRes->                         getParameter("retcode"). ",retmsg=" . $queryRes->getParameter("retmsg") . "<br/>" ;
			   log_result("即时到帐后台回调失败");
			   echo "fail";
			}
		}elseif ($resHandler->getParameter("trade_mode") == "2")

	    {
    //判断签名及结果（中介担保）
	//只有签名正确,retcode为0，trade_state为0才是支付成功
		if($queryRes->isTenpaySign() && $queryRes->getParameter("retcode") == "0" )
		{
				log_result("中介担保验签ID成功");
	//取结果参数做业务处理
				$out_trade_no = $resHandler->getParameter("out_trade_no");
	//财付通订单号
				$transaction_id = $resHandler->getParameter("transaction_id");

				//金额,以分为单位
				$total_fee = $resHandler->getParameter("total_fee");
				//------------------------------
				//处理业务开始
				//------------------------------
				$orders->zo_trade_no = $transaction_id;//写入财付通订单号
				$orders->save();
				if( $total_fee != $orders->zo_amount*100){	//判断订单金额是否相等
					echo "fail";
					exit;
				}
				//处理数据库逻辑
				//注意交易单不要重复处理
				//注意判断返回金额

			log_result("中介担保后台回调，trade_state=".$resHandler->getParameter("trade_state"));
				switch ($resHandler->getParameter("trade_state")) {
						case "0":	//付款成功
						//该判断表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货
						$this->updateOrderStatus($order_id,$trade_no,2);
						OrdMsg::model()->setOrdMsg($product['zm_id'],$order_id,2);
							break;
						case "1":	//交易创建

							break;
						case "2":	//收获地址填写完毕

							break;
						case "4":	//卖家发货成功
							//该判断表示卖家已经发了货，但买家还没有做确认收货的操作
							$orders->UpOrderStatus($order_id,4);
							OrdMsg::model()->setOrdMsg($product['zm_id'],$order_id,3);
							break;
						case "5":	//买家收货确认，交易成功
							//该判断表示买家已经确认收货，这笔交易完成
							$orders->UpOrderStatus($order_id,5);
							OrdMsg::model()->setOrdMsg($product['zm_id'],$order_id,4);
							break;
						case "6":	//交易关闭，未完成超时关闭

							break;
						case "7":	//修改交易价格成功

							break;
						case "8":	//买家发起退款

							break;
						case "9":	//退款成功

							break;
						case "10":	//退款关闭

							break;
						default:
							//nothing to do
							break;
					}


				//------------------------------
				//处理业务完毕
				//------------------------------
				echo "success";
			} else

		     {
	//错误时，返回结果可能没有签名，写日志trade_state、retcode、retmsg看失败详情。
	//echo "验证签名失败 或 业务错误信息:trade_state=" . $resHandler->getParameter("trade_state") . ",retcode=" . $queryRes->             										       getParameter("retcode"). ",retmsg=" . $queryRes->getParameter("retmsg") . "<br/>" ;
			   log_result("中介担保后台回调失败");
				echo "fail";
			 }
		  }



	//获取查询的debug信息,建议把请求、应答内容、debug信息，通信返回码写入日志，方便定位问题
	/*
		echo "<br>------------------------------------------------------<br>";
		echo "http res:" . $httpClient->getResponseCode() . "," . $httpClient->getErrInfo() . "<br>";
		echo "query req:" . htmlentities($queryReq->getRequestURL(), ENT_NOQUOTES, "GB2312") . "<br><br>";
		echo "query res:" . htmlentities($queryRes->getContent(), ENT_NOQUOTES, "GB2312") . "<br><br>";
		echo "query reqdebug:" . $queryReq->getDebugInfo() . "<br><br>" ;
		echo "query resdebug:" . $queryRes->getDebugInfo() . "<br><br>";
		*/
	}else
	 {
	//通信失败
		echo "fail";
	//后台调用通信失败,写日志，方便定位问题
	echo "<br>call err:" . $httpClient->getResponseCode() ."," . $httpClient->getErrInfo() . "<br>";
	 }


   } else
     {
    echo "<br/>" . "认证签名失败" . "<br/>";
    echo $resHandler->getDebugInfo() . "<br>";
	}

	}

	/*
     * 财付通同步通知
     */
	public function actionpayReturn()
	{

		$order_id = $_GET['out_trade_no'];//订单号

		$orders = $this->loadModel($order_id);

		$product = $orders->orderProduct['0']->attributes;//取订单产品

		//判断是否多店铺
		if( F::sitetype()==2 ){
			//实例化支付类
			$alipay = new $orders->cmppayment->class_name();
			//配置
			$alipay->pay_config = $orders->cmppayment->zpy_config;

		}else{
			//实例化支付类
			$alipay = new $orders->payment->class_name();
			//配置
			$alipay->pay_config = $orders->payment->zpy_config;

		}

		//判断签名
		if($alipay->verifyReturn()->isTenpaySign()) {

			//通知id
			$notify_id = $alipay->verifyReturn()->getParameter("notify_id");
			//商户订单号
			$out_trade_no = $alipay->verifyReturn()->getParameter("out_trade_no");
			//财付通订单号
			$transaction_id = $alipay->verifyReturn()->getParameter("transaction_id");
			//金额,以分为单位
			$total_fee = $alipay->verifyReturn()->getParameter("total_fee");
			//订单号
			$trade_no = $resHandler->getParameter('trade_no');//交易号
			//如果有使用折扣券，discount有值，total_fee+discount=原请求的total_fee
			$discount = $alipay->verifyReturn()->getParameter("discount");
			//支付结果
			$trade_state = $alipay->verifyReturn()->getParameter("trade_state");
			//交易模式,1即时到账
			$trade_mode = $alipay->verifyReturn()->getParameter("trade_mode");

			if("1" == $trade_mode ) {//即时支付
				if( "0" == $trade_state){
					if( $total_fee == $orders->zo_amount*100){	//判断订单金额是否相等
						$orders->zo_trade_no = $transaction_id;//写入财付通订单号
						$orders->save();

						$this->updateOrderStatus($order_id,$trade_no,2);
						OrdMsg::model()->setOrdMsg($product['zm_id'],$order_id,2);
						header301( url('/member/index/orders/vieworder/',array('orderid'=>$out_trade_no)) );
					}else{
						Message('交易金额与订单金额不相同！交易失败');
						exit;
					}
				} else {
					//当做不成功处理
					Message('交易失败！');
            		exit();
				}
			}elseif( "2" == $trade_mode  ) {//担保支付
				if( "0" == $trade_state) {
					if( $total_fee == $orders->zo_amount*100){	//判断订单金额是否相等
						$this->updateOrderStatus($out_trade_no,$transaction_id,2);
						OrdMsg::model()->setOrdMsg($product['zm_id'],$out_trade_no,2);
						header301( url('/member/index/orders/vieworder/',array('orderid'=>$out_trade_no)) );
					}else{
						Message('交易金额与订单金额不相同！交易失败');
						exit;
					}
				} else {
					//当做不成功处理
					Message('交易失败！');
            		exit();
				}
			}

		} else {
			Message('认证签名失败！');
            exit();
		}
	}

	/*
	 * 腾付通同步返回
	*/
	public function actionTftpay(){


	}
	/*
	 * 腾付通异步返回
	*/
	 public function actionNotifyTftpay(){

	 	$handle = fopen(Yii::app()->basePath.'../../alipay.txt', "wb");
		$info = !empty($_POST)?$_POST:$_GET;
		//dump($_POST);exit;
   		fwrite($handle,"<?php \n return array(\n\t'" . implode("',\n\t'", $info) . "'\n);\n?>");
   		fclose($handle);

	 	$priKeyPath = "/key/000000000003124.pfx";
	 	//公钥文件
	    $pubKeyPath = "/key/cacert.pem";
	    $priKeyPass = "kBUg8r";
	 	$rsa = new Rsa();
	 	$path = Yii::getPathOfAlias('ext.payment');
	 	$rsa->setPriKey($path.$priKeyPath,$priKeyPass);    //获取私钥
        $rsa->setPubKey($path.$pubKeyPath);   //获取公钥


        //if(!)exit("签名失败");
	 	//dump(1);exit;
	 	//$xml = 'PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48YnVzaW5lc3MgY29kZT0iMjcyMDAwIj48Z3JvdXA+PGRhdGEgbmFtZT0iTm90aWZ5VHlwIiB2YWx1ZT0iMCIvPjxkYXRhIG5hbWU9Im1lck9yZGVyQW10IiB2YWx1ZT0iMC4wMSIvPjxkYXRhIG5hbWU9IkFjdERhdCIgdmFsdWU9IjIwMTQxMjA0Ii8+PGRhdGEgbmFtZT0ibWVyT3JkZXJTdGF0dXMiIHZhbHVlPSIwMCIvPjxkYXRhIG5hbWU9InBheU9yZE5vIiB2YWx1ZT0iMjAxNDEyMDQwMDk2ODI2NTg5Ii8+PGRhdGEgbmFtZT0ibWVyTm8iIHZhbHVlPSI4NDY1ODQwNDgxNjIwMzgiLz48ZGF0YSBuYW1lPSJtZXJPcmRlcklkIiB2YWx1ZT0iMjAxNDEyMDQ4MzQzMjYiLz48L2dyb3VwPjwvYnVzaW5lc3M+';
	 	//$sign = 'P453IkQ25t0nRp5n3w+S8zCgPjDsbZ5+EWgmZDD+dmmxiOGWlzdJ8xA/chyfBtBgskRqAYPcb3642uF5cotwfcwfbf1L7uacwCQKjKwHwaV0np+zs9gQPFDgbwQFKknda9noN2CiOzjNuTrphfioJBQZaSPgcmkIJOol9tCG9u4AVFmyVPhjHjwpYBnHzeRSmplKMqITS6/sSo4XBNrYTf6Zwtaeyd3fRGg/MFBzSEc8+fji+tmE7IsXBzXprd6X8w51gvd6iXpV2xVMj+TYXYRTVdLnfeHd7ILsTgaaHjlGG/vSpMAqKi7YO8AcPpOT9uKjuDPQoyrc5hpyG/dj3g==';
	 	//获取表单中的 XML 数据
	 	$xmlData	= base64_decode($_POST['xml']);
	 	//dump($xmlData);exit;
	 	$rsa->getSslVerify($xmlData,$_POST['sign']);//签名
	 	//dump($path.$pubKeyPath);
	 	//dump($rsa->isContinue());
	 	//exit;
	 	//获取签名数据
	 	if($rsa->isContinue()==true){
	 		//如果验签成功执行
	 		$simpleXmlData = simplexml_load_string($xmlData);
	 		$attributes = $simpleXmlData->attributes();
	 		$businessCode = (string)$attributes['code'];
	 		/*echo $rsa->debugInfo.'<br/>';
	 		echo '交易码:'.$businessCode.'<br/>';*/

	 		$attributes = $simpleXmlData->xpath("group/data[@name='NotifyTyp']");
	 		$attributes = $attributes[0]->attributes();
	 		$notifyTyp = (string)$attributes['value'];
	 		/*echo '通知类型:';
	 		if($notifyTyp == "0")
	 		{
	 			echo '付款';
	 		}else if($notifyTyp == "1")
	 		{
	 			echo '退款';
	 		}
	 		echo '<br/>';*/


	 		$attributes = $simpleXmlData->xpath("group/data[@name='ActDat']");
	 		$attributes = $attributes[0]->attributes();
	 		$actDat = (string)$attributes['value'];
	 		/*echo '通知时间:'.$actDat.'<br/>';*/


	 		$attributes = $simpleXmlData->xpath("group/data[@name='payOrdNo']");
	 		$attributes = $attributes[0]->attributes();
	 		$payOrdNo= (string)$attributes['value'];
	 		/*echo '支付系统交易号:'.$payOrdNo.'<br/>';*/

	 		$attributes = $simpleXmlData->xpath("group/data[@name='merOrderId']");
	 		$attributes = $attributes[0]->attributes();
	 		$merOrderId= (string)$attributes['value'];
	 		/*echo '外部交易号:'.$merOrderId.'<br/>';*/

	 		$attributes = $simpleXmlData->xpath("group/data[@name='merOrderAmt']");
	 		$attributes = $attributes[0]->attributes();
	 		$merOrderAmt= (string)$attributes['value'];
	 		/*echo '订单总金额:'.$merOrderAmt.'<br/>';*/


	 		$attributes = $simpleXmlData->xpath("group/data[@name='merOrderStatus']");
	 		$attributes = $attributes[0]->attributes();
	 		$merOrderStatus = (string)$attributes['value'];
	 		/*echo '交易状态:';
	 		if($merOrderStatus == "00"){
	 			echo '成功';
	 		}else if($merOrderStatus == "00"){
	 			echo '失败';
	 		}
	 		echo '<br/>';*/


	 		$attributes = $simpleXmlData->xpath("group/data[@name='merNo']");
	 		$attributes = $attributes[0]->attributes();
	 		$merNo = (string)$attributes['value'];
	 		/*echo '卖家ID:'.$merNo.'<br/>';*/

	 		$order_id = $merOrderId;//商户订单号
	 		$trade_no = $payOrdNo;//支付宝交易号
	 		$total_fee = $merOrderAmt;//
	 		//$orders = $this->loadModel($order_id);

	 		//交易状态
		 	$order_id = substr($orderid, 0, 14);
			$memacount = new MemAccount();
			$time = time();
			$n=strpos($orderid,'-');//寻找位置
			if($n)
			{
				$str=substr($orderid,$n+1);
			}
			$orders = $this->loadModel($order_id);
			$ordp = $orders['orderProduct'];//关联订单产品表
			foreach ($ordp as $val){
					$zp_id = $val->zp_id;//产品id
				}
			if($str == 1){
					//充值订单
					$orders = Cash::model()->findByPk($order_id);
					$meminfo = tbMember::model()->findByPk($orders->zm_id);
			}elseif($str == 3){
					$orders = PaimaiApply::model()->findByAttributes(array('zpai_orderid'=>$order_id));
					$company = Comapny::model()->findByPk($orders->zm_id);
			}else{
					$orders = $this->loadModel($order_id);
					$ordp = $orders['orderProduct'];//关联订单产品表
					foreach ($ordp as $val){
						$zp_id = $val->zp_id;//产品id
					}
					$product = $orders->orderProduct['0']->attributes;//取订单产品
					$receivables = Receivables::model()->getReceinfo($order_id);//收款单信息（线上支付）
					$meminfo = tbMember::model()->findByPk($receivables->zm_id);
					if($orders->zo_status==3){
						echo '成功';
						exit;
					}
					if($orders->zo_pay_status==2){
						echo '成功';
						exit;
					}
			}
	 		if($merOrderStatus == "00"){
	 			if($str==1){ //充值
	 				if($total_fee!=$orders->zch_price){
	 					echo "支付失败,金额不正确!";
	 					$orders->zch_status = 1;
	 					$orders->zch_back_no = $trade_no;
	 					$orders->zch_re = '充值失败,支付金额与实付金额不符合!实付金额为:'.$total_fee.'应付金额为:'.$orders->zch_price;
	 					$orders->update();
	 					exit();
	 				}
	 				$orders->zch_back_no = $trade_no;
	 				$orders->zch_status = 4;
	 				$orders->update();
	 				//dump($meminfo->zm_balance);exit;
	 				$meminfo->zm_balance = $meminfo->zm_balance + $total_fee;
	 				$meminfo->update();

	 				$memacount->zm_id = $orders->zm_id;
	 				$memacount->zm_type = 1;
	 				$memacount->zmc_type = 1;
	 				$memacount->zmc_event = 2;
	 				$memacount->zmc_orderid = $order_id;
	 				$memacount->zmc_amount = $total_fee;
	 				$memacount->zmc_amount_log = $meminfo->zm_balance;
	 				$memacount->zmc_note = '充值';
	 				$memacount->inputtime = $time;

	 				$memacount->save();

	 			}elseif($str==3){//拍卖保证金

	 				$paimaiprice = $orders->zpai_deposti+$orders->zpai_paimai_ser;
	 				if($total_fee!=$paimaiprice){
	 					echo "支付失败,金额不正确!";
	 					exit();
	 				}
	 				$orders->zpai_trade_no = $trade_no;
	 				$orders->zpai_deposit_status = 2;
	 				$orders->zpai_deposit_time = $time;
	 				$orders->save();
	 				//dump($meminfo->zm_balance);exit;
	 				$company->zc_paimai = $orders->zpai_deposti;
	 				$company->zc_paimai_ser = $orders->zpai_paimai_ser;
	 				$company->save();

	 				$memacount->zm_id = $orders->zm_id;
	 				$memacount->zm_type = 1;
	 				$memacount->zmc_type = 1;
	 				$memacount->zmc_event = 2;
	 				$memacount->zmc_orderid = $order_id;
	 				$memacount->zmc_amount = $total_fee;
	 				$memacount->zmc_amount_log = $company->zc_paimai;
	 				$memacount->zmc_note = '拍卖保证金';
	 				$memacount->inputtime = $time;

	 				$memacount->save();
	 			}else{
		 			$this->updateOrderStatusOne($order_id,$trade_no,2,NULL,NULL,$total_fee);
		 			OrdMsg::model()->setOrdMsg($product['zm_id'],$order_id,2);
	 			}
	 			echo '成功';
	 		}else if($merOrderStatus == "00"){
	 			echo'支付失败！'.$rsa->debugInfo;
	 		}

		 }else{
		 	echo '支付失败！'.$rsa->debugInfo;
		 }
	 }





    function actionWxnotify(){

    	//使用通用通知接口
    	$notify = new Notify_pub();

    	//存储微信的回调
    	$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
    	$notify->saveData($xml);

    	//验证签名，并回应微信。
    	//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
    	//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
    	//尽可能提高通知的成功率，但微信不保证通知最终能成功。
    	if($notify->checkSign() == FALSE){
    		$notify->setReturnParameter("return_code","FAIL");//返回状态码
    		$notify->setReturnParameter("return_msg","签名失败");//返回信息
    	}else{
    		$notify->setReturnParameter("return_code","SUCCESS");//设置返回码
    	}
    	$returnXml = $notify->returnXml();
    	echo $returnXml;

    	//==商户根据实际情况设置相应的处理流程，此处仅作举例=======

    	//以log文件形式记录回调信息
    	$log_name="./notify_url.log";//log文件路径
    	$this->log_result($log_name,"【接收到的notify通知】:\n".$xml."\n");

    	if($notify->checkSign() == TRUE)
    	{
    		if ($notify->data["return_code"] == "FAIL") {
    			//此处应该更新一下订单状态，商户自行增删操作
    			$log_->log_result($log_name,"【通信出错】:\n".$xml."\n");
    		}
    		elseif($notify->data["result_code"] == "FAIL"){
    			//此处应该更新一下订单状态，商户自行增删操作
    			$log_->log_result($log_name,"【业务出错】:\n".$xml."\n");
    		}
    		else{
    			//此处应该更新一下订单状态，商户自行增删操作
    			//交易单号
    			$out_trade_no = $notify->data["out_trade_no"];
    			//微信单号
    			$transaction_id = $notify->data["transaction_id"];
    			//金额,以分为单位
    			$total_fee = $notify->data["total_fee"];

    			$order_id = $out_trade_no;//订单号
    			$orders = $this->loadModel($order_id);
    			$product = $orders->orderProduct['0']->attributes;//取订单产品
    			//注意判断返回金额
    			if( $total_fee == $orders->zo_amount*100){	//判断订单金额是否相等
    				$orders->zo_trade_no = $transaction_id;//写入财付通订单号
    				$orders->save();

    				$this->updateOrderStatus($out_trade_no,$transaction_id,2);
    				OrdMsg::model()->setOrdMsg($product['zm_id'],$out_trade_no,2);
    				$log_->log_result($log_name,"【支付成功】:\n".$xml."\n");
    				exit;
    			}else{
    				$log_->log_result($log_name,"【支付失败,金额不正常】:\n".$xml."\n");
    				exit;
    			}

    		}

    		//商户自行增加处理流程,
    		//例如：更新订单状态
    		//例如：数据库操作
    		//例如：推送支付完成信息
    	}
    }
    // 打印log
    function  log_result($file,$word)
    {
    	$fp = fopen($file,"a");
    	flock($fp, LOCK_EX) ;
    	fwrite($fp,"执行日期：".strftime("%Y-%m-%d-%H：%M：%S",time())."\n".$word."\n\n");
    	flock($fp, LOCK_UN);
    	fclose($fp);
    }

    /*
     * 更新订单支付状态
     * @param $orderid 订单ID
     * @param $status 状态
     * @param $trade_no 交易号
     * @param $amount 支付金额
     */
    public function updateOrderStatus($orderid,$status=null,$trade_no=null,$amount=0)
    {
    	$model=$this->loadModel($orderid);
    	// Uncomment the following line if AJAX validation is needed
    	// $this->performAjaxValidation($model);

    	if($status==2){
    		$order['state'] = $status;
    		$order['trade_no'] = $trade_no;
    		$order['payTime'] = date( 'Y-m-d H:i:s',time() );
    	}

    	if($amount>0){
    	//添加卖家收支明细
    	MemberAccount::model()->setAccountLog($model->shopId,1,0,1,1,$orderid,$amount,'');
    	//添加买家收支明细
    	MemberAccount::model()->setAccountLog($model->member,0,0,2,1,$orderid,$amount,'');
    	}

    	$OrderStorage = new OrderStorage();
    	if($status==2){
    		$OrderStorage->save($order,$orderid);
    	}
    	$OrderStorage->setState($orderid,$status);
    	return true;

    }
    /**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model = new tbOrdPayment();
		$return = $model->findbyPk( $id );
		if($return===null)
			throw new CHttpException(404,'订单不存在!');
		return $return;
	}

	/**
	 * 购物卡消费保存在优惠表中
	 * @param $zo_id	订单id
	 * @param $zp_id 	产品id
	 * @param $zod_price 购物卡消费金额
	 * @param $zop_id 产品id
	 */
	public function saveCardInfoToOrdd($zo_id,$zp_id,$zod_price)
	{
		$orddis = new OrdDiscount();

		$orddis->zo_id = $zo_id;
		$orddis->zp_id = $zp_id;
		$orddis->zod_price = $zod_price;
		$orddis->zod_type = 7;
		$orddis->zop_id = $zp_id;

		if($orddis->save()){
			return true;
		}
	}


}
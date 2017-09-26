<?php
/**
 * 申请接口调用验证
 * @author yagas
 *
 */
class TokenController extends CController {
	
	/**
	 * 对开发者进行验证
	 * @param string $account
	 * @param string $password
	 * 
	 * @output json
	 * successful: {"state":true,"data":{"tokan":"0184c61f5600cc3b2dfc"}}
	 * faild: {"state":false,"message":"帐号或密码不匹配"}
	 * 
	 * @example http://api.leather.com/token/?account=test&password=123456
	 */
	public function actionIndex($account, $password) {
		$authenticate = tbRestfulAccount::model()->authenticate($account, $password);
		list($state, $msg) = $authenticate;
		
		$json = new AjaxData($state);
		if( $state ) {
			$json->data = array('tokan' => $msg);
		}
		else {
			$json->setMessage('restful', $msg);
		}
		
		echo $json->toJson();
	}
}
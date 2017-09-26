<?php
/**
 * ajax 注册--检查账号是否可用
 * @author liang
 * @version 0.1
 * @param string account 需检查的账号
 * @package CAction
 */
class ajaxCheckaccount extends CAction {
	public function run() {
		$account = Yii::app()->request->getPost('account');
		$state = false;


		//检查账号是否合法
		if( tbMember::checkAccountValid( $account ) ) {
			//查找账号是否存在
			if( AccountFactory::findAccount( $account ) ) {
				$data['account'][0] = Yii::t ( 'reg', 'The accout already exists' );
			} else {
				$data = '';
				$state = true;
			}
		} else {
			$data['account'][0] = Yii::t ( 'reg', 'accout must be email or phone' );
		}

		$json = new AjaxData($state,null,$data);
		echo $json->toJson();
		Yii::app()->end();
	}
}
<?php
/**
 * 发送验证码
 * @author morven
 *
 */
abstract class SendCode {

	
	/**
	 *  发送
	 * @param string $account 账号
	 * @param string $data 发送数据
	 * @param int $type 类型0:邮件1:手机
	 */
	public function send($account,$data=null,$type=0){
		return true;
	}
	
	/**
	 *  保存数据
	 * @param string $account 账号
	 * @param string $data 发送数据
	 * @param int $state 状态0:失败1:成功
	 */
	public function save($account,$data=null,$state=1){		
		$model = new tbPhoneLog();
		$model->account = $account;
		$model->content = $data;		
		$model->state = $state;
		$model->createTime =date('Y-m-d H:i:s',time());
		$model->save();		
		return true;
	}
	
	/**
	 * 检查账号类型
	 * @param string $account 账号
	 * @return string
	 */
	public function checkName($account) {
		$validator = new CEmailValidator;
		$validator->allowEmpty=false;
		if ( $validator->validateValue( $account ) ){
			$type = 'mail';
		} else if( preg_match( Regexp::$mobile,$account ) ){
			$type = 'phone';
		}
	
		return $type;
	}
}

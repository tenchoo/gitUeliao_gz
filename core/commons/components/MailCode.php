<?php
/**
 * 发送邮件验证码
 * @author morven
 * @package SendCode
 */
class MailCode extends SendCode {

	
	
	/**
	 *  发送
	 * @param string $account 账号
	 * @param array $data 发送数据
	 * @param int $type 类型0:邮件1:手机
	 */
	public function send($account,$data=null,$type=0){		
		//发送邮件
		$return = $this->mail($account, $data);
		//保存数据
		$this->save($account, $data, $type);			
		return 	$return;
	}
	
	/**
	 * @param $email 接收邮件
	 * @param $data 发送数据
	 */
	public function mail($email,$data){
		//发送激活邮件....
		$mailer = Yii::app()->mailer;
		if(Yii::app()->params['mailType'] == 'smtp'){
			$mailer->IsSMTP();
			$mailer->SMTPAuth = true;
		}
		$mailer->AddReplyTo($email);
		$mailer->AddAddress($email);
		$mailer->Subject = $data['title'];		
		$mailer->Body = $data['body'];		
		if($mailer->Send()){			
			return true;
		}else{
			
			return false;
		}
	
	}
}

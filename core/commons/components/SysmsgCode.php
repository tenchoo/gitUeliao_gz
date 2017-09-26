<?php
/**
 * 发送系统消息
 * @author morven
 * @package SendCode
 */
class SysmsgCode extends SendCode {

	
	
	/**
	 *  发送
	 * @param string $account 账号
	 * @param array $data 发送数据
	 * @param int $type 类型0:邮件1:手机3:系统消息
	 */
	public function send($account,$data=null,$type=0){		
		//发送邮件		
		$return = $this->systemMsg($account, $data);
		//保存数据
		$this->save($account, $data, $type);			
		return 	$return;
	}
	
	/**
	 * @param $account 接收账号
	 * @param $data 发送数据
	 */
	public function systemMsg($account,$data){		
	
		$type = $this->checkName($account);		
		if( $type=='phone' ){
			$member = tbMember::model()->find('phone=:phone',array(':phone'=>$account));
		}else{
			$member = tbMember::model()->find('email=:email',array(':email'=>$account));
		}
		$model = new Message();
		$model->memberId = $member->memberId;
		$model->title = $data['title'];
		$model->content = $data['body'];
		$model->state = 1;//状态:0::未查看1:已查看2:删除		
		if($model->save()){
			return true;
		}else{
			return false;
		}
			
	
	}
}

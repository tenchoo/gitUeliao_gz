<?php
/**
 * 发送消息
 * @author morven
 * @package sendCode
 */
class SendMessage {
	//信息标题
	public $title ;
	//信息内容
	public $body ;
	//信息类型
	public $type = 'mail';

	private $instance;

	public function __construct($body,$title=null) {
			$this->title = $title;
			$this->body = $body;
	}
	/**
	 * 创建实例类
	 * @param string $type 调用实例类型
	 * @throws CHttpException
	 */
	public function create() {
		$className = ucfirst($this->type) . 'Code';
		if( class_exists($className,true) ) {
			$f = new ReflectionClass( $className );
			$this->instance = $f->newInstance();
			return;
		}
		throw new CHttpException(500,'No Class Instance');
	}

	/**
	 * 发送信息
	 * @param string $account 账号
	 * @return true/false
	 *
	 */
	public function send($account){
		
		$this->create();
		$type = $this->instance->checkName($account);
		if( $type=='phone' ){
			$data = $this->body;
		}else{
			$data = array();
			$data['title'] = $this->title;
			$data['body']  = $this->body;
		}		
		$result = $this->instance->send($account,$data);
		return $result;
	}

}

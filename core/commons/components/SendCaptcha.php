<?php
/**
 * 发送验证码
 * @author morven
 *
 */
class SendCaptcha {

	public $title ;

	public $body ;

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
	public function create($type) {
		$className = ucfirst($type) . 'Code';
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

		$type = $this->checkName($account);
		if( $type=='phone' ){
			$data = $this->body;
		}else{
			$data = array();
			$data['title'] = $this->title;
			$data['body']  = $this->body;
		}
		$this->create($type);
		$result = $this->instance->send($account,$data);
		return $result;
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

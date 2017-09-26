<?php
/**
 * ajax数据格式
 * 用于标准化返回json数据
 * @author yagas
 * @version 2.0
 */
class AjaxData {
	
	private $_callback = array(
		'state'    => null,
		'message'   => null,
		'data'      => null,
		'errorCode' => null,
	);
	
	/**
	 * 初始化数据
	 * @param mixed $return
	 * @param string|array $message
	 * @param string|array $data
	 * @param integer $errorCode
	 */
	public function __construct( $return, $message=null, $data=null, $errorCode=null ) {
		$this->_callback['state']    = $return;
		$this->_callback['data']      = $data;
		$this->_callback['errorCode'] = $errorCode;
		$this->setMessage( $message );
	}
	
	public function attributeNames() {
		return array();
	}
	
	/**
	 * 数据转换为json格式
	 */
	public function toJson() {
		foreach( $this->_callback as $key=>$val ) {
			if( is_null($val) ) {
				unset( $this->_callback[$key] );
			}
		}
		
		$body     = json_encode( $this->_callback );
		$callback = Yii::app()->request->getQuery('callback');
		if( !is_null( $callback ) ) {
			$body = sprintf( "%s(%s);", $callback, $body );
		}
		return $body;
	}
	
	/**
	 * 返回数据数组
	 */
	public function toArray() {
		foreach( $this->_callback as $key=>$val ) {
			if( is_null($val) ) {
				unset( $this->_callback[$key] );
			}
		}
		return $this->_callback;
	}
	
	public function __set( $name, $value ) {
		if( $name == 'message' ) {
			return $this->setMessage($value);
		}

		if( array_key_exists($name,$this->_callback) ) {
			$this->_callback[$name] = $value;
		}
	}
	
	/**
	 * 设置信息
	 * @param array|string $value
	 */
	public function setMessage( $value ) {
		if( func_num_args() == 2 ) {
			$value = func_get_args();
		}		
		if( is_array($value) ) {
			list($lang,$message) = $value;
			$this->_callback['message'] = Yii::t( $lang, $message );
		}
		else {
			$this->_callback['message'] = $value;
		}
	}
}
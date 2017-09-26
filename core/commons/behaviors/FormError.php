<?php
class FormError extends CBehavior {
	
	protected $_error;
	
	/**
	 * 输出错误信息
	 * @param string $key 字段名称
	 * @param string $tag 错误标记html元素，默认值:直接输出文本
	 * @return NULL
	 */
	public function showError( $key, $tag='' ) {
		if( is_null($key) || empty($key) || empty($this->_error) ) {
			goto toEnd;
		}
	
		if( array_key_exists($key, $this->_error) ) {
			$item = $this->_error[$key];
			
			if( is_array($item) ) {
				//读取第一个错误
				$message = Yii::t( 'forms', array_shift($item) );
			}
			else {
				$message = $item;
			}
			if( empty($tag) ) {
				echo htmlspecialchars( $message );
				return true;
			}
			echo CHtml::tag( $tag, array('class'=>'error'), $message );
		}
	
		toEnd:
		return null;
	}
	
	/**
	 * 设置错误信息
	 * @param array $errors
	 */
	public function setError( $errors ) {
		$this->_error = $errors;
	}
	
	/**
	 * 提取每个字段的首页个错误信息
	 * @param array $error
	 * @return array
	 */
	public function errorOnce( $error ) {
		if( is_array($error) && $error ) {
			$newError = array();
			foreach ( $error as $item => $value ) {
				$newError[$item] = array_shift($value);
			}
			return $newError;
		}
		return array();
	}
	
	/**
	 * 获取第一个错误信息
	 * @return string
	 */
	public function getError() {
		if( $this->_error ) {
			$result = array_shift( $this->_error );
			if( is_array($result) ) {
				$result = array_shift( $result );
			}
			return $result;
		}
		return null;
	}
}
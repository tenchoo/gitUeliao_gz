<?php
/**
 * 指易达访问请求帮手类
 * 处理访问请求相关的参数提取，过滤，转化相关的操作
 * @author yagas
 * @package CBehavior
 * @version 0.1
 */
class ZRequestHelper extends CBehavior {
	
	private $_params;
	
	/**
	 * 提取url地址参数
	 * @param string $url 链接地址
	 * @return array
	 */
	public function parse_params( $url ) {
		if( is_null($this->_params) ) {
			$parse = parse_url( $url );
			if( !isset($parse['query']) ) {
				$this->_params = array();
			}
			else {
				parse_str( $parse['query'], $this->_params );
			}
		}
		return $this->_params;
	}
}
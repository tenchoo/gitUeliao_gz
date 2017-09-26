<?php
class ZHttpRequest extends CHttpRequest {
	/**
	 * 覆盖判断是否ajax请求的算法以支持跨域请求
	 * @see CHttpRequest::getIsAjaxRequest()
	 */
	public function getIsAjaxRequest() {
		$state = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';
		if( !$state && isset($_GET['callback']) ) {
			return true;
		}
		return $state;
	}
	
	/**
	 * 获取所有的POST请求数据
	 * @param string $filter 需要过滤的字段
	 * @return array
	 */
	public function getPosts( $filter=null ) {
		$post = $_POST;
		if( !is_null($filter) ) {
			foreach ( $filter as $field ) {
				unset( $post[$field] );
			}
		}
		return $post;
	}
}
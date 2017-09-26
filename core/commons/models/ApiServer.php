<?php
/**
 * 内部调用接口服务端模型
 * @author yagasx
 * @version 0.1.2
 * @package CModel
 */
class ApiServer extends CController {
	
	public $layout = false;
	
	/**
	 * 生成请求密钥
	 * @param array $data
	 * @throws CException
	 * @return string
	 */
	static public function signKey( $data ) {
		if( is_array($data) ) {
			$uri = http_build_query($data,'','&');
			return md5( $uri . SECURITY_MASK );
		}
		throw new CException("Invalid request data");
	}
	
	/**
	 * 对请求进行安全验证
	 */
	protected final function checkSecurty() {
		$sign = Yii::app()->request->getPost( "sign" );
		if( !is_null( $sign ) ) {
			unset($_POST['sign']);
			if( self::signKey( $_POST ) !== $sign ) {
				return true;
			}
		}

		echo "Forbidden: You don't have permission.";
		Yii::app()->end(403);
	}
	
	/**
	 * 获取项目的路由地址
	 */
	public function actionCreateUrl() {
		$route  = Yii::app()->request->getPost( 'route' );
		$params = Yii::app()->request->getPost( 'params', array() );
		$url = $this->createUrl( $route, $params );
		echo Yii::app()->request->hostInfo, $url;
		Yii::app()->end(200);
	}
}
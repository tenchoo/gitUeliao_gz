<?php
/**
 * 项目内部服务接口
 * @author yagas
 * @package CController
 */
class ZService extends CController {
	public $layout = false;
	
	/**
	 * 获取项目的路由地址
	 */
	public function actionCreateUrl() {
		$params = $_POST;
		$route  = $params['route'];
		$params = $this->removeFields( $params, array('route','sign') );		
		echo Yii::app()->request->hostInfo,$this->createUrl( $route, $params );
	}
	
	/**
	 * 请求安全性验证
	 * @see CController::beforeAction()
	 */
	public function beforeAction( $action ) {
		if ( SECURITY_ENABLE == FALSE ) {
			return true;
		}
		
		$securty = Yii::app()->request->getPost('sign');
		$post    = $this->removeFields( $_POST, array('sign') );
		$mask    = self::signKey( $post );

		if( $securty !== $mask ) {
			echo "Forbidden: You don't have permission.";
			Yii::app()->end(403);
	}
	return true;
	}
	
	/**
	 * 生成请求密钥
	 * @param array $data
	 * @throws CException
	 * @return string
	 */
	static public function signKey( $data ) {
		if( is_array($data) ) {
			$uri = "";
			foreach ($data as $key => $val) {
				$uri .= "&".$key.'='.$val;
			}
			return md5( substr($uri,1) . SECURITY_MASK );
		}
		throw new CException("Invalid request data");
	}
	
	/**
	 * 清除数组中的无用字段
	 * @param array $array
	 * @param array $fields
	 * @return array
	 */
	protected function removeFields( $array, $fields ) {
		foreach ( $fields as $item ) {
			unset( $array[$item] );
		}
		return $array;
	}
}
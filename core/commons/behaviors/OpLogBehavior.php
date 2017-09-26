<?php
class OpLogBehavior extends CBehavior {

	/**
	 * 写入访问日志进数据库
	 * @param array $data 附加保存信息
	 */
	public function writeOpLog( array $data = array() ){
		//项目日志分开
		$objName = basename( dirname( Yii::app()->BasePath ) );
		$collection = 'viewlog_'.$objName;

		$_db = Yii::app()->mongoDB->collection($collection);

		$method = Yii::app()->request->getRequestType();
		if( Yii::app()->request->isAjaxRequest ) {
			$method = 'ajax '.$method;
		}

		$controller = $this->getOwner();
		$memberId = property_exists( $controller,'memberId' )?$controller->memberId:Yii::app()->user->id;
		$data = array_merge( array(	'memberId' => $memberId,
									'viewTime' => time(),
									'ip' => Yii::app()->request->userHostAddress,
									'method' => $method,
									'route' => Yii::app()->request->getPathInfo(),
							), $data);
	$result = $_db->save( $data );
	}
}

<?php
/**
* 根据仓库ID和关键词查找分拣员，返回数组，包含分拣员userId和真实姓名
* @access 分拣员信息
* @param integer warehouseId 所属仓库ID
* @param integer username 分拣员名字查询
*/
class PackingerAtWare extends CAction {

	public function run() {
		$warehouseId = Yii::app()->request->getQuery('warehouseId');
		$username      = Yii::app()->request->getQuery('username');

		$result = tbUserPackinger::model()->getAllByWare( $warehouseId,$username );
		return new AjaxData( true, null, $result );
	}
}
<?php
/**
 * 仓库管理
 * @author liang
 * @access 仓库管理
 */
class WarehouseModule extends CWebModule {
	
	public function init() {
		parent::init();
		Yii::import('warehouse.models.*');
	}
	
}
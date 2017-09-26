<?php
/**
 * 采购管理
 * @author liang
 * @access 采购管理
 */
class PurchaseModule extends CWebModule {
	public function init() {
		parent::init();
		Yii::import('purchase.models.*');
	}
}
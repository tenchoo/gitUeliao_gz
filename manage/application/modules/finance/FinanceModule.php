<?php
/**
 * 财务管理
 * @author liang
 * @access 财务管理
 */
class FinanceModule extends CWebModule {
	
	public function init() {
		parent::init();
		Yii::import('finance.models.*');
	}
	
}
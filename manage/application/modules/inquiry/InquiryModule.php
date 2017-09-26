<?php
/**
 * 接口管理
 * @author liang
 * @access 接口管理
 */
class InquiryModule extends CWebModule {
	
	public function init() {
		parent::init();
		Yii::import('inquiry.models.*');
	}
	
}
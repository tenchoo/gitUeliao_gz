<?php
/**
 * 内容管理
 * @author liang
 * @access 内容管理
 */
class ContentModule extends CWebModule {
	
	public function init() {
		parent::init();
		Yii::import('content.models.*');
	}
	
}
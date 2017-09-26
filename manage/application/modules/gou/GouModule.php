<?php
/**
 * 送货系统
 * @author liang
 * @access 送货系统
 */
class GouModule extends CWebModule {
	
	public function init() {
		parent::init();
		Yii::import('gou.models.*');
	}
	
}
<?php
/**
* 批发价申请
*/
class ApplypriceModule extends CWebModule {

	public function init() {
		parent::init();
		Yii::import('applyprice.models.*');
	}
}
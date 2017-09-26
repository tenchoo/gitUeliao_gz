<?php
class AjaxModule extends CWebModule {
	
	public function init() {
		parent::init();
		Yii::import('ajax.actions.*');
	}
}
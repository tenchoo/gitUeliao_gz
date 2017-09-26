<?php
class OrderModule extends CWebModule {
	
	public function init() {
		parent::init();
		Yii::import('order.models.*');
	}
}
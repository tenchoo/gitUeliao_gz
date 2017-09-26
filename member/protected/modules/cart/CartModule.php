<?php
class CartModule extends CWebModule {
	
	public function init() {
		parent::init();
		Yii::import('cart.models.*');
	}
}
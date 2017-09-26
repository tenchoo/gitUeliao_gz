<?php
/**
 * 尾货频道管理
 * @author liang
 * @access 尾货频道管理
 * @package CWebModule
 * @version 0.1
 */
class TailgoodsModule extends CWebModule {

	public function init() {
		parent::init();
		Yii::import('tailgoods.models.*');
	}
}
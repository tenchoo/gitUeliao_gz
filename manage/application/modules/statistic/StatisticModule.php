<?php
/**
 * 数据统计模块
 * @author yagas
 * @package CWebmodule
 * @subpackage MemberModule
 * @access 数据统计
 */
class StatisticModule extends CWebModule {
	
	public function init() {
		parent::init();
		Yii::import('statistic.models.*');
	}
}
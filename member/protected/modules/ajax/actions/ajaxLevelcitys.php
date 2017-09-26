<?php
/**
 * ajax 地域按等级显示
 * @author morven
 * @version 0.1
 * @param int $areaid 当前地域ID
 * @package CAction
 */
class ajaxLevelcitys extends CAction{
	public function run() {
		$data = tbArea::model()->getCache();	
		$json=new AjaxData(true,null,$data);
		echo $json->toJson();
		Yii::app()->end();
	}
}
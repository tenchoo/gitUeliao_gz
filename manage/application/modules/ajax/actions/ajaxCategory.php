<?php
/**
 * ajax 行业分类
 * @author morven
 * @version 0.1
 * @param int $areaid 当前地域ID
 * @package CAction
 */
class ajaxCategory extends CAction{
	public function run() {
		$categoryId = Yii::app()->request->getParam("categoryId");
		$categorys = tbCategory::model()->getChildrens( $categoryId );
		$json = new AjaxData( true, null, $categorys );
		echo $json->toJson();
		Yii::app()->end( 200 );
	}
}
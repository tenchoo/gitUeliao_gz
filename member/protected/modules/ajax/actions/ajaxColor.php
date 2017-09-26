<?php
/**
 * ajax 色系查找，产品详情页
 * @author liang
 * @version 0.1
 * @param int $keyword 搜索关键词
 * @package CAction
 */
class ajaxColor extends CAction{
	public function run() {
		$keyword = Yii::app()->request->getParam("keyword");
		if( $keyword ){
			
		}
		
		$model = tbSpecvalue::model()->search( $keyword );
		$data = array();
		foreach ( $model as $val ){
			$data[] = '[data-rel="'.$val->specId.':'.$val->specvalueId.'"]';;
		}
		$data = implode(',',$data);
		

		$json = new AjaxData( true, null, $data );
		echo $json->toJson();
		Yii::app()->end( 200 );
	}
}
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
		$data = array('parent'=>'','childs'=>'');
		if( is_numeric($categoryId) && $categoryId>=0 ){
			$model =  new tbCategory();
			$category = $model->findByPk( $categoryId );
			if( $category ){
				$data['parent']  =  $category->parentId;
			}
			$data['childs'] =   $model->getChildrens( $categoryId );
		}

		$json = new AjaxData( true, null, $data );
		echo $json->toJson();
		Yii::app()->end( 200 );
	}
}
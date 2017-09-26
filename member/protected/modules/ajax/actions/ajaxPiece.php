<?php
/**
 * 取得碎片信息，传碎片标识 mark 进行读取。
 * @author liang
 * @version 0.1
 * @package CAction
 */
class ajaxPiece extends CAction {

	public function run() {
		$mark = Yii::app()->request->getQuery('mark');
		if( !empty($mark) && preg_match('/^[a-zA-Z]+$/',$mark)) {
			$model = tbPiece::model()->findByAttributes( array('mark'=> $mark ) ,'t.state=0 and t.parentId > 0');
			if( $model ){
				$json = new AjaxData( true ,null,$model->content);
			}
		}

		if(!isset($json)){
			$json = new AjaxData( false, 'Not found record' );
		}

		echo $json->toJson();
		Yii::app()->end();
	}
}
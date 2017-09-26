<?php
/*
* ajax
* @access ajax
*/
class DefaultController extends CController {
	public function actionIndex(){
		$action = Yii::app()->request->getParam("action");
		$action = 'ajax'.ucfirst( strtolower($action) );
		if( class_exists( $action ) ) {
			$actionc = new $action( $this, $action );
			$actionc->run();
			Yii::app()->end(200);
		}

		$ajaxData = new ajaxData( false, 'Not found action','' );
		echo $ajaxData->toJson();
		Yii::app()->end( 200 );

	}
}
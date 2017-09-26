<?php
/*
* ajax
* @access ajax
*/
class DefaultController extends Controller {
	public function actionIndex(){
		$action = Yii::app()->request->getParam("action");
		$action = 'ajax'.ucfirst( strtolower($action) );
		if( class_exists( $action ) ) {
			$actionc = new $action( $this, $action );
			$actionc->run();
			Yii::app()->end(200);
		}

		$ajaxData = new AjaxData( false, 'Not found action','' );
		echo $ajaxData->toJson();
		Yii::app()->end( 200 );

	}
}
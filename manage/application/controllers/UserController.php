<?php
class UserController extends Controller {
	public function actionAddnew() {		
		if( Yii::app()->request->getIsPostRequest() ) {
			$this->fields = $form = Yii::app()->request->getPost( 'form' );
			$user = new tbUser();
			$user->setAttributes( $form );
			if( $user->save() ) {
				echo "successfully.";
				Yii::app()->end( 200 );
			}
			else {
				$this->setError( $user->getErrors() );
			}
		}
		
		$this->render( 'addnew' );
	}
}
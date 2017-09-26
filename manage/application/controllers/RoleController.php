<?php
class RoleController extends Controller {
	
	public function actionAddnew() {
		if( Yii::app()->request->getIsPostRequest() ) {
			$this->fields = $form = Yii::app()->request->getPost( 'form' );
			$role = new tbRole();
			$role->setAttributes( $form );
			if( $role->save() ) {
				echo "successfully.";
				Yii::app()->end( 200 );
			}
			else {
				$this->setError( $role->getErrors() );
			}
		}
		
		$this->render( 'addnew' );
	}
}
<?php
class MenuController extends Controller {
	
	public function actionIndex() {
		return $this->render( "index" );
	}
	
	public function actionAddnew() {		
		if( Yii::app()->request->getIsPostRequest() ) {
			$this->fields = $form = Yii::app()->request->getPost( 'form' );
			$menu = new tbMenu();
			$menu->setAttributes( $form );
			if( $menu->save() ) {
				echo "successfully.";
				Yii::app()->end( 200 );
			}
			else {
				$this->setError( $menu->getErrors() );
			}
		}
		
		$params = array(
			'menus'    => $this->getParentMenu(),
			'selected' => 0
		);
		
		$this->render( 'addnew', $params );
	}
	
	private function getParentMenu() {
		return array(0=>'father');
	}
}
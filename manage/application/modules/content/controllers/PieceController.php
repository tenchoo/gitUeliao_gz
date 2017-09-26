<?php
/**
 * 碎片管理
 * @access 碎片管理
 * @author liang
 * @package Controller
 * @version 0.1
 *
 */
class PieceController extends Controller {

	/**
	 * 碎片管理
	 * @access 碎片管理
	 */
	public function actionIndex() {
		if( Yii::app()->request->getIsAjaxRequest() ) {
			$id = Yii::app()->request->getQuery('pieceId');
			$list = tbPiece::getList( $id );
			$data = new AjaxData( true, null, $list );
			echo $data->toJson();
			Yii::app()->end(200);
		}
		$list = tbPiece::getList( 0 );
		$this->render( 'index',array( 'list'=>$list ) );
	}


	/**
	 * 编辑碎片
	 * @access 编辑碎片内容
	 * @throws CHttpException
	 */
	public function actionSetcontent() {
		$model = $this->getModel();
		$model->scenario = 'setcontent';
		if( Yii::app()->request->isPostRequest ) {
			$model->attributes = Yii::app()->request->getPost('data');
			if( $model->save() ) {
				$this->dealSuccess( $this->createUrl('index') );
			}else{
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}
		}
		$this->render( 'setcontent' ,$model->attributes );
	}

	/**
	* 查找碎片
	*/
	private function getModel(){
		$id = Yii::app()->request->getQuery('pieceId');

		if( is_numeric($id) && $id>1 ) {
			$model = tbPiece::model()->findByPk( $id ,'t.state=0 and t.parentId > 0');
			if( $model ) return $model;
		}

		if( Yii::app()->request->getIsAjaxRequest() ) {
			$data = new AjaxData( false, 'Not found record' );
			echo $data->toJson();
			Yii::app()->end();
		}else{
			throw new CHttpException(404,'Not found record.');
		}
	}


	/**
	 * 碎片管理--编辑页面或碎片,树形编辑
	 * @access 编辑碎片/页面
	 * @throws CHttpException
	 */
	public function actionEdit() {
		$form = Yii::app()->request->getPost('form');
		$model = tbPiece::model()->findByPk( $form['pieceId'],'state=0' );
		if( !$model ) {
			$error = new AjaxData(false,'Not found record');
			echo $error->toJson();
			Yii::app()->end(200);
		}
		$this->saveData( $model );
	}

	/**
	 * 碎片管理--新增
	 * @access 新增碎片
	 * @throws CHttpException
	 */
	public function actionAdd() {
		$model = new tbPiece();
		$this->saveData( $model );
	}

	/**
	 * 碎片管理--删除,
	 * @access 删除碎片
	 */
	public function actionDel() {
		$pieceId  =  Yii::app()->request->getPost('pieceId');
		$message = Yii::t('category','Delete failed');;
		$state = tbPiece::model()->del ( $pieceId,$message );
		$json = new AjaxData($state,$message);
		echo $json->toJson();
		Yii::app()->end();
	}

	/**
	* 保存数据
	*/
	private function saveData( $model ){
		if( Yii::app()->request->isPostRequest ) {
			$form  =  Yii::app()->request->getPost('form');
			unset( $form['pieceId'] );
			$model->attributes = $form;
			if( $model->save() ) {
				$state = true;
				$data = $model->getAttributes(array('pieceId','title','parentId'));
			}else{
				$state = false;
				$data = $model->getErrors();
			}
			$json = new AjaxData($state,null,$data);
			echo $json->toJson();
		}
		Yii::app()->end(200);
	}
}
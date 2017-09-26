<?php
/*
* @access 呆滞级别管理
*/
class LevelController extends Controller {

	/**
	* @access 呆滞级别列表
	*/
	public function actionIndex() {
		$model = tbGlassyLevel::model()->findAll( array(
			'order'=>'conditions asc',
		));
		$data['list'] = array_map( function($i){return $i->attributes;},$model);
		$this->render( 'index',$data );
	}

	/**
	* @access 新增呆滞级别
	*/
	public function actionAdd(){
		$model = new tbGlassyLevel();
		$this->addEdit( $model );
	}


	/**
	* @access 编辑呆滞级别
	*/
	public function actionEdit( $id ){
		$model = tbGlassyLevel::model()->findByPk( $id );
		if ( !$model ) {
			throw new CHttpException(404,"the require Craft has not exists.");
		}
		$this->addEdit( $model );
	}

	/**
	* @access 保存呆滞级别信息
	*/
	private function addEdit( $model ){
		if( Yii::app()->request->isPostRequest ) {
			$model->title = trim ( Yii::app()->request->getPost('title') );
			$model->conditions = trim ( Yii::app()->request->getPost('conditions') );
			$model->logo = trim ( Yii::app()->request->getPost('logo') );
			if( $model->save() ){
				$url = $this->createUrl( 'index' );
				$this->dealSuccess($url);
			}else{
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}
		}

		$this->render( 'add',$model->attributes );
	}


	/**
	* @access 删除呆滞级别
	*/
	public function actionDel( $id ){
		if( is_numeric($id) && $id>0 ){
			tbGlassyLevel::model()->deleteByPk( $id );
		}
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}
}
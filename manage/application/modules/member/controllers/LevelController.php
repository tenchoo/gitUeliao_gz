<?php
/**
 * 会员等级管理模块控制器
 * @access 会员等级
 * @author liang
 * @package Controller
 */
class LevelController extends Controller {


	/**
	 * 显示会员等级列表
	 * @access 会员等级
	 */
	public function actionIndex() {
		$keyword = Yii::app()->request->getQuery('keyword');

		$criteria = new CDbCriteria;
		if( $keyword ){
			$criteria->compare('title',$keyword,true);
		}
		$criteria->order = 'levelId ASC';

		$data = tbLevel::model()->findAll( $criteria );
		$this->render('index',array('data' =>$data, 'keyword' => $keyword));
	}

	/**
	 * 添加会员等级
	 * @access 添加会员等级
	 */
	public function actionAdd() {
		$this->savedata();
	}

	/**
	 * 添加会员等级
	 * @access 添加会员等级
	 */
	public function actionEdit() {
		$id = Yii::app()->request->getQuery('id');
		if( !is_numeric($id) || $id<1 ){
			throw new CHttpException(404,'the require obj has not exists.');
		}
		$this->savedata( $id );
	}

	/**
	 * 新增/编辑会员等级，保存数据
	 * @param integer $id 要编辑的等级ID
	 */
	private function savedata( $id = 0 ){
		if( $id ){
			$model = tbLevel::model()->findByPk( $id );
		}else{
			$model = new tbLevel();
		}

		if(empty( $model )){
			throw new CHttpException(404,'the require obj has not exists.');
		}
		if( Yii::app()->request->isPostRequest ) {
			$model->title =  Yii::app()->request->getPost('title');
			$model->logo =  Yii::app()->request->getPost('logo');
			if( $model->save() ) {
				$this->dealSuccess( $this->createUrl('index') );
			}
			else {
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}
		}
		$this->render( 'addedit' ,array('title'=>$model->title ,'logo'=>$model->logo));
	}

	/**
	 * 删除会员等级
	 * @access 删除会员等级
	 */
	public function actionDel() {
		$id = Yii::app()->request->getQuery('id');
		if( is_numeric($id) && $id >0 ){
			tbLevel::model()->deleteByPk( $id );
		}
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}
}
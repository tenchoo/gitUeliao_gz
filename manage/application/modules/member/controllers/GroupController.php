<?php
/**
 * 会员组管理模块控制器
 * @access 会员组
 * @author liang
 * @package Controller
 */
class GroupController extends Controller {


	/**
	 * 显示会员组列表
	 * @access 会员组列表
	 */
	public function actionIndex() {
		$keyword = Yii::app()->request->getQuery('keyword');

		$criteria = new CDbCriteria;
		if( $keyword ){
			$criteria->compare('title',$keyword,true);
		}
		$criteria->addCondition("groupId>1");
		$criteria->order = 'groupId ASC';

		$data = tbGroup::model()->findAll( $criteria );
		$this->render('index',array('data' =>$data, 'keyword' => $keyword));
	}

	/**
	 * 添加会员组
	 * @access 添加会员组
	 */
	public function actionAdd() {
		$this->saveGroup();
	}

	/**
	 * 添加会员组
	 * @access 添加会员组
	 */
	public function actionEdit() {
		$id = Yii::app()->request->getQuery('groupId');
		if( !is_numeric($id) || $id<1 ){
			throw new CHttpException(404,'the require obj has not exists.');
		}
		$this->saveGroup( $id );
	}

	/**
	 * 新增/编辑会员组，保存数据
	 * @param integer $id 要编辑的组ID
	 */
	private function saveGroup( $id = 0 ){
		if( $id ){
			$model = tbGroup::model()->findByPk( $id );
		}else{
			$model = new tbGroup();
		}

		if(empty( $model )){
			throw new CHttpException(404,'the require obj has not exists.');
		}
		if( Yii::app()->request->isPostRequest ) {
			$model->title =  Yii::app()->request->getPost('title');
			if( $model->save() ) {
				$this->dealSuccess( $this->createUrl('index') );
			}
			else {
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}
		}
		$this->render( 'addedit' ,array('title'=>$model->title ));
	}

	/**
	 * 删除会员组,id为1和2的组不删
	 * @access 删除会员组
	 */
	public function actionDel() {
		$id = Yii::app()->request->getQuery('id');
		if( is_numeric($id) && $id >2 ){
			tbGroup::model()->deleteByPk( $id );
		}
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}
}
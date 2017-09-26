<?php
/**
 * 产品类目管理
 * @author yagas
 * @version 0.1
 * @access 产品类目管理
 */
class DefaultController extends Controller {
	
	/**
	 * 菜单组编号
	 * @var interge
	 */
	public $index = 1;
	
	/**
	 * 校验类目ID合法性
	 * @param integer $id 类目ID
	 */
	protected function validateCategory( & $id ) {
		if( !isset($id) || !is_numeric($id) ) {
			$error = new AjaxData(false,'Invalid category id');
			echo $error->toJson();
			Yii::app()->end(200);
		}
		return true;
	}
	
	/**
	 * 获取产品记录实例
	 * @param integer $id
	 */
	protected function getCategory( $id ) {
		$category = tbCategory::model()->find( "categoryId=:cid", array(':cid'=>$id) );
		if( is_null($category) ) {
			$error = new AjaxData(false,'Not found category');
			echo $error->toJson();
			Yii::app()->end(200);
		}
		return $category;
	}

	/**
	 * 数目列表
	 * @access 类目列表
	 */
	public function actionIndex() {
		
		//获取产品类目列表
		if( Yii::app()->request->getIsAjaxRequest() ) {
			$categoryId = Yii::app()->request->getQuery('categoryId');
			$this->validateCategory( $categoryId );
			$categorys = tbCategory::model()->getChildrens( $categoryId );
			$data = new AjaxData( true, null, $categorys );
			echo $data->toJson();
			Yii::app()->end();
		}
		
		$categoryId = Yii::app()->request->getQuery('categoryId',0);
		$categorys = tbCategory::model()->getTrees( $categoryId );
		$this->render( 'index', array('categorys'=>$categorys));
	}
	
	/**
	 * 移动分类排序
	 * @access 类目排序
	 */
	public function actionMove() {
		$form = Yii::app()->request->getPost('form');		
		if( tbCategory::model()->changePosition($form['categoryId'],$form['to']) ) {
			$state = true;
		} else {
			$state = false;
		}
		$data = new AjaxData($state ,null,null);
		echo $data->toJson();
		Yii::app()->end();
	}
	
	/**
	 * 编辑类目信息
	 * @access 编辑SEO信息
	 */
	public function actionDetail() {
		$categoryId = Yii::app()->request->getQuery('categoryId');
		$this->validateCategory( $categoryId );
		$category = tbCategory::model()->findByPk($categoryId);
		if( is_null($category) ) {
			$this->forward('notice/notfound');
			Yii::app()->end();
		}
		
		if( Yii::app()->request->getIsPostRequest() ) {
			$form  = Yii::app()->request->getPost('form');
			$state = new FormState($this, $category);
			$state->setAttributes( $form );
			$state->view = 'edit';
			$state->redirect = $this->createUrl('/category/default/index',array('categoryId'=>$categoryId));
			$state->execute();
		}
		
		$this->fields = $category->getAttributes();
		$this->render( 'edit' );
	}

	/**
	 * 数据写入数据库中
	 * @access 新增类目
	 */
	public function actionWrite() {
		$form = Yii::app()->request->getPost('form');
		$this->validateCategory( $form['categoryId'] );
		
		$state = new FormState( $this, new tbCategory() );		
		$state->setAttributes( $form );		
		$state->execute();
	}
	
	/**
	 * 更新类目数据
	 * @access 编辑类目
	 */
	public function actionUpdate() {
		$form = Yii::app()->request->getPost('form');
		$this->validateCategory( $form['categoryId'] );
		$category = $this->getCategory( $form['categoryId'] );
		
		$state = new FormState( $this, $category );
		$state->setAttributes( $form );
		$state->execute();
	}
	
	/**
	 * 删除类目及其子目录
	 * @access 删除类目
	 */
	public function actionRemove() {
		if( Yii::app()->request->getIsAjaxRequest() ) {
			$categoryId = Yii::app()->request->getPost('categoryId');
			$this->validateCategory( $categoryId );
			
			$state = true;
			if( !tbCategory::model()->remove( $categoryId ) ) {
				$state = false;
			}	
			$data = new AjaxData( $state , null, null );
			echo $data->toJson();
			Yii::app()->end(200);
		}
	}
	
	/**
	 * 重建类目排序值
	 * @access hidden
	 */
	public function actionFixorder() {
		set_time_limit(0);
		tbCategory::model()->rebuildTree();
		$this->redirect( $this->createUrl('/category/default/index') );
	}
}
<?php
/**
 * 部门管理
 * @access 部门管理
 * @author yagas
 *
 */
class DepartmentController extends Controller {

	/**
	 * 部门管理
	 * @access 部门管理
	 */
	public function actionIndex() {
		$perSize = tbConfig::model()->get( 'page_size' );
		$condition['departmentName'] = trim(Yii::app()->request->getQuery('departmentName'));
		$data = tbDepartment::model()->search( $condition,$perSize );
		$this->render( 'index', array_merge($condition,$data));
	}

	/**
	 * 编辑部门
	 * @access 编辑部门
	 * @throws CHttpException
	 */
	public function actionEdit() {
		$id = Yii::app()->request->getQuery('id');
		if( is_null($id) || !is_numeric($id) || ($model = tbDepartment::model()->findByPk( $id ))==null ) {
			throw new CHttpException( '404', 'Not found record' );
		}
		$this->saveData( $model );
	}

	/**
	* 新增部门
	* @access 新增部门
	* @throws CHttpException
	*/
	public function actionAdd(){
		$model = new tbDepartment();
		$this->saveData( $model );
	}

	/**
	 * 删除部门
	 * @access 删除部门
	 */
	public function actionDel() {
		$id = Yii::app()->request->getQuery('id');
		if( is_numeric($id) && $id >0 ){
			tbDepartment::model()->updateByPk( $id,array('state'=>1) );
		}
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}

	/**
	* 保存部门数据
	*/
	private function saveData( $model ){
		if( Yii::app()->request->isPostRequest ) {
			$model->departmentName = Yii::app()->request->getPost('departmentName');
			if( $model->save() ) {
				$this->dealSuccess( $this->createUrl('index') );
			}else{
				$this->dealError( $model->getErrors() );
			}
		}
		$this->render( 'edit', array( 'departmentName'=>$model->departmentName ) );
	}
}
<?php
/**
 * 驾驶员管理
 * @access 驾驶员管理
 * @author liang
 *
 */
class DriverController extends Controller {

	/**
	 * 驾驶员管理
	 * @access 驾驶员管理
	 */
	public function actionIndex() {
		$perSize = tbConfig::model()->get( 'page_size' );
		$condition['driverName'] = trim(Yii::app()->request->getQuery('driverName'));
		$data = tbDriver::model()->search( $condition,$perSize );
		$this->render( 'index', array_merge($condition,$data));
	}

	/**
	 * 编辑驾驶员
	 * @access 编辑驾驶员
	 * @throws CHttpException
	 */
	public function actionEdit() {
		$id = Yii::app()->request->getQuery('id');
		if( is_null($id) || !is_numeric($id) || ($model = tbDriver::model()->findByPk( $id ))==null ) {
			throw new CHttpException( '404', 'Not found record' );
		}
		$this->saveData( $model );
	}

	/**
	* 新增驾驶员
	* @access 新增驾驶员
	* @throws CHttpException
	*/
	public function actionAdd(){
		$model = new tbDriver();
		$this->saveData( $model );
	}

	/**
	 * 删除驾驶员
	 * @access 删除驾驶员
	 */
	public function actionDel() {
		$id = Yii::app()->request->getQuery('id');
		if( is_numeric($id) && $id >0 ){
			tbDriver::model()->updateByPk( $id,array('state'=>1) );
		}
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}

	/**
	* 保存驾驶员数据
	*/
	private function saveData( $model ){
		if( Yii::app()->request->isPostRequest ) {
			$model->attributes = Yii::app()->request->getPost('data');
			if( $model->save() ) {
				$this->dealSuccess( $this->createUrl('index') );
			}else{
				$this->dealError( $model->getErrors() );
			}
		}
		$this->render( 'edit', $model->attributes );
	}
}
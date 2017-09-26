<?php
/**
 * 车辆管理
 * @access 车辆管理
 * @author liang
 * @package Controller
 * @version 0.1
 *
 */
class VehicleController extends Controller {

	/**
	 * 车辆列表
	 * @access 车辆列表
	 */
	public function actionIndex() {
		$data['plateNumber'] =  Yii::app()->request->getQuery('plateNumber');
		$c = new CDbCriteria();

		if( $data['plateNumber'] ){
			$c->compare('t.plateNumber',$data['plateNumber'],true);
		}

		$c->addCondition("t.state!=1");		
		$pageSize =  tbConfig::model()->get('page_size');
		$model = new CActiveDataProvider('tbVehicle', array(
				'criteria'=>$c,
				'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
			));
		$data['list'] = $model->getData();
		$data['pages'] = $model->getPagination();
		$this->render( 'index' ,$data );
	}
	
	/**
	 * 编辑车辆
	 * @access 编辑车辆
	 * @throws CHttpException
	 */
	public function actionEdit() {
		$id = Yii::app()->request->getQuery('id');
		if( is_null($id) || !is_numeric($id) || ($model = tbVehicle::model()->findByPk( $id ))==null ) {
			throw new CHttpException( '404', 'Not found record' );
		}
		$this->saveData( $model );
	}

	/**
	* 新增车辆
	* @access 新增车辆
	* @throws CHttpException
	*/
	public function actionAdd(){
		$model = new tbVehicle();
		$this->saveData( $model );
	}

	/**
	 * 删除车辆
	 * @access 删除车辆
	 */
	public function actionDel() {
		$id = Yii::app()->request->getQuery('id');
		if( is_numeric($id) && $id >0 ){
			tbVehicle::model()->deleteByPk( $id );
		}
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}

	/**
	* 保存车辆数据
	*/
	private function saveData( $model ){
		if( Yii::app()->request->isPostRequest ) {
			$model->plateNumber = Yii::app()->request->getPost('plateNumber');
			if( $model->save() ) {
				$this->dealSuccess( $this->createUrl('index') );
			}else{
				$this->dealError( $model->getErrors() );
			}
		}
		$this->render( 'edit', array( 'data'=>$model->attributes ) );
	} 


}
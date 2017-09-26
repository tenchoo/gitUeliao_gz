<?php
/*
* @access 送货区域管理
*/
class AreaController extends Controller {

	//欢迎页面无需检察权限
    public function beforeAction($action) {
        return true;
    }

	/**
	* @access 送货区域列表
	*/
	public function actionIndex() {
		$state = Yii::app()->request->getQuery('state',0);
		$data = tbDeliveryArea::model()->getAllArea( $state );
		$this->render( 'index',$data );
	}

	/**
	* @access 按片区设置送货员
	*/
	public function actionSetdeliveryman() {
		$deliverymanId = Yii::app()->request->getPost('deliverymanId');
		$areaId = Yii::app()->request->getQuery('areaId');
		$t = tbDeliveryOrder::model()->setDeliverymanByArea( $deliverymanId ,$areaId );
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}



	/**
	* @access 新增送货区域
	*/
	public function actionAdd(){
		$model = new tbDeliveryArea();
		$this->addEdit( $model );
	}


	/**
	* @access 编辑送货区域
	*/
	public function actionEdit( $id ){
		$model = tbDeliveryArea::model()->findByPk( $id );
		if ( !$model ) {
			$this->redirect( $this->createUrl( 'index' ) );
		}
		$this->addEdit( $model );
	}

	/**
	* @access 保存送货区域信息
	*/
	private function addEdit( $model ){
		if( Yii::app()->request->isPostRequest ) {
			$model->title = trim ( Yii::app()->request->getPost('title') );
			if( $model->save() ){
				$url = $this->createUrl( 'index' );
				$this->dealSuccess($url);
			}else{
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}
		}

		$this->render( 'add',array( 'title'=>$model->title) );
	}


	/**
	* @access 删除送货区域
	*/
	public function actionDel( $id ){
		if( is_numeric($id) && $id>0 ){
			tbDeliveryArea::model()->deleteByPk( $id );
		}
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}
}
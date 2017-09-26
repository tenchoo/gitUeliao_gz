<?php
/**
 * 仓库盘点单
 * @access 仓库盘点单
 * @author liang
 * @package Controller
 * @version 0.1
 *
 */

class StocktakingController extends Controller {

	/**
	 * 盘点单管理
	 * @access 盘点单管理
	 */
	public function actionIndex() {
		$stocktakingId = Yii::app()->request->getQuery('stocktakingId');
		$userName = Yii::app()->request->getQuery('userName');
		$createTime = Yii::app()->request->getQuery('createTime');
		$criteria = new CDbCriteria;
		$criteria->order = 'createTime desc';
		if( $stocktakingId ){
			$criteria->compare('t.stocktakingId',$stocktakingId);
		}
		if( $createTime ){
			$criteria->addCondition("t.createTime>'$createTime'");
			$createTime2 = date("Y-m-d",strtotime( $createTime )+86400 ) ;
			$criteria->addCondition("t.createTime < '$createTime2' ");
		}
		if( $userName ){
			$criteria->compare('t.userName',$userName);
		}

		$pageSize =  tbConfig::model()->get('page_size');
		$model = new CActiveDataProvider('tbStocktaking', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));
		$list = $model->getData();
		$pages = $model->getPagination();
		$this->render( 'index', array('list' => $list,'pages'=>$pages,'stocktakingId'=>$stocktakingId,'userName'=>$userName,'createTime'=>$createTime) );
	}

	/**
	* 新增盘点单
	* @access 新增盘点单
	* @throws CHttpException
	*/
	public function actionAdd(){
		$id = Yii::app()->request->getQuery('id');
		if( !is_null( $id ) ){
			$this->add_step3( $id );
		}else{
			$step =  Yii::app()->request->getPost('step');
			$func = ( $step == '2' )?'add_step2':'add_step1';
			$this->$func();
		}
	}



	private function add_step1(){
		$model = new StocktakingForm( 'step1' );
		$tpl = 'edit';

		$warehouse = tbWarehouseInfo::model()->getAll();
		if( Yii::app()->request->isPostRequest ) {
			$warehouseId = Yii::app()->request->getPost('warehouseId');
			$model->warehouseId = (array_key_exists($warehouseId,$warehouse))?$warehouseId:'';
			$model->serialNumber = Yii::app()->request->getPost('serialNumber');
			if( $model->add_step1() ) {
				Yii::app()->session->add('takingModel', $model );
				$tpl = 'edit2';
			}else{
				$this->dealError( $model->getErrors() );
			}
		}
		$data = $model->attributes;
		$data['warehouse'] = $warehouse;
		$this->render( $tpl, $data );
	}

	private function add_step2(){
		set_time_limit(0);
		$model = Yii::app()->session->get('takingModel');
		if(  is_null( $model ) || !( $model instanceof StocktakingForm ) ) {
			$this->add_step1();
		}

		$takingData = Yii::app()->session->get('takingData');
		if( $model->add() ) {
			$this->dealSuccess( $this->createUrl('add',array('id'=>$model->stocktakingId)) );
		}else{
			$this->dealError( $model->getErrors() );
		}

		$data = $model->attributes;
		$data['warehouse'] = tbWarehouseInfo::model()->getAll();
		$this->render( 'edit2', $data );
	}

	private function add_step3( $id ){
		set_time_limit(0);

		$StocktakingForm = new StocktakingForm();
		$data = $StocktakingForm->getModelData( $id,'0' );
		if( empty( $data ) ){
			$this->redirect( $this->createUrl('add') );
		}

		if( Yii::app()->request->isPostRequest ) {
			if( $StocktakingForm->comfirmData() ) {
				$this->dealSuccess( $this->createUrl('index') );
			}else{
				$this->dealError( $StocktakingForm->getErrors() );
			}
		}

		$warehouse = tbWarehouseInfo::model()->findByPk( $data['warehouseId'] );
		if( $warehouse ){
			$warehouse = $warehouse->title;
		}
		$data['warehouse'] = $warehouse;
		$this->render( 'edit3',$data );
		Yii::app()->end();
	}

	/**
	 * 查看盘点单
	 * @access 查看盘点单
	 * @throws CHttpException
	 */
	public function actionView() {
		$id = Yii::app()->request->getQuery('id');

		$StocktakingForm = new StocktakingForm();
		$data = $StocktakingForm->getModelData( $id );
		if( empty( $data ) ){
			$this->redirect( $this->createUrl('index') );
		}

		$warehouse = tbWarehouseInfo::model()->findByPk( $data['warehouseId'] );
		if( $warehouse ){
			$warehouse = $warehouse->title;
		}
		$data['warehouse'] = $warehouse;

		/* $data = $model->attributes;
		$data['createTime'] = date('Y/m/d',strtotime($data['createTime']));
		$data['products'] = array();
		$warehouse = tbWarehouseInfo::model()->findByPk( $model->warehouseId );
		$data['warehouse'] = ($warehouse)?$warehouse->title:'';
		foreach ( $model->detail as $key=>$val ){
			$data['products'][$key] = $val->attributes;
		} */
		$this->render( 'view', $data );
	}


	/**
	 * 盘点单现有库存数据模板下载
	 * @access 模板下载
	 */
	public function actionTemps() {
		$type = Yii::app()->request->getQuery('type');
		if( $type === 'defaultTemp' ){
			$StocktakingForm = new StocktakingForm();
			$StocktakingForm->defaultTemp();
			EXIT;
		}

		$warehouseId = Yii::app()->request->getQuery('warehouseId');
		$serialNumber = Yii::app()->request->getQuery('serialNumber');

		$warehouse = tbWarehouseInfo::model()->getAll();
		if( !array_key_exists($warehouseId,$warehouse) ){
			$warehouseId = '';
		}

		if( Yii::app()->request->isPostRequest && $warehouseId ) {
			set_time_limit(0);
			Yii::$enableIncludePath = false;
			Yii::import('libs.vendors.phpexcel.*');

			$StocktakingForm = new StocktakingForm();
			$StocktakingForm->warehouseId = $warehouseId;
			$StocktakingForm->downTemps( $warehouse[$warehouseId] );
			$errors = $StocktakingForm->getErrors();
			if( !empty( $errors ) ) {
				$this->dealError( $errors );
			}
		}

		$list = array();
		if( $warehouseId ){
			$criteria = new CDbCriteria;
			$criteria->select = 't.productId,t.serialNumber';//默认*
			$criteria->addCondition( "exists ( select null from {{warehouse_product}} wp where wp.productId = t.productId and wp.warehouseId = '$warehouseId') " );
			$criteria->order = 't.serialNumber asc';
			if( $serialNumber ){
				$criteria->compare( 'serialNumber',$serialNumber );
			}

			$data = tbProduct::model()->findAll( $criteria );
			$list = array_map ( function($i){ return $i->getAttributes( array('productId','serialNumber'));} ,$data );
		}



		$this->render( 'temps', array('list' => $list,'warehouseId'=>$warehouseId,'warehouse'=>$warehouse,'serialNumber'=>$serialNumber) );
	}
	/**
	* 全仓盘点--导出数据
	*/
	public function actionExportexcel(){
		set_time_limit(0);
		$warehouseId = Yii::app()->request->getQuery('warehouseId');
		$op = Yii::app()->request->getQuery('op');
		$warehouse = tbWarehouseInfo::model()->getAll();
		if( array_key_exists($warehouseId,$warehouse) && $op === 'exportExcel' ){
			//导出全仓数据
			$StocktakingForm = new StocktakingForm();
			$StocktakingForm->exportExcel( $warehouseId,$warehouse[$warehouseId] );
		}

		$this->render( 'exportexcel', array('warehouseId'=>$warehouseId,'warehouse'=>$warehouse ) );
	}


	/**
	* 全仓盘点--导入数据
	*/
	public function actionimportexcel(){
		set_time_limit(0);
		$warehouse = tbWarehouseInfo::model()->getAll();

		$StocktakingForm = new StocktakingForm( 'all' );

		if( Yii::app()->request->isPostRequest ) {
			$warehouseId = Yii::app()->request->getPost('warehouseId');
			$StocktakingForm->warehouseId = (array_key_exists($warehouseId,$warehouse))?$warehouseId:'';
			$StocktakingForm->takinger = Yii::app()->request->getPost('takinger');

			if( $StocktakingForm->importExcel() ) {
				$this->dealSuccess( $this->createUrl('index') );
			}else{
				$this->dealError( $StocktakingForm->getErrors() );
			}
		}



		$this->render( 'importexcel', array('warehouseId'=>$StocktakingForm->warehouseId,'takinger'=>$StocktakingForm->takinger,'warehouse'=>$warehouse,) );
	}


}
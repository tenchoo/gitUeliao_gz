<?php
/**
 * 仓库调拨单,新建调拨时不更改库存量，确定调拨后，更改双方对应的库存量。
 * @access 仓库调拨单
 * @author liang
 * @package Controller
 * @version 0.1
 *
 */
class AllocationController extends Controller {

	/**
	 * 调拨单管理
	 * @access 调拨单管理
	 */
	public function actionIndex() {
		$this->showList( '2' );
	}


	/**
	 * 待确认调拨订单
	 * @access 待确认调拨订单
	 */
	public function actionWaitconfirm() {
		$this->showList( '1' );
	}

	/**
	 * 待调拨订单
	 * @access 待调拨订单
	 */
	public function actionWait() {
		$this->showList( '0' );

	}


	/**
	 * 调拨单列表
	 * @param integer $state  调拨单状态
	 */
	private function showList( $state ) {
		$condition['state']				= $state;
		$condition['allocationId']		= Yii::app()->request->getQuery('allocationId');
		$condition['orderId']		= Yii::app()->request->getQuery('orderId');
		$condition['userName']			= Yii::app()->request->getQuery('userName');
		$condition['createTime']		=  Yii::app()->request->getQuery('createTime');

		$data = call_user_func( array( new AllocationForm(),'search'),$condition);
		$this->render( 'index', array_merge($condition,$data) );
	}


	/**
	* 待调拨订单--确定调拨
	* @access 确定调拨
	* @throws CHttpException
	*/
	public function actionConfirmation(){
		$id = Yii::app()->request->getQuery('id');
		$model = tbAllocation::model()->findByPk( $id,'t.state=0 ');

		if( !$model ){
			throw new CHttpException(404,"the require obj has not exists.");
		}

		$vehicle = tbVehicle::model()->getAll();
		$drivers =  tbDriver::model()->getAll();

		$form = new AllocationForm( 'confirmation' );

		if( Yii::app()->request->isPostRequest ){
			$cancle = Yii::app()->session->get('allocation_cancle');
			if( $cancle && $cancle == $model->orderId ){
				$result = $form->cancle( $model );
				Yii::app()->session->add('allocation_cancle',null);
			}else{
				$form->attributes = Yii::app()->request->getPost('data');
				$result = $form->confirmation( $model,$vehicle ,$drivers);
			}

			if( $result ) {
				$this->dealSuccess( $this->createUrl( 'wait' ) );
			} else {
				$errors = $form->getErrors();
				$this->dealError( $errors );
			}
		}

		$data['orderTime'] = '';
		$data['orderState'] = 0;
		$data['applyInfo'] = null;
		$warehouse = tbWarehouseInfo::model()->getAll();
		if($model->orderId){
			$order = tbOrder::model()->find( array( 'select'=>'createTime,state','condition'=>'orderId = '.$model->orderId));
			if( $order ){
				$data['orderTime'] = $order->createTime;
				$data['orderState'] = $order->state;
				if( $data['orderState'] == '7' ){
					Yii::app()->session->add('allocation_cancle',$model->orderId);
				}else{
					Yii::app()->session->add('allocation_cancle',null);
					$data['applyInfo'] = tbWarehouseMessage::model()->getWMessage( $model->orderId,$model->warehouseId );
					if($data['applyInfo']['opstate'] == tbWarehouseMessage::OP_MODIFY ){
						Yii::app()->session->add('allocation_modify',$model->orderId);
					}else{
						Yii::app()->session->add('allocation_modify',null);
					}
				}
			}
		}

		$data['packinger'] = '';
		$data['packingTime'] = '';
		if( $model->packingId ){
			$packing = tbPacking::model()->findByPk( $model->packingId );
			if( $packing ){
				$data['packingTime'] = $packing->packingTime;
				$data['packinger'] =	tbUser::model()->getUsername( $packing->userId );
			}
		}

		if( Yii::app()->session->get('allocation_modify') == $model->orderId ){
			$data = array_merge($model->attributes,$data);
			$data['warehouse'] = isset($warehouse[$model->warehouseId])?$warehouse[$model->warehouseId]:'';
			$data['targetWarehouse'] = isset($warehouse[$model->targetWarehouseId])?$warehouse[$model->targetWarehouseId]:'';
			$productIds = array_map(function ($i){return $i->productId;},$model->detail);
			$pInfo =  tbProduct::model()->getUnitConversion( $productIds );
			$data['detail'] = array();
			foreach ($model->detail as $val ){
				$detail =  $val->attributes;
				$detail['unit'] = (isset($pInfo[$val->productId]['unit']))?$pInfo[$val->productId]['unit']:'';
				$data['detail'][] = $detail;
			}

		}else{
			$data = array_merge($form->setData( $model,$warehouse ),$data);
		}

		$this->render( 'confirmation', array_merge( $data ,array('vehicle'=>$vehicle,'drivers'=>$drivers,'params'=>$form->attributes))  );
	}

	/**
	* 待确认调拨订单--确定收货
	* @access 确定收货
	* @throws CHttpException
	*/
	public function actionReceipt(){
		$id = Yii::app()->request->getQuery('id');
		$model = tbAllocation::model()->findByPk( $id,'t.state=1 ');
		if( !$model ){
			throw new CHttpException(404,"the require obj has not exists.");
		}
		$saveData = array();
		$form = new AllocationForm( 'receipt' );
		if( Yii::app()->request->isPostRequest ){
			$saveData = Yii::app()->request->getPost('data');
			if( $form->receipt( $saveData,$model ) ) {
				$this->dealSuccess( $this->createUrl( 'waitconfirm' ) );
			} else {
				$errors = $form->getErrors();
				$this->dealError( $errors );
			}
		}

		$warehouse = tbWarehouseInfo::model()->getAll();
		$data = $form->setData( $model,$warehouse );
		$detail = array();
		foreach ( $data['detail'] as $val ){
			$detail = array_merge( $detail,array_values($val));
		}
		$data['detail'] = $detail;
		if( empty($saveData) || !is_array($saveData) ){
			$saveData  = $detail;
		}

		$data['orderTime'] = '';
		if($model->orderId){
			$order = tbOrder::model()->find( array( 'select'=>'createTime,state','condition'=>'orderId = '.$model->orderId));
			if( $order ){
				$data['orderTime'] = $order->createTime;
			}
		}

		$this->render( 'receipt',array('data'=>$data,'saveData'=>$saveData) );
	}



	/**
	* 新增调拨单
	* @access 新增调拨单
	* @throws CHttpException
	*/
	public function actionAdd(){
		$model = new AllocationForm( 'add' );
		$vehicle = tbVehicle::model()->getAll();
		$drivers =  tbDriver::model()->getAll();
		$warehouse = tbWarehouseInfo::model()->getAll();

		$warehouseId = Yii::app()->request->getQuery('warehouseId');
		$model->warehouseId = array_key_exists( $warehouseId, $warehouse )?$warehouseId:'';

		if( Yii::app()->request->isPostRequest ) {
			$data = Yii::app()->request->getPost('data');
			if( $data ){
				$model->attributes = $data;
				if( $model->add( $vehicle ,$drivers ) ) {
					$this->dealSuccess( $this->createUrl('waitconfirm') );
				}else{
					$this->dealError( $model->getErrors() );
				}
			}
		}

		if( !is_array($model->products)){
			$model->products  = array();
		}
		$this->render( 'edit', array( 'data'=>$model->attributes,'warehouse'=>$warehouse ,'vehicle'=>$vehicle,'drivers'=>$drivers) );
	}


	/**
	* 新增调拨单---回调，根据原调拨单的信息，建一张相反的调拨单
	* @access 回调调拨单
	* @throws CHttpException
	*/
	public function actionCallback(){
		$id = Yii::app()->request->getQuery('id');
		$model = new AllocationForm( 'add' );
		$falg = $model->getCallbackInfo( $id );

		if( !$falg ){
			$this->redirect( $this->createUrl('index') );
			exit;
		}

		$vehicle = tbVehicle::model()->getAll();
		$drivers =  tbDriver::model()->getAll();
		if( Yii::app()->request->isPostRequest ) {
			$model->driverId = Yii::app()->request->getPost('driverId');
			$model->vehicleId = Yii::app()->request->getPost('vehicleId');
			if( $model->add( $vehicle ,$drivers ) ) {
				$this->dealSuccess( $this->createUrl('waitconfirm') );
			}else{
				$this->dealError( $model->getErrors() );
			}
		}

		$warehouse = tbWarehouseInfo::model()->getAll();
		$data = $model->attributes;
		$this->render( 'callback', array( 'data'=>$data,'warehouse'=>$warehouse ,'vehicle'=>$vehicle,'drivers'=>$drivers) );
	}

	/**
	 * 删除调拨单
	 * @access 删除调拨单
	 */
	public function actionDel() {
		$id = Yii::app()->request->getQuery('id');
		if( is_numeric($id) && $id >0 ){
			tbAllocation::model()->updateByPk( $id,array('state'=>1) );
		}
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}

	/**
	 * 查看调拨单
	 * @access 查看调拨单
	 * @throws CHttpException
	 */
	public function actionView() {
		$id = Yii::app()->request->getQuery('id');
		if( is_null($id) || !is_numeric($id) || ($allocation = tbAllocation::model()->findByPk( $id ))==null ) {
			throw new CHttpException( '404', 'Not found record' );
		}
		$warehouse = tbWarehouseInfo::model()->getAll();
		$data = call_user_func( array( new AllocationForm(),'setData'),$allocation,$warehouse,'2');

		//入库信息
		$inputModel = tbWarehouseWarrant::model()->find(
										'source = :s and postId = :p',
										array(':s'=>tbWarehouseWarrant::FROM_CALLBACK,':p'=>$id)
										);
		$input = array();
		foreach ( $inputModel->detail as $val ){
			if(!isset($input[$val->singleNumber])){
				$input[$val->singleNumber] = array(
							'singleNumber'=>$val->singleNumber,
							'color'=>$val->color,
							'total'=>0,
						);
			}
			$input[$val->singleNumber]['total'] += $val->num;
			$input[$val->singleNumber]['detail'][] = array(
									'positionName'=>$val->positionName,
									'productBatch'=>$val->batch,
									'num'=>$val->num,
								);
		}
		$this->render( 'view', array('data'=>$data,'input'=>$input) );
	}
}
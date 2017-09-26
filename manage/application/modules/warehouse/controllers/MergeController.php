<?php
/**
 * 归单管理
 * @access 归单管理
 * @author liang
 * @package Controller
 * @version 0.1
 *
 */
class MergeController extends Controller {

	public function actionUsers(){
		//step1 判断当前账号是否为管理员
		$model =  new WarehouseManager();
		$warehouseId = $model->ManageWarehouse();

		if( empty( $warehouseId ) ){
			exit(  '只有仓库管理员才能进行此操作' );
		}

		$model->warehouseId = $warehouseId;
		if( Yii::app()->request->isPostRequest ) {
			$userId = Yii::app()->request->getPost('userId');
			if( $model->addMergeUser() ) {
				$this->dealSuccess( $this->createUrl( 'users' ) );
			}else{
				$this->dealError( $model->getErrors() );
			}
		}else{
			$this->delMerge( $model );
		}

		$list = $model->mergeList();
		$this->render('users', array('list'=>$list));
	}

	/**
	* 归单管理---删除归单人员
	* @access 归单调度
	*/
	private function delMerge( $model ){
		$op = Yii::app()->request->getQuery('op');
		$userId = Yii::app()->request->getQuery('id');
		if( $op !== 'del' || empty( $userId ) || !is_numeric( $userId ) ) return ;

		$model->delMerge( $userId );
		$this->dealSuccess(  Yii::app()->request->urlReferrer  );
	}

	/**
	* 归单管理---归单调度  仓库管理员的功能
	* @access 归单调度
	*/
	public function actionOrders(){
		//step1 判断当前账号是否为管理员
		$model =  new WarehouseManager();
		$warehouseId = $model->ManageWarehouse();

		if( empty( $warehouseId ) ){
			exit(  '只有仓库管理员才能进行此操作' );
		}

		$model->warehouseId = $warehouseId;

		//归单人员列表
		$merges = $model->mergeList();
		$users = array();
		foreach ( $merges as $val ){
			$users[$val['userId']] = $val['username'];
		}

		$MergeForm = new MergeForm();
		$MergeForm->warehouseId = $warehouseId;
		if( Yii::app()->request->isPostRequest ){
			if( $MergeForm->scheduling( $users ) ){
				$this->dealSuccess( $this->createUrl( 'orders' ) );
			}else{
				$errors = $MergeForm->getErrors();
				$this->dealError( $errors );
			}
		}

		$condition['orderId'] = Yii::app()->request->getQuery('orderId');
		$condition['singleNumber'] = Yii::app()->request->getQuery('singleNumber');
		$condition['state'] = 0;
		$condition['warehouseId'] = $warehouseId;
		$data = $MergeForm->doneList( $condition );
		$data['users'] = $users;
		$this->render('orders',array_merge($data,$condition));
	}



	/**
	* 归单管理---归单记录
	* 如果是仓库管理员，可以查看全部列表，
	* 如果是区域，可以查看此区域内的全部待归单记录
	* @access 待归单列表
	*/
	public function actionIndex(){
		//step1 查找当前账号的服务仓库,
		$model = tbWarehouseUser::model()->find( 'userId=:userId',array(':userId'=>Yii::app()->user->id ) );

		if( empty( $model ) ){
			exit(  '您不是仓库人员，无权限查看此页面' );
		}

		$warehouseId = $model->warehouseId;
		$Packing = new PackingForm();

		$condition['orderId'] = Yii::app()->request->getQuery('orderId');
		$condition['singleNumber'] = Yii::app()->request->getQuery('singleNumber');
		$condition['state'] = 2;
		$condition['warehouseId'] = $warehouseId;
		$data = $Packing->search( $condition );
		$this->render('index',array_merge($data,$condition));
	}

	/**
	* 待出库订单列表
	* 操作：如果是本仓库归单完成的，要么发货，要么调拔，如果是调拨过来的订单，只能发货，不能调拨。
	* @access 待出库订单列表
	*/
	public function actionActionlist(){
		//step1 判断当前账号是否为管理员
		$model =  new WarehouseManager();
		$warehouseId = $model->ManageWarehouse();

		if( empty( $warehouseId ) ){
			exit(  '只有仓库管理员才能进行此操作' );
		}

		$MergeForm = new MergeForm();
		$MergeForm->warehouseId = $warehouseId;
		$condition['orderId'] = Yii::app()->request->getQuery('orderId');
		$condition['state'] = array(tbOrderMerge::STATE_DONE,tbOrderMerge::STATE_ALLOTTED);
		$condition['actionType'] = 0;
		$condition['warehouseId'] = $warehouseId;
		$data = $MergeForm->doneList( $condition );

		$this->render('actionlist',array_merge($data,$condition));
	}

	/**
	* @access 已出库订单列表
	*/
	public function actionDonelist(){
		//step1 判断当前账号是否为管理员
		$model =  new WarehouseManager();
		$warehouseId = $model->ManageWarehouse();

		if( empty( $warehouseId ) ){
			exit(  '只有仓库管理员才能进行此操作' );
		}

		$MergeForm = new MergeForm();
		$MergeForm->warehouseId = $warehouseId;
		$condition['orderId'] = Yii::app()->request->getQuery('orderId');
		$condition['state'] = array(tbOrderMerge::STATE_DONE,tbOrderMerge::STATE_ALLOTTED);
		$condition['actionType'] = array(1,2);
		$condition['warehouseId'] = $warehouseId;
		$data = $MergeForm->doneList( $condition );

		$this->render('donelist',array_merge($data,$condition));
	}

	/**
	* 归单管理---查看归单完成的订单记录，
	*/
	public function actionView(){
		$data = $this->getOrderInfo();
		$this->render('view',$data );
	}

	private function getOrderInfo( $state = null ){
		$id = Yii::app()->request->getQuery('id');
		$model =  new MergeForm();
		$data = $model->getOrderInfo( $id,$state );

		if( empty( $data ) ){
			$this->redirect( $this->createUrl( 'donelist' ) );
		}
		return $data;
	}

	/**
	* 归单管理---发货
	*/
	public function actionDelivery(){
		$id = Yii::app()->request->getQuery('id');
		$model = tbOrderMerge::model()->findByPk( $id,'state >0 and actionType = 0' );
		if( !$model ){
			$this->redirect( $this->createUrl( 'donelist' ) );
		}

		if( Yii::app()->request->isPostRequest ) {
			$DeliveryOrder = new DeliveryOrder();
			if( $DeliveryOrder->doDelivery( $model ) ) {
				$this->dealSuccess( $this->createUrl( 'donelist' ) );
			}else{
				$this->dealError( $DeliveryOrder->getErrors() );
			}
		}

		$MergeForm =  new MergeForm();
		$data = $MergeForm->setData( $model );
		$this->render('delivery',$data );
	}


	/**
	* 归单管理---调拨，如果当前分拣仓库与发货仓库一致，无须调拨
	*/
	public function actionAllocation(){
		$data = $this->getOrderInfo();
		if( $data['state'] !== '1' || $data['actionType'] != '0' || $data['warehouseId'] == $data['DwarehouseId'] ){
			$this->redirect( $this->createUrl( 'donelist' ) );
		}
		$data['vehicle'] = tbVehicle::model()->getAll();
		$data['drivers'] =  tbDriver::model()->getAll();

		if( Yii::app()->request->isPostRequest ) {
			$model = new AllocationForm1( 'confirmation' );
			$model->targetWarehouseId = $data['DwarehouseId'] ;//直接指定调拨目标仓库
			$model->vehicleId = Yii::app()->request->getPost('vehicleId');
			$model->driverId = Yii::app()->request->getPost('driverId');
			if( $model->allocationOrder( $data['id'],$data['vehicle'],$data['drivers'] ) ) {
				$this->dealSuccess( $this->createUrl( 'actionlist' ) );
			}else{
				$this->dealError( $model->getErrors() );
			}
		}

		$this->render('allocation',$data );
	}

	/**
	* @access 打印备货单 -- 备货单会自动打印，这里用于补打
	* @param integer id 订单ID
	*/
	public function actionPrint(){
		$id = Yii::app()->request->getQuery('id');
		$state = PrintPush::printOrderProduct( $id,$msg );
		if( Yii::app()->request->getIsAjaxRequest() ) {
			$json = new AjaxData( $state , $msg );
			echo $json->toJson();
		}else{
			echo $msg;
		}
		Yii::app()->end();
	}








}
<?php
/**
* 业务员--结算单列表
*/
class SettlementController extends Controller {

	public function init() {
		parent::init();
		$this->pageTitle .= ' 结算单';
	}

	public function beforeAction( $action ){
		$userType = Yii::app()->user->getState('usertype');
		if( $userType != tbMember::UTYPE_SALEMAN ){
			$this->redirect('/');
		}
		return  $action ;
	}

	/**
	* 结算单列表
	* @access 结算单列表
	*/
	public function actionIndex() {
		$condition['createTime1'] = Yii::app()->request->getQuery('createTime1');
		$condition['createTime2'] = Yii::app()->request->getQuery('createTime2');
		$condition['orderId'] = Yii::app()->request->getQuery('orderId');
		$OrderSettlement = new OrderSettlement();
		$data = $OrderSettlement->search( $condition );
		$this->render('index',array_merge( $data,$condition ));
	}


	private function isServe( $memberId ){
		$isserve = tbMember::checkServe( $memberId,Yii::app()->user->id );
		if( !$isserve ){
			throw new CHttpException(403,"You do not have permission to view this page.");
		}
	}

	/**
	* @access 打印结算单
	*/
	public function actionPrint(){
		$id = Yii::app()->request->getQuery('id');
		$state = PrintPush::printSettlement( $id,$msg,false );
		$json = new AjaxData( $state , $msg );
		echo $json->toJson();
		Yii::app()->end();
	}

	/**
	* @access 设置打印
	*/
	public function actionSetprint(){
		$model = tbMemberSaleman::model()->findByPk( Yii::app()->user->id );

		if( !$model ){
			$model = new tbMemberSaleman();
			$model->memberId = Yii::app()->user->id;
		}

		$printers    = tbPrinter::model()->getAll();
		if(Yii::app()->request->getIsPostRequest()) {
			$model->printerId = Yii::app()->request->getPost('printerId');
			if( !array_key_exists( $model->printerId, $printers )  ){
				$model->printerId = '';
			}

			if( $model->save() ) {
				$this->dealSuccess( $this->createUrl('setprint') );
			}else{
				$this->dealError( $model->getErrors() );
			}
		}

        $this->render('setprint', array('printerId' => $model->printerId, 'printers'=>$printers));
	}


	/**
	* 查看结算单
	* @access  查看结算单
	* @param integer id
	*/
	public function actionView(){
		$id = Yii::app()->request->getQuery('id');
		$model = tbOrderSettlement::model()->findByPk( $id );
		if( !$model ){
			$this->redirect('/');
		}

		$orderModel = $model->order;
		$this->isServe( $orderModel->memberId );

		$order = new Order();
		$orderModel->orderType = $order->orderType( $orderModel->orderType );
		$member = $order->getMemberDetial( $orderModel->memberId );


		$member['salesman'] = tbUser::model()->getUserName( $orderModel->userId );
		$orderModel->deliveryMethod = $order->deliveryMethod( $orderModel->deliveryMethod );
		$payments = tbPayMent::model()->getPayMents();

		$orderModel->payModel = array_key_exists($orderModel->payModel,$payments)?$payments[$orderModel->payModel]['paymentTitle']:'未付款';

		if( $orderModel->warehouseId && $warehouse = tbWarehouseInfo::model()->findByPk( $orderModel->warehouseId ) ){
			$orderModel->warehouseId = $warehouse->title;
		}

		//结算单出单人
		if( $model->type == '1' ){
			$originator = tbUser::model()->getUserName( $model->originatorId );
		}else{
			$originator = tbProfile::model()->getMemberUserName( $model->originatorId );
			$originator .= '(业务员)';
		}

		$detail = array_map( function( $i ){ return $i->attributes;},$model->detail );

		$products = array();
		foreach ( $orderModel->products as $_product ){
			$products[$_product->orderProductId] = $_product->getAttributes( array('productId','singleNumber','mainPic','color','price','tailId','title') );
		}
		foreach ( $detail as &$val ){
			$val = array_merge( $val, $products[$val['orderProductId']] );
			if( $val['isSample']=='1' ){
				$val['subprice'] = 0;
				$val['isSample']= '是';
			}else{
				$val['subprice'] = bcmul( $val['num'],$val['price'],2 );
				$val['isSample']= '否';
			}

		}

		$this->render('view',array('model' => $model,'orderModel' => $orderModel,'member' => $member ,'originator' => $originator,'detail' => $detail));
	}

	/**
	* 业务员-生成结算单
	* @access 生成结算单
	* @param string orderId 订单号
	*/
	public function actionAdd(){
		$id = Yii::app()->request->getQuery('id');
		$model = tbOrder::model()->with('products')->findByPk( $id ,'t.state >0 and t.state !=7 and t.isSettled = 0');
		if( !$model ){
			$url = $this->createUrl('index');
			$this->redirect($url);
		}

		$this->isServe( $model->memberId );

		if( Yii::app()->request->isPostRequest ){
			$dataArr = Yii::app()->request->getPost('data');
			$OrderSettlement = new OrderSettlement();

			if( $OrderSettlement->save( $dataArr,$model ) ) {
				$this->dealSuccess( $this->createUrl( '/order/default/index',array('type'=>'3') ) );
			} else {
				$errors = $OrderSettlement->getErrors();
				$this->dealError( $errors );
			}
		}

		$order = new Order();
		$orderType = $order->orderType( $model->orderType );
		$model->state =  $order->stateTitle( $model->state );
		$member = $order->getMemberDetial( $model->memberId );
		$deliveryMethod = $order->deliveryMethod();
		$payModel = tbPayMent::model()->getPayMents( '0' );

		//过滤掉月结
		//判断当前用户是否月结用户
		$creditInfo = tbMemberCredit::creditInfo( $model->memberId );
		if( empty( $creditInfo ) && $model->payModel != '1' ){
			unset( $payModel['1'] );
		}

		if( $model->warehouseId && $warehouse = tbWarehouseInfo::model()->findByPk( $model->warehouseId ) ){
			$model->warehouseId = $warehouse->title;
		}
		$this->render('add',array('model' => $model,'member' => $member ,'deliveryMethod' => $deliveryMethod,'payModel' => $payModel,'orderType' => $orderType));
	}
}

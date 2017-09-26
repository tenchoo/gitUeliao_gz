<?php
/**
* 订单管理
* @access 订单管理
* @author liang
* @package Controller
* @version 0.1
*/
class DefaultController extends Controller {
	public $memberApi;

	/**
	 * 初始化控制器
	 * @see CController::init()
	 */
	public function init() {
		parent::init();
		$m = Yii::app()->params['SLD_member'];
		$this->memberApi = new ApiClient($m,'service',true);
	}

	/**
	* 所有订单
	* @access 所有订单
	*/
	public function actionAlllist() {
		$this->orderlist( '10'  ,$condition = array(),'t.createTime DESC');
	}

	/**
	* 待审核订单
	* @access 待审核订单
	*/
	public function actionIndex() {
		$this->orderlist( '0'  ,$condition = array('state'=>'0','orderType'=>array( 0,1,3 ) ),'t.createTime ASC');
	}

	/**
	* 备货中订单
	* @access 备货中订单
	*/
	public function actionDopacking() {
		$this->orderlist( '1'  ,$condition = array( 'state'=>'1' ));
	}

	/**
	* 备货完成订单
	* @access 备货完成订单
	*/
	public function actionPackingcomplete() {
		$this->orderlist( '2'  ,$condition = array( 'state'=>'2' ));
	}

	/**
	* 待财务确定订单---财务权限，财务确定后订单才进行发货。
	* @access 待财务确定订单
	*/
	public function actionWaitconfirmpayment() {
		$condition['createTime1'] = Yii::app()->request->getQuery('createTime1');
		$condition['createTime2'] = Yii::app()->request->getQuery('createTime2');
		$condition['orderId'] = Yii::app()->request->getQuery('orderId');
		$condition['settlementId'] = Yii::app()->request->getQuery('settlementId');
		$condition['is_string'] = ' exists ( select null from {{order}} o where o.orderId = t.orderId and o.isRecognition = 0 ) ';
		$OrderSettlement = new OrderSettlement();
		$data = $OrderSettlement->search( $condition );
		$this->render('settlement',array_merge( $data,$condition ));
	}

	/**
	* 待发货订单
	* @access 待发货订单
	*/
	public function actionWaitdelivery() {
		$this->orderlist( '3'  ,$condition = array('is_string'=>'t.state = 3 and ( t.isRecognition = 1 or t.payModel = 4 )') );
	}

	/**
	* 待确认收货订单
	* @access 待确认收货订单
	*/
	public function actionWaitconfirm() {
		$this->orderlist( '4'   ,$condition = array( 'state'=>'4' ));
	}

	/**
	* 待收款订单
	* @access 待收款订单
	*/
	public function actionWaitpayment() {
		$this->orderlist( '5' ,$condition = array( 'is_string'=>'payState<2' ));
	}



	/**
	* 已完成订单
	* @access 已完成订单
	*/
	public function actionDeal() {
		$this->orderlist( '6'  ,$condition = array( 'state'=>'6','isDel'=>'0' ),'t.createTime DESC');
	}

	/**
	* 已关闭订单
	* @access 已关闭订单
	*/
	public function actionClose() {
		$this->orderlist( '7'  ,$condition = array( 'state'=>'7','isDel'=>'0' ),'t.createTime DESC');
	}

	/**
	* 订单列表
	* @access 订单列表
	* @param $condition 订单列表查询条件
	*/
	private function orderlist( $state ,$condition = array(),$order = '' ){
		$condition['orderId'] = Yii::app()->request->getQuery('orderId');
		$condition['createTime1'] = Yii::app()->request->getQuery('createTime1');
		$condition['createTime2'] = Yii::app()->request->getQuery('createTime2');
		$condition['singleNumber'] = Yii::app()->request->getQuery('singleNumber');

		$Order = new Order();
		$pageSize = tbConfig::model()->get( 'page_size' );
		$data = $Order->search( $condition,$order, $pageSize );

		$reasons = Order::closeReason();
		$payments = tbPayMent::model()->getPayMents();
		$this->render('index',array('list' => $data['list'], 'pages' => $data['pages'], 'units' => $data['units'], 'condition' => $condition,'reasons' => $reasons,'payments' => $payments,'state'=>$state));
	}

	/**
	* 取消订单
	* @access 取消订单
	* @param integer $orderId
	* @param string $closeReason
	* @output json
	*/
	public function actionCancle(){
		$orderId = Yii::app()->request->getPost('orderId');
		$closeReason = Yii::app()->request->getPost('closeReason');
		$result = Order::cancleOrder( $orderId ,$whoClose = '2', $closeReason );
		$this->toJson($result );
	}

	/**
	* 删除订单
	* @access 删除订单
	* @param integer $orderId
	* @output json
	*/
	public function actionDel(){
		$orderId = Yii::app()->request->getQuery('id');
		Order::del( $orderId );
		$this->toJson( true );
	}

	/**
	* 上传凭证
	* @access 上传凭证
	* @param integer $paymemtId
	* @param string $voucher
	*/
	public function actionUpload(){
		$id = Yii::app()->request->getPost('paymemtId');
		$voucher = Yii::app()->request->getPost('voucher');
		$order = new Order();
		$result = $order->uploadVoucher( $id,$voucher );
		$this->toJson( $result );
	}

	/**
	* 根据结果输出JSON
	* @param complex $result
	* @output json
	*/
	private function toJson($result ){
		if( $result == 'true' ){
			$url = Yii::app()->request->urlReferrer;
			$json = new AjaxData( true, null, $url );
		}else{
			$json = new AjaxData( false, null, $result );
		}

		echo $json->toJson();
		Yii::app()->end();
	}


	/**
	* 订单追踪
	* @access 订单追踪
	*/
	public function actionTrace(){
		$id = Yii::app()->request->getQuery('id');
		$model = tbOrder::model()->with('user')->findByPk($id);
		$order = new Order();
		$model->orderType = $order->orderType( $model->orderType );
		$member = $order->getMemberDetial( $model->memberId );
		$member['salesman'] = isset( $model->user->username )?$model->user->username:'';
		$model->deliveryMethod = $order->deliveryMethod( $model->deliveryMethod );
		if( $model->warehouseId && $warehouse = tbWarehouseInfo::model()->findByPk( $model->warehouseId ) ){
			$model->warehouseId = $warehouse->title;
		}

		$trace = tbOrderMessage::getList( $model->orderId,true );
		$this->render('trace',array('model' => $model,'member' => $member,'trace' => $trace));
	}

	/**
	* 查看订单
	* @access 查看订单
	* @param integer id
	*/
	public function actionView(){
		$id = Yii::app()->request->getQuery('id');
		$model = tbOrder::model()->with('products','batches','user','paymemt')->findByPk($id);

		if( !$model ){
			$this->redirect( $this->createUrl( 'index' )  );
		}

		$order = new Order();
		$model->orderType = $order->orderType( $model->orderType );
		$member = $order->getMemberDetial( $model->memberId );
		$member['salesman'] = isset( $model->user->username )?$model->user->username:'';
		$model->deliveryMethod = $order->deliveryMethod( $model->deliveryMethod );
		$payments = tbPayMent::model()->getPayMents();
		$closeReason = '';
		if( $model->state == '7' && $closeModel = tbOrderClose::model()->findByPk( $id ) ){
			$closeReason = $closeModel->reason;
		}
		if( $model->warehouseId && $warehouse = tbWarehouseInfo::model()->findByPk( $model->warehouseId ) ){
			$model->warehouseId = $warehouse->title;
		}

		$this->render('view',array('model' => $model,'member' => $member ,'payments' => $payments,'closeReason' => $closeReason,));
	}

	/**
	* 确认收货
	* @access 确认收货
	* @param integer id  发货单ID，针对发货单收货
	*/
	public function actionReceived(){
		$id = Yii::app()->request->getQuery('id');
		$model = tbOrder::model()->with('products')->findByPk( $id,'t.state = 4' );
		if( !$model ){
			$this->redirect( $this->createUrl( 'waitconfirm' )  );
		}
		$dataArr = array();
		if( Yii::app()->request->isPostRequest ){
			$dataArr = Yii::app()->request->getPost('data');
			$Received = new Received();
			if( $Received->save( $dataArr,$model,'1' ) ) {
				$this->dealSuccess( $this->createUrl( 'waitconfirm' ) );
			} else {
				$errors = $Received->getErrors();
				$this->dealError( $errors );
			}
		}

		$productIds = array_map(function ($i){return $i->productId;},$model->products);
		$units = tbProduct::model()->getUnitConversion( $productIds );
		$this->render('received',array('products' => $model->products ,'dataArr' =>$dataArr,'units' =>$units));
	}

	/**
	* 查看物流
	* @access 查看物流
	* @param integer orderId
	* @param integer deliveryId
	* @param integer packingId
	*/
	public function actionLogistics(){
		$orderId = Yii::app()->request->getQuery('orderId');
		$deliveryId = Yii::app()->request->getQuery('deliveryId');
		$packingId = Yii::app()->request->getQuery('packingId');

		$logistics =  tbDelivery::model()->getLogistics( $orderId ,$packingId,$deliveryId );
		$this->render('logistics',array('logistics' => $logistics ));
	}


	/**
	* 确认收款
	* @access 确认收款
	* @param integer orderId 订单ID
	*/
	public function actionReconciliation(){
		$id = Yii::app()->request->getQuery('orderId');
		$model = tbOrder::model()->with('products','paymemt','user')->findByPk( $id );
		if( !$model ){
			$this->redirect( $this->createUrl( 'index' )  );
		}

		$domodel = new Reconciliation();

		if( Yii::app()->request->isPostRequest ){
			$domodel->attributes = Yii::app()->request->getPost('data');
			if( $domodel->save( $model ) ) {
				$this->dealSuccess( $this->createUrl( 'waitpayment' ) );
			} else {
				$errors = $domodel->getErrors();
				$this->dealError( $errors );
			}
		}
		$model->orderType = ($model->orderType=='0')?'订货订单':(($model->orderType=='1')?'预计订单':'留货订单');
		$member = $this->getMemberDetial( $model->memberId );
		$member['salesman'] = isset( $model->user->username )?$model->user->username:'';
		$payments = tbPayMent::model()->getPayMents();
		$order = array();
		if( $model->state == '7' ){
			$order['closeReason'] = tbOrderClose::model()->findByPk( $id )->reason;
		}
		$this->render('reconciliation',array('model' => $model ,'member'=>$member,'payments' => $payments,'order' => $order,'dataArr'=>$domodel->attributes ));
	}

	/**
	* 取得客户详细信息
	*/
	private function getMemberDetial( $memberId ){
		$order = new Order();
		return $order->getMemberDetial( $memberId );
	}

	/**
	* @access 订单审核
	* @param string orderId 订单号
	*/
	public function actionCheck(){
		$id = Yii::app()->request->getQuery('id');
		$model = tbOrder::model()->with('products','batches','user')->findByPk( $id ,'t.state = 0');
		if( !$model ){
			$this->redirect( $this->createUrl( 'index' )  );
		}
		if( Yii::app()->request->isPostRequest ){
			$dataArr = Yii::app()->request->getPost('data');
			$orderCheck = new OrderCheck();

			if( $orderCheck->save( $dataArr,$model,true ) ) {
				$this->dealSuccess( $this->createUrl( 'index' ) );
			} else {
				$errors = $orderCheck->getErrors();
				$this->dealError( $errors );
			}
		}

		$order = new Order();
		$orderType = $order->orderType( $model->orderType );
		$member = $order->getMemberDetial( $model->memberId );
		$member['salesman'] = isset( $model->user->username )?$model->user->username:'';
		$deliveryMethod = $order->deliveryMethod();
		$payModel = tbPayMent::model()->getPayMents( '0' );

		//过滤掉月结
		//判断当前用户是否月结用户
		$creditInfo = tbMemberCredit::creditInfo( $model->memberId );
		if( empty( $creditInfo ) ){
			unset( $payModel['1'] );
		}

		$this->render('check',array('model' => $model,'member' => $member,'orderType' => $orderType ,'deliveryMethod' => $deliveryMethod,'payModel' => $payModel,'creditInfo' => $creditInfo));
	}

	/**
	* @access 修改订金
	* @param integer id 订单号
	*/
	public function actionChangedeposit(){
		$id = Yii::app()->request->getQuery('id');
		$model = tbOrder::model()->with('products','user')->findByPk( $id ,'t.state <= 1 and t.orderType = 1');
		if( !$model ){
			$this->redirect( $this->createUrl( 'index' )  );
			//throw new CHttpException(404,"the require obj has not exists.");
		}

		$deposit = $model->deposit;
		if( empty($deposit) ){
			$this->redirect( $this->createUrl( 'index' )  );
			//throw new CHttpException(500,"the order has not deposit.");
		}

		if( $deposit->payState == '1' ){
			$this->redirect( $this->createUrl( 'index' )  );
			//throw new CHttpException(500,"the order deposit has pay.");
		}

		if( Yii::app()->request->isPostRequest ){
			$amount = Yii::app()->request->getPost('amount');
			if( $deposit->changeDeposit( $amount,$model ) ) {
				$this->dealSuccess( $this->createUrl('check',array('id'=>$id)) );
			} else {
				$errors = $deposit->getErrors();
				$this->dealError( $errors );
			}
		}

		$order = new Order();
		$model->orderType = $order->orderType( $model->orderType );
		$member = $order->getMemberDetial( $model->memberId );
		$member['salesman'] = isset( $model->user->username )?$model->user->username:'';
		$model->deliveryMethod = $order->deliveryMethod( $model->deliveryMethod );

		$this->render('changedeposit',array('model' => $model,'member' => $member ,'deposit' => $deposit ));
	}


	/**
	* 财务权限，财务确定后订单才进行发货。
	* @access 财务确定收款
	* @param integer id 订单号
	*/
	public function actionConfirmpayment(){
		$id = Yii::app()->request->getQuery('id');
		$model = tbOrderSettlement::model()->findByPk( $id );
		if( !$model ){
			$this->redirect( $this->createUrl( 'waitconfirmpayment' )  );
		}

		$orderModel = $model->order;
		if( $orderModel->isRecognition != '0' ){
			$this->redirect( $this->createUrl( 'waitconfirmpayment' )  );
		}

		if( Yii::app()->request->isPostRequest ){
			$confirmpay = Yii::app()->request->getPost('confirmpay');
			if( $confirmpay == '1' ){
				$orderModel->isRecognition = '1';//财务已确认
				if( $orderModel->state == '2' ){
					$orderModel->state = 3; //备货完成的进入待发货状态
				}

				if( $orderModel->save() ) {
					$message = '操作人：'. Yii::app()->user->getState('username').'(userId:'.Yii::app()->user->id.')';
					tbOrderMessage::addMessage2( $orderModel->orderId,'财务确认',$message,true );

					$this->dealSuccess( $this->createUrl( 'waitconfirmpayment' ) );
				} else {
					$errors = $orderModel->getErrors();
					$this->dealError( $errors );
				}
			}
		}



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

		$this->render('confirmpayment',array('model' => $model,'orderModel' => $orderModel,'member' => $member ,'originator' => $originator,'detail' => $detail));

		/* $id = Yii::app()->request->getQuery('id');
		$model = tbOrder::model()->with('products','paymemt')->findByPk( $id ,'t.isSettled >= 1 and t.isRecognition = 0 ');
		if( !$model ){
			throw new CHttpException(404,"the require obj has not exists.");
		}


		$payments = tbPayMent::model()->getPayMents(); */
		//$this->render('confirmpayment',array( 'model' => $model, 'payments' => $payments ) );
	}
}

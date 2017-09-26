<?php
class DefaultController extends Controller {

	public $userType;

	public function init(){
		parent::init();
		$this->userType =  Yii::app()->user->getState('usertype');
	}

	/**
	* 订单默认列表页
	* @access 订单默认列表页
	*/
	public function actionIndex() {
		$type = Yii::app()->request->getQuery('type');

		$OrderList = new OrderList();
		$tabs = $OrderList->tabs();
		foreach ( $tabs as $key=>&$val ){
			if( $key == '0' ){
				$val['url'] = $this->createUrl('index');
			}else{
				$val['url'] = $this->createUrl('index',array('type'=>$key));
				$val['count'] = $OrderList->orderCounts( $key );
			}
			unset($val['condition']);
		}

		$type = array_key_exists( $type,$tabs ) ? $type : '0';

		$condition['orderId'] = trim(Yii::app()->request->getQuery('orderId'));
		$condition['createTime1'] = Yii::app()->request->getQuery('createTime1');
		$condition['createTime2'] = Yii::app()->request->getQuery('createTime2');

		$data = $OrderList->getList( $type,$condition );
		$reasons = Order::closeReason();
		$payments = tbPayMent::model()->getPayMents();
		$this->render( 'list',array('list' => $data['list'], 'pages' => $data['pages'],'condition' => $condition,'closeReasons'=>$reasons,'type'=>$type,'tabs'=>$tabs,'payments'=>$payments ) );
	}

	/**
	* 业务员-订单审核
	* @access 订单审核
	* @param string orderId 订单号
	*/
	public function actionCheck(){
		$this->checkSaleman();

		$id = Yii::app()->request->getQuery('id');
		$model = tbOrder::model()->with('products','batches')->findByPk( $id ,'t.state = 0');

		if( !$model ){
			$url = $this->createUrl('index');
			$this->redirect($url);
		}

		$this->isServe( $model->memberId );

		$orderType = $model->orderType; //订录原来的订单类型。
		if( Yii::app()->request->isPostRequest ){
			$dataArr = Yii::app()->request->getPost('data');
			$orderCheck = new OrderCheck();

			if( $orderCheck->save( $dataArr,$model ) ) {
				if( $orderType == '2' ){
					if( $orderCheck->needPay ){
						$url = $this->createUrl('/cart/pay/index',array('orderids'=>$id));
					}else{
						$url = $this->createUrl( 'index',array('type'=>'7') );
					}
				}else{
					$url = $this->createUrl( 'index',array('type'=>'1') );
				}
				$this->dealSuccess( $url );
			} else {
				$errors = $orderCheck->getErrors();
				$this->dealError( $errors );
			}
		}

		$order = new Order();
		$orderType = $order->orderType( $model->orderType );
		$member = $order->getMemberDetial( $model->memberId );
		$deliveryMethod = $order->deliveryMethod();

		$payModel = tbPayMent::model()->getPayMents( '0' );

		//过滤掉月结
		//判断当前用户是否月结用户
		$creditInfo = tbMemberCredit::creditInfo( $model->memberId );
		if( empty( $creditInfo ) ){
			unset( $payModel['1'] );
		}else{
			if( $model->payState < '2'){
				$payModel = array_slice($payModel,0,1);
			}

		}

		$this->render('check',array('model' => $model,'member' => $member ,'deliveryMethod' => $deliveryMethod,'payModel' => $payModel,'orderType' => $orderType,'creditInfo' => $creditInfo));
	}

	/**
	* 业务员-价格申请
	* @access 价格申请
	* @param string orderId 订单号
	*/
	public function actionApplyprice(){
		$this->checkSaleman();

		$id = Yii::app()->request->getQuery('id');

		$model = tbOrder::model()->with('products')->findByPk( $id );
		if( !$model ){
			$url = $this->createUrl('index');
			$this->redirect($url);
		}


		$this->isServe( $model->memberId );

		$applyModel = tbOrderApplyprice::model()->find( 'orderId = :id and state in(0,1)',array(':id'=>$id ) );

		if( !$applyModel && Yii::app()->request->isPostRequest ){
			$dataArr = Yii::app()->request->getPost('data');
			$OrderApplyPrice = new OrderApplyPrice();

			if( $OrderApplyPrice->save( $dataArr,$model->orderId ) ) {
				Yii::app()->session['alertSuccess'] = '1';
				$this->dealSuccess( $this->createUrl( 'applyprice',array('id'=>$model->orderId) ) );
			} else {
				$errors = $OrderApplyPrice->getErrors();
				$this->dealError( $errors );
			}
		}
		$order = new Order();
		$model->deliveryMethod = $order->deliveryMethod( $model->deliveryMethod );
		$member = $order->getMemberDetial( $model->memberId );

		if( $model->payModel ){
			$payModel = tbPayMent::model()->findByPk( $model->payModel );
		}
		$model->payModel = (isset($payModel)&&$payModel)?$payModel->paymentTitle:'';

		$applyprices = null;
		if( $applyModel ){
			$applyprices = unserialize( $applyModel->prices );
		}

		if( Yii::app()->session->get('alertSuccess') ){
			Yii::app()->session->remove('alertSuccess');
			$msg = '成功提交价格申请';
		}else if( !empty ( $applyprices ) ){
			$msg = '已提交价格申请';
		}else {
			$msg =$this->getError();
		}
		$this->render('applyprice',array('model' => $model,'member' => $member,'applyprices' => $applyprices,'msg' => $msg  ));
	}




	/**
	* 业务员-修改订金
	* @access 修改订金
	* @param string orderId 订单号
	*/
	public function actionChangedeposit(){
		$this->checkSaleman();

		$id = Yii::app()->request->getQuery('id');
		$model = tbOrder::model()->with('products')->findByPk( $id ,'t.state <= 1 and t.orderType = 1');
		if( !$model ){
			$url = $this->createUrl('index');
			$this->redirect($url);
		}

		$this->isServe( $model->memberId );

		$deposit = $model->deposit;
		if( empty($deposit) ){
			throw new CHttpException(500,"the order has not deposit.");
		}

		if( $deposit->payState == '1' ){
			throw new CHttpException(500,"the order deposit has pay.");
		}

		if( Yii::app()->request->isPostRequest ){
			$amount = Yii::app()->request->getPost('amount');
			if( $deposit->changeDeposit( $amount,$model ) ) {
				$this->dealSuccess( $this->createUrl( 'check',array('id'=>$id) ) );
			} else {
				$errors = $deposit->getErrors();
				$this->dealError( $errors );
			}
		}

		$order = new Order();
		$orderType = $order->orderType( $model->orderType );
		$member = $order->getMemberDetial( $model->memberId );
		$model->deliveryMethod = $order->deliveryMethod( $model->deliveryMethod );
		$this->render('changedeposit',array('model' => $model,'member' => $member ,'deposit' => $deposit ,'orderType' => $orderType));
	}



	private function checkSaleman(){
		if( $this->userType != tbMember::UTYPE_SALEMAN ){
			throw new CHttpException(403,"You do not have permission .");
		}
	}

	private function isServe( $memberId ){
		if( $this->userType == tbMember::UTYPE_SALEMAN ){
			$isserve = tbMember::checkServe( $memberId,Yii::app()->user->id );
			if( !$isserve ){
				throw new CHttpException(403,"You do not have permission to view this page.");
			}
		}
	}

	/**
	* 订单详情页
	* @param string orderId 订单号
	*/
	public function actionView(){
		$id = Yii::app()->request->getQuery('id');

		$condition = '';
		if($this->userType != tbMember::UTYPE_SALEMAN){
			$condition = ' t.memberId = '.Yii::app()->user->id ;
		}
		$model = tbOrder::model()->with('products','batches','user','paymemt')->findByPk( $id ,$condition );

		if( !$model ){
			$url = $this->createUrl('index');
			$this->redirect($url);
		}

		$this->isServe( $model->memberId );

		$order = new Order();
		$orderType = $order->orderType( $model->orderType );
		$member = $order->getMemberDetial( $model->memberId );
		$member['salesman'] = isset( $model->user->username )?$model->user->username:'';
		$model->deliveryMethod = $order->deliveryMethod( $model->deliveryMethod );
		$payments = tbPayMent::model()->getPayMents();
		$closeReason = array('reasonType'=>'','reason'=>'');
		$keep = null;
		if( $model->state == '7' && $closeModel = tbOrderClose::model()->findByPk( $id ) ){
			$reasonType = array('0'=>'客户取消','1'=>'业务员取消','2'=>'后台管理员取消','3'=>'系统取消',);
			$closeReason['reason'] = $closeModel->reason;
			$closeReason['reasonType'] = $reasonType[$closeModel->opType];
		}else if( $model->orderType == tbOrder::TYPE_KEEP ){
			//取得留货单审核信息
			$keep = tbOrderKeep::model()->findByAttributes( array('orderId'=> $id ) );
			if( $keep ){
				$stateTitle = array('0'=>'留货审核中','1'=>'留货审核通过','2'=>'留货审核不通过');
				$keep->state = $stateTitle[$keep->state];
				$keep->expireTime = date('Y-m-d H:i:s',$keep->expireTime);
				$keep = $keep->attributes;
			}
		}else if(  $model->state == '0' ){
			$member['payTime'] = bcmul ( tbConfig::model()->get( 'pay_save_time' ),60,0);
		}

		if( $model->warehouseId && $warehouse = tbWarehouseInfo::model()->findByPk( $model->warehouseId ) ){
			$model->warehouseId = $warehouse->title;
		}


		$reasons = Order::closeReason();
		$this->render('view',array('model' => $model,'orderType'=>$orderType,'member' => $member ,'payments' => $payments,'closeReason' => $closeReason,'closeReasons'=>$reasons,'keep'=>$keep));
	}


	/**
	* 查看订单物流
	*/
	public function actionExpressinfo(){
		$orderId = Yii::app()->request->getQuery('orderId');
		$logistics =  tbDelivery::model()->getLogistics( $orderId );
		$this->render( 'expressinfo' ,array('logistics'=>$logistics) );
	}


	/**
	* 确认收货
	* @access 确认收货
	* @param integer id  发货单ID，针对发货单收货
	*/
	public function actionReceived(){
		$id = Yii::app()->request->getQuery('id');
		if($this->userType == tbMember::UTYPE_SALEMAN){
			$condition = 't.state = 4 ' ;
		}else{
			$condition = 't.state = 4 and t.memberId = '.Yii::app()->user->id ;
		}

		$model = tbOrder::model()->with('products')->findByPk( $id,$condition );
		if( !$model ){
			$url = $this->createUrl('index');
			$this->redirect($url);
		}
		$this->isServe( $model->memberId );

		$dataArr = array();
		if( Yii::app()->request->isPostRequest ){
			$dataArr = Yii::app()->request->getPost('data');
			$Received = new Received();
			if( $Received->save( $dataArr,$model,'1' ) ) {
				$this->dealSuccess( $this->createUrl( 'index' ) );
			} else {
				$errors = $Received->getErrors();
				$this->dealError( $errors );
			}
		}

		$order = new Order();
		$orderType = $order->orderType( $model->orderType );
		$member = $order->getMemberDetial( $model->memberId );
		$model->deliveryMethod = $order->deliveryMethod( $model->deliveryMethod );
		$payments = tbPayMent::model()->getPayMents();

		if( $model->warehouseId && $warehouse = tbWarehouseInfo::model()->findByPk( $model->warehouseId ) ){
			$model->warehouseId = $warehouse->title;
		}

		if( $model->payModel=='4' && $model->logistics && $logistics = tbLogistics::model()->findByPk( $model->logistics )){
			$model->logistics = $logistics->title;
		}else{
			$model->logistics ='';
		}


		if( $payment = tbPayMent::model()->findByPk( $model->payModel ) ){
			$model->payModel = $payment->paymentTitle;
		}else{
			$model->payModel = '';
		}

		$productIds = array_map(function ($i){return $i->productId;},$model->products);
		$units = tbProduct::model()->getUnitConversion( $productIds );
		$this->render('received',array('model' => $model ,'dataArr' =>$dataArr,'units' =>$units,'member' =>$member,'orderType' => $orderType));
	}


	/**
	* 上传凭证
	* @param integer $paymemtId
	* @param string $voucher
	*/
	public function actionUpload(){
		$id = Yii::app()->request->getPost('paymemtId');
		$voucher = Yii::app()->request->getPost('voucher');
		$result = Order::uploadVoucher( $id,$voucher );
		$this->toJson( $result );
	}

	/**
	* 订单追踪
	* @access 订单追踪
	*/
	public function actionTrace(){
		$id = Yii::app()->request->getQuery('id');

		$condition = ( $this->userType == tbMember::UTYPE_SALEMAN )?'':' t.memberId = '.Yii::app()->user->id ;
		$model = tbOrder::model()->with('products','user')->findByPk( $id ,$condition );

		if( !$model ){
			$url = $this->createUrl('index');
			$this->redirect($url);

			//throw new CHttpException(404,"the require obj has not exists.");
		}

		$this->isServe( $model->memberId );

		$order = new Order();
		$orderType = $order->orderType( $model->orderType );
		$member = $order->getMemberDetial( $model->memberId );
		$member['salesman'] = isset( $model->user->username )?$model->user->username:'';
		$model->deliveryMethod = $order->deliveryMethod( $model->deliveryMethod );
		if( $model->warehouseId && $warehouse = tbWarehouseInfo::model()->findByPk( $model->warehouseId ) ){
			$model->warehouseId = $warehouse->title;
		}

		$trace = tbOrderMessage::getList( $model->orderId );
		$this->render('trace',array('model' => $model,'member' => $member,'trace' => $trace,'orderType' => $orderType));


	}
}

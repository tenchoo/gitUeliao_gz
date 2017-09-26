<?php
/**
 * 订单管理
 * @author liang
 * @version 0.1
 * @package Controller
 */
class OrderController extends Controller {

	public function init() {
		parent::init();

		if( empty( $this->memberId ) ) {
			$this->message = Yii::t('user','You do not log in or log out');
			$this->showJson();
		}
	}

    /**
	 * 订单列表接口
	 * @param integer $type 列表类型 0 所有订单,1 待审核,2备货中,3 备货完成,4 待发货,5 待确认收货,6 待结算
	 */
	public function actionIndex(){
		$type = Yii::app()->request->getQuery('type','0');
		$nextid = (int)Yii::app()->request->getQuery( 'nextid', 0 ); //断点orderId

		$condition['orderId'] = trim(Yii::app()->request->getQuery('orderId'));
		$condition['createTime1'] = Yii::app()->request->getQuery('createTime1');
		$condition['createTime2'] = Yii::app()->request->getQuery('createTime2');

		$OrderManager = new OrderManager();
		$OrderManager->userType = $this->userType;
		$OrderManager->memberId = $this->memberId;
		$tabs = $OrderManager->tabs();
		$type = isset($tabs[$type])?$type:'0';

		$this->data = $OrderManager->getList( $type,$nextid,$condition,$pageSize = 6 );
		$this->data['title'] = $tabs[$type]['title'];
		$this->state = true;
		$this->showJson();
	}


	public function actionStatenum(){

		$OrderManager = new OrderManager();
		$OrderManager->userType = $this->userType;
		$OrderManager->memberId = $this->memberId;

		$tabs = $OrderManager->tabs();
		$data = array(
					array('id'=>1,'title'=>'待审核'),
					array('id'=>4,'title'=>'待发货'),
					array('id'=>5,'title'=>'待收货'),
					array('id'=>6,'title'=>'待付款'));

		foreach ( $data as &$val ){
			$val['num'] = $OrderManager->orderCounts( $val['id'] );
		}

		$this->data = $data;
		$this->state = true;
		$this->showJson();
	}

	/**
	* 订单详情
	* @param integer $id 订单ID
	*/
	public function actionShow( $id ){
		$OrderManager = new OrderManager();
		$OrderManager->userType = $this->userType;
		$OrderManager->memberId = $this->memberId;
		$this->data = $OrderManager->getOne( $id );
		if( $this->data ){
			$this->state = true;
			$this->showJson();
		}

		$this->notFound();
	}


	/**
	* 新增订单
	*
	*/
	public function actionCreate(){
		$AddOrder = new OldAddOrder( $this->getSource(),$this->memberId,$this->userType );
		$data = Yii::app()->request->getPost('order');
		if ( $AddOrder->add( $data )){
			$this->state = true;
			$arr['needPay'] = $AddOrder->needPay;
			$arr['orderids'] = $AddOrder->orderids;
			$this->data = $arr;
		}else{
			$this->message = current( current( $AddOrder->getErrors() ) );
		}
		$this->showJson();
	}


	/**
	* 新增订单step1--取得订单确认信息
	*
	*/
	public function actionConfirm(){
		$model =  new Cart($this->memberId,$this->userType);
		$this->data = $model->getConfirms();
		if( empty($this->data) ){
			$this->message = Yii::t('msg','NO Data');
		}else{
			$this->state = true;
		}

		$this->showJson();
	}



	/**
	* 订单关闭理由
	*/
	public function actionClosereason(){
		$this->data = Order::closeReason();
		$this->state = true;
		$this->showJson();
	}


	/**
	* 订单支付方法,改为根据客户的支付方式出支付方法，这方法不能用了。暂留
	*/
 	public function actionPayments(){
		$payModels = tbPayMent::model()->getWXPayment();
		if(isset($payModels['4'])){
			$logistics = tbLogistics::model()->getList( 1 );
			foreach ( $logistics as $key=>$logi){
				$payModels['4']['methods'][] = array ('id'=>$key,'title'=>$logi);
			}
		}

		if( isset($payModels['1']) && $this->userType != tbMember::UTYPE_SALEMAN ){
			$memberPayModels = tbMember::model()->getPayMentType( $this->memberId );//客户的支付方式
			if( !isset ( $memberPayModels['payModel']['1'] )){
				unset($payModels['1']); //无月结方式
			}
		}

		$this->data = array_values($payModels);
		$this->state = true;
		$this->showJson();
	}

	/**
	* 订单支付信息
	*/
	public function actionPay(){
		if(Yii::app()->request->getIsPutRequest()){
			$orderids = Yii::app()->request->getPut( 'orderids' );
		}else{
			$orderids = Yii::app()->request->getQuery( 'orderids' );
		}

		if(empty($orderids)){
			$this->message = Yii::t('msg','Missing parameter');
			$this->showJson();
		}

		$PayForm = new PayForm($this->memberId,$this->userType);
		$model = $PayForm->getOrder( $orderids );
		if(empty( $model )){
			$this->message = Yii::t('msg','NO Data');
			$this->showJson();
		}

		if(Yii::app()->request->getIsPutRequest()){
			$PayForm->payModel = Yii::app()->request->getPut( 'payModel' );
			$PayForm->logistics = Yii::app()->request->getPut( 'logisticsId' );
			if( $PayForm->paymemtMethod( $model ) ){
				$this->state = true;
				$this->message = 'success';
			}else{
				$this->message = current( current( $PayForm->getErrors() ) );
			}
		}else{
			$this->data = $PayForm->PayInfo( $model );
			$this->state = true;
		}

		$this->showJson();
	}

	/**
	* 订单确认收货
	*/
	public function actionReceived(){
		if(Yii::app()->request->getIsPutRequest()){
			$orderId = Yii::app()->request->getPut( 'orderId' );
		}else{
			$orderId = Yii::app()->request->getQuery( 'orderId' );
		}

		if(empty($orderId)){
			$this->message = Yii::t('msg','Missing parameter');
			$this->showJson();
		}

		$condition = 't.state = 4';
		if($this->userType != tbMember::UTYPE_SALEMAN ){
			$condition .= ' and  t.memberId = '.$this->memberId ;
		}

		$model = tbOrder::model()->with('products')->findByPk( $orderId ,$condition );
		if( !$model ){
			$this->message = Yii::t('msg','NO Data');
			$this->showJson();
		}

		if( $this->userType == tbMember::UTYPE_SALEMAN ){
			$isserve = tbMember::checkServe( $model->memberId,$this->memberId );
			if( !$isserve ){
				$this->message = Yii::t('msg','NO Data');
				$this->showJson();
			}
		}

		if(Yii::app()->request->getIsPutRequest()){
			$Received = new Received();
			$Received->opId = $this->memberId;
			$dataArr = Yii::app()->request->getPut('data');
			if( $Received->save( $dataArr,$model,'1' ) ) {
				$this->state = true;
				$this->message = 'success';
			}else{
				$this->message = current( current( $Received->getErrors() ) );
			}
		}else{
			$products = array();
			$productids = array_map( function ( $i ){ return $i->productId;},$model->products);
			$units = tbProduct::model()->getUnitConversion( $productids );

			foreach ( $model->products as $pro ){
				if( !isset($products[$pro->productId])){
					$pro->mainPic = $this->getImageUrl( $pro->mainPic, 200 );
					$pro->price = Order::priceFormat( $pro->price);
					$products[$pro->productId] = $pro->getAttributes(array('productId','title','serialNumber','mainPic','price'));
					$products[$pro->productId]['unit'] = $units[$pro->productId]['unit'];
				}
				$pro->specifiaction = explode(':',$pro->specifiaction);
				$pro->specifiaction = $pro->specifiaction['1'];
				$pro->deliveryNum = Order::quantityFormat( $pro->deliveryNum );
				$products[$pro->productId]['detail'][] = $pro->getAttributes(array('orderProductId','specifiaction','singleNumber','deliveryNum'));
			}

			$this->state = true;
			$this->data = array_values($products);
		}

		$this->showJson();
	}

	/**
	* 订单操作
	* @param integer $id 订单ID
	*/
	public function actionUpdate($id){
		$op = Yii::app()->request->getPut('op');
		$func  = "op_".$op;
		if( method_exists($this, $func) ) {
			$this->$func($id);
		}else{
			$this->message = Yii::t('msg','Missing parameter');
		}
		$this->showJson();
	}

	/**
	* 物流信息
	* @param integer $id 订单ID
	*/
	public function actionLogistics( $id ){
		$OrderManager = new OrderManager();
		$OrderManager->userType = $this->userType;
		$OrderManager->memberId = $this->memberId;
		$this->data = $OrderManager->getLogistics( $id,$this->message );
		if( $this->data ){
			$this->state = true;
		}
		$this->showJson();
	}


	/**
	* 取消订单
	* @param integer $orderId  订单ID
	* @param string $closeReason  订单关闭理由
	*/
	private function op_cancle( $orderId ){
		$closeReason = Yii::app()->request->getPut('closeReason');
		$whoClose = ( $this->userType == tbMember::UTYPE_SALEMAN )?'1':'0';

		$result = Order::cancleOrder( $orderId ,$whoClose, $closeReason,$this->memberId );
		if( $result ===  true ){
			$this->state = true;
		}else{
			if(is_array($result)){
				$this->message =  current(current($result));
			}else{
				$this->message = Yii::t('msg','NO Data');
			}
		}
	}

	/**
	* 业务员-留货订单申请延期，最后一天可以申请,申请延期后订单需重新审核
	* @param integer $orderId	订单号
	*/
	public function op_delaykeep( $orderId ){

		if( $this->userType != tbMember::UTYPE_SALEMAN ){
			$this->message = Yii::t('user','Only sales man can do this action');
			return ;
		}

		$this->state = tbOrderKeepDelay::delay( $orderId,$this->message );
	}


	/**
	* 搜索客户，业务员下订单时查找用户
	* @param string $keyword 搜索关键词
	*/
	public function actionSearchmember( $keyword ){
		if( $this->userType != tbMember::UTYPE_SALEMAN ){
			$this->message = Yii::t('user','Only sales man can do this action');
			goto end;
		}

		if( empty($keyword) ){
			$this->message = Yii::t('msg','Missing parameter');
			goto end;
		}

		$func = is_numeric( $keyword )?'searchByPhone':'searchByCompanyname';
		$this->data = $this->$func( $keyword );
		$this->state = true;

		end:
		$this->showJson();
	}


	/**
	* 通过手机号码搜索客户，业务员下订单时查找用户,业务员只能查找自己服务的客户
	* @param string $keyword 搜索关键词
	* @param string $limit  搜索结果条数
	*/
	private function searchByPhone( $keyword ,$limit = '10'){

		$criteria = new CDbCriteria;
		$criteria->compare('state','Normal');

		//业务员只能查找自己服务的客户
		$userId[] = $this->memberId ;
		if( tbConfig::model()->get( 'default_saleman_id' ) == $this->memberId ){
			$userId[] = 0;
		}
		$criteria->compare('userId',$userId);

		$criteria->addSearchCondition('phone', $keyword);
		$criteria->compare('groupId','2');//只查找客户
		$criteria->limit = $limit;
		$model = tbMember::model()->findAll( $criteria );

		$result = array();
		foreach( $model as $val ){
			$result[] = array('id'=>$val->memberId,'title'=>$val->phone) ;
		}
		return $result ;
	}

	/**
	* 通过公司名称搜索客户，业务员下订单时查找用户
	* @param string $keyword 搜索关键词
	* @param string $limit  搜索结果条数
	*/
	private function searchByCompanyname( $keyword,$limit = '10' ){
		$criteria=new CDbCriteria;
		$criteria->select ='t.memberId,t.companyname';
		$criteria->addSearchCondition('t.companyname', $keyword);

		//业务员只能查找自己服务的客户
		$userId[] = $this->memberId ;
		if( tbConfig::model()->get( 'default_saleman_id' ) == $this->memberId ){
			$userId[] = 0;
		}
		$userId = implode(',',$userId);
		$criteria->join = " join {{member}} t2 on( t.memberId=t2.memberId and t2.state = 'Normal' and t2.groupId = 2 and t2.userId in( $userId ) )";

		$criteria->limit = $limit;
		$model = tbProfileDetail::model()->findAll( $criteria );

		$result = array();
		foreach( $model as $val ){
			$result[] = array('id'=>$val->memberId,'title'=>$val->companyname) ;
		}
		return $result ;
	}
}
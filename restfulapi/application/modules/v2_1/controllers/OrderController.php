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
		$AddOrder = new AddOrder( $this->getSource(),$this->memberId,$this->userType );
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
	* 选择客户--业务员下单时选择客户后调用，返回的客户对应的价格和地址信息。
	* @param integer $id 订单ID
	*/
	public function actionChoosemember(){
		if( $this->userType != tbMember::UTYPE_SALEMAN ){
			$this->message = 'No permission';
			goto end;
		}

		//业务员选择的客户ID
		$memberId = Yii::app()->request->getParam( 'memberId' );
		$cart = new tbCart();
		$cart->memberId = $this->memberId;
		$list = $cart->getConfirms();
		if(empty( $list ) || !is_numeric( $memberId ) || empty( $memberId )){
			$message = 'no data';
			goto end;
		}

		//判断此客户是否当前业务员在服务
		$isserve = tbMember::checkServe( $memberId,$this->memberId );
		if( !$isserve ){
			$this->message = 'Not your customers';
			goto end;
		}

		$member = tbMember::model()->with('profiledetail')->findByPk( $memberId );
		$data['memberId'] = $memberId;
		$data['memberName'] = is_null( $member->profiledetail )? $member->phone :$member->profiledetail->companyname.' '.$member->phone ;

		$address = tbMemberAddress::model()->getDefault( $memberId );
		$data['address'] = '';
		if( $address ){
			$info = $address->attributes;
			$info['cityinfo'] = tbArea::getAreaStrByFloorId( $address->areaId );
			$data['address'] = $info;
		}


		$data['price'] = array();
		$priceType = tbMember::model()->getPriceType( $memberId );

		foreach ( $list as $val ){
			foreach ($val['list'] as $pval ){
				if( !array_key_exists( $pval['productId'],$data['price'] ) ){
					$price = ($priceType =='1')?$pval['tradePrice']:$pval['price'];
					$data['price'][] = array('productId'=>$pval['productId'],'price'=> $price) ;
					//$data['price'][$pval['productId']] = $price;
				}
			}
		}
		/**
		$creditInfo = tbMemberCredit::creditInfo( $memberId );
		$data['creditInfo'] = empty($creditInfo)?'':$creditInfo; */

		$this->state = true;
		$this->data = $data;

		end:
		$this->showJson();
	}




	/**
	* 新增订单step1--取得订单确认信息
	*
	*/
	public function actionConfirm(){
		$this->order_confirms();

		/* $model =  new Cart($this->memberId,$this->userType);
		$this->data = $model->getConfirms();
		if( empty($this->data) ){
			$this->message = Yii::t('msg','NO Data');
		}else{
			$this->state = true;
		}

		$this->showJson(); */
	}

	/**
	* 1.取得订单确认信息
	*/
	private function order_confirms(){

		$cart = new tbCart();
		$cart->memberId = $this->memberId;

		$list = $cart->getConfirms();
		if( empty( $list ) ){
			$json=new AjaxData(false,'not found Data',null);
			echo $json->toJson();
			Yii::app()->end();
		}

		ksort($list);

		$order = new Order();
		$deliveryMethod = $order->deliveryMethod();
		foreach ( $deliveryMethod as $key=>&$val){
			$val = array ('id'=>$key,'title'=>$val);
		}
		$deliveryMethod = array_values( $deliveryMethod );

		$keeyday = Order::getKeeyday();  //留货天数
		$data = array(	'keeyday'=>$keeyday,
						'userType'=> $this->userType,
						'totalPayment'=>0,
						'inttotalPayment'=>0,
						'deliveryMethod'=>$deliveryMethod,
						'defaultMethod'=>current($deliveryMethod),
						'readyList'=>array('flag'=>false),
						'bookingList'=>array('flag'=>false),
						);
		$priceType = 0;
		if( $data['userType'] != tbMember::UTYPE_MEMBER ){
			$priceType = (int)tbMember::model()->getPriceType( $this->memberId );
		}

		for( $i =0;$i<2;$i++){
			$key = ($i==1)?'bookingList':'readyList';
			$isdeposit = ($i==1)?true:false;
			if( array_key_exists( $i,$list ) ){
				$data[$key] = $this->assembledData( $list[$i]['list'] ,$priceType,$isdeposit );
				$data['totalPayment'] += $data[$key]['total'] ;
				$data[$key]['total'] = $data[$key]['total'];
			}
		}

		$data['inttotalPayment']  = (int) bcmul( $data['totalPayment'],100,0);
		$data['totalPayment']  =  $data['totalPayment'];

		$json=new AjaxData(true,null,$data);
		echo $json->toJson();
		Yii::app()->end();
	}

	private function assembledData( $list ,$priceType,$isdeposit ){
		if( empty($list) ) return array();
		$products = $count = array();
		$unit = Yii::app()->session->get('unit');
		if( !is_array($unit)){
			$unit = array();
		}
		$deposit = 0;
		foreach ( $list as $val){
			if( !array_key_exists($val['unitId'], $unit) ){
				$unit[$val['unitId']] = tbUnit::getUnitName( $val['unitId']);
				Yii::app()->session->add('unit',$unit);
			}
			$price = ($priceType)?$val['tradePrice']:$val['price'];
			$total = $count[] = bcmul( $price,$val['num'],2);
			if( !array_key_exists($val['productId'], $products) ){
			$products[$val['productId']]  = array(
							'productId'=>(int)$val['productId'],
							'title'=>'【'.$val['serialNumber'].'】'.$val['title'],
							'mainPic'=>  $this->getImageUrl( $val['mainPic'], 200 ),
							'price'=>$price,
							'intprice'=>(int) bcmul( $price,100,0),
							'unit'=>$unit[$val['unitId']],
							'totalPrice'=> 0,
							'intTotalprice'=> 0,

						);
			}
			$detail = array(
				'cartId'=>(int)$val['cartId'],
				'stockId'=>(int)$val['stockId'],
				'singleNumber'=>$val['singleNumber'],
				'relation'=>$val['relation'],
				'num'=>$val['num'],
				'totalprice'=>$total,
				'intTotalprice'=> (int) bcmul ( $total,100,0),
				'depositRatio'=>$val['depositRatio'],//订金比例
			);


			if( $isdeposit ){
				$deposit = bcadd( bcmul( $total,$val['depositRatio']/100,2 ),$deposit,2);
				$detail['depositRatio'] = (int)$val['depositRatio'];//订金比例
			}

			$products[$val['productId']]['details'][] = $detail;
			$products[$val['productId']]['totalPrice']  += $total;
		}

		$return['flag'] = true;

		$return['total'] =  array_sum($count);
		$return['inttotal'] =(int) bcmul( $return['total'],100,0);

		if( $isdeposit ){
			$return['deposit'] = $deposit;
			$return['blance'] = bcsub( $return['total'],$deposit,2 ) ;

		}
		$return['list'] = array_values(array_map(function ( $i ){
							$i['intTotalprice'] = (int)bcmul( $i['totalPrice'],100,0);
							$i['totalPrice'] = $i['totalPrice'];
							return $i;
						} ,$products));
		return $return;
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


		//step1 获取订单支付信息
		$PayForm = new OrderPayForm( $this->memberId,$this->userType );
		$model = $PayForm->getPayInfo( $orderids );
		if(empty( $model )){
			$this->message = Yii::t('msg','NO Data');
			$this->showJson();
		}

		$payModels = $PayForm->appPayment();

		if( Yii::app()->request->getIsPutRequest() ){
			$pay['payModel']  = Yii::app()->request->getPut( 'payModel' );
			$pay['logistics'] = Yii::app()->request->getPut( 'logisticsId' );
			if( $PayForm->paymemtMethod( $pay,$model ) ){
				$this->state = true;
				$this->message = 'success';
			}else{
				$this->message = current( current( $PayForm->getErrors() ) );
			}
		}else{
			$orderInfo = array();
			$orderClass = new Order();
			foreach ( $model as $val ){
				$orderInfo[] = array(
								'orderId'=>$val->orderId,
								'orderType'=>$val->orderType,
								'orderTypeTitle'=>$orderClass->orderType( $val->orderType ),
								'payment'=> $val->realPayment,
							);
			}



			 //step2 生成支付信息
			$paymentModel = $PayForm->setPaymentOrder( 0 );
			if( $paymentModel  ){
				$tradeNo =  $paymentModel->ordpaymentId;
			}else{
				throw new CHttpException(503,'生成付款单失败。');
			}
			$this->data = array('orderInfo'=>$orderInfo,
								'totalpayMents'=>$PayForm->totalPrice,
								'tradeNo'=>$tradeNo,
								'payModels'=>array_values( $payModels ),
								'title'=>$PayForm->payTitle );
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
		$criteria->addNotInCondition('groupId', array(1));//只查找客户

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
		$criteria->join = " join {{member}} t2 on( t.memberId=t2.memberId and t2.state = 'Normal' and t2.groupId != 1 and t2.userId in( $userId ) )";

		$criteria->limit = $limit;
		$model = tbProfileDetail::model()->findAll( $criteria );

		$result = array();
		foreach( $model as $val ){
			$result[] = array('id'=>$val->memberId,'title'=>$val->companyname) ;
		}
		return $result ;
	}
}
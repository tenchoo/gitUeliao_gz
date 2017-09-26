<?php
class ServiceController extends ApiServer {

	public function init(){
		Yii::import('cart.models.*');
		Yii::import('order.models.*');
	}
	
	/**
	* 个人中心订单计数
	*/
	public function actionOrdercounts(){
		$OrderList = new OrderList();
		$tabs = $OrderList->tabs();
		foreach ( $tabs as $key=>&$val ){
			if( !in_array ( $key, array(1,4,5,6))){
				unset( $tabs[$key] );
				continue;
			}

			$val['count'] = $OrderList->orderCounts( $key );
			unset($val['condition']);
		}

		$json=new AjaxData( true,null,$tabs );
		echo $json->toJson();
		Yii::app()->end();
	}

	/**
	* 订单列表
	*/
	public function actionOrderlist(){
		if( Yii::app()->user->getIsGuest() ) {
			$message = Yii::t('user','You do not log in or log out');
			$json=new AjaxData(false,$message);
			echo $json->toJson();
			Yii::app()->end( 200 );
		}

		$type = Yii::app()->request->getQuery('type');
		$OrderList = new OrderList();
		$tabs = $OrderList->tabs();
		$type = isset($tabs[$type])?$type:'0';
		$condition['orderId'] = trim(Yii::app()->request->getQuery('orderId'));
		$condition['createTime1'] = Yii::app()->request->getQuery('createTime1');
		$condition['createTime2'] = Yii::app()->request->getQuery('createTime2');

		$data = $OrderList->getList( $type,$condition,$pageSize = 6 );
		$data['page'] = 1 + $data['pages']->getCurrentPage();
		$data['totalpage'] = $data['pages']->getPageCount();
		unset($data['pages']);
		echo json_encode($data);
	}


	public function actionTopCart(){
		$model = new CartStorage();
		$cart = $model->findAll();
		//项目总数
		$total_items = $model->total_items();
		//总金额
		$total = $model->total();
		$carts = array();
		if($cart){
		$carts = $cart;
		}
		else{
			$carts = null;
		}
		echo json_encode($carts);
	}

	/**
	* 购物车列表
	*/
	public function actionCartlist(){
		if( Yii::app()->user->getIsGuest() ) {
			$message = Yii::t('user','You do not log in or log out');
			$json=new AjaxData(false,$message);
			echo $json->toJson();
			Yii::app()->end( 200 );
		}
		$model =  new Cart();
		$cartsdata = $model->getCarts();
		$specs = $model->getSpecs();
		if( $model->userType == '0' ){
			$priceType = (int)tbMember::model()->getPriceType( Yii::app()->user->id );
		}else{
			$priceType = 0;
		}


		$totalPrice = 0;
		$totalItems = count($cartsdata);
		$list = array();
		//给数据分组
		foreach ( $cartsdata as &$val ){
			$relation = '';
			foreach ( $val['relation'] as $reval ){
				if(isset($specs[$reval])){
					$relation .= $specs[$reval]['specName'].': '.$specs[$reval]['title'].' '.$specs[$reval]['serialNumber'];
				}
			}

			$val['price'] = ($priceType)?$val['tradePrice']:$val['price'];

			if( $val['state']!='0' || empty( $relation ) ) {
				$totalItems --;
				continue; 			//暂不处理失败产品
				//$val['state'] = '产品已失效';
			}else{
				$totalPrice  += $val['num']*$val['price'];
			}

			if( !isset($list[$val['productId']]) ){
				$unit = tbUnit::getUnitName( $val['unitId']);
				$list[$val['productId']] = array(
							'productId'=>$val['productId'],
							'title'=>'【'.$val['serialNumber'].'】'.$val['title'],
							'mainPic'=> Yii::app()->params['domain_images'] . $val['mainPic'] . '_200',

						);
			}

			$list[$val['productId']]['relations'][] = array(
				'cartId'=>$val['cartId'],
				'stockId'=>$val['stockId'],
				'relation'=>$relation,
				'num'=>$val['num'],
				'price'=>$val['price'],
				'intprice'=>$val['price']*100,
				'unit'=>$unit,
			);
		}

		$data = array('list'=>array_values($list),'totalItems'=>$totalItems,'totalPrice'=> number_format($totalPrice,2),'inttotalPrice'=> $totalPrice*100 );
		echo json_encode($data);
	}


	/**
	* 订单确认信息
	*/
	public function actionGetconfirms(){
		$model =  new Cart();
		$data['list'] = $model->getConfirms();
		$data['keeyday'] = Cart::getKeeyday();  //留货天数
		$data['userType'] = $model->userType;
		echo json_encode($data);
	}
	
	/**
	* 订单支付提交，非在线支付
	*/
	public function actionPay(){
		$data = Yii::app()->request->getPost('params');
		if( !isset($data['0']['pay']) || !is_array($data['0']['pay']) || !isset($data['0']['orderids']) ){
			echo false;exit;
		}
		
		$ids = explode(',',$data['0']['orderids']);
		$conditions = 't.payState in ( 0,1 )';
		$memberId = Yii::app()->user->id;
		$userType = Yii::app()->user->getState('usertype');
		if( $userType == 'member'){
			$conditions .= ' and t.memberId = '.$memberId;
		}else{
			$conditions .= ' and t.userId = '.$memberId;
		}

		$model = tbOrder::model()->findAllByPk($ids,$conditions);
		if( !$model ){
			$json=new AjaxData(false,'the paymemt you require does not exists');
			echo $json->toJson();
			Yii::app()->end();
		}
	//	echo json_encode( $data['0']['orderids'] );exit;
		$PayForm = new PayForm();
		// $PayForm->payModel = $data['0']['pay']['bank'];
		// unset($data['0']['pay']['bank']);
		if( $PayForm->paymemtMethod( $data['0']['pay'],$model ) ){
			$json = new AjaxData(true);
		}else{
			$json = new AjaxData(false,null,$PayForm->getErrors());
		}
		echo $json->toJson();
	}

	/**
	* 订单确认信息
	*/
	public function actionAddorder(){
		$data = Yii::app()->request->getPost('params');
		$AddOrder = new AddOrder();
		if ( $AddOrder->add( $data['0'] )){
			$arr['needPay'] = $AddOrder->needPay;
			$arr['orderids'] = $AddOrder->orderids;
			$json = new AjaxData(true,null,$arr);
		}else{
			$json = new AjaxData(false,null,$AddOrder->getErrors());
		}
		echo $json->toJson();
	}
	/**
	* 手机端登录接口
	*/
	public function actionLogin(){
		$data = Yii::app()->request->getPost('params');
		$model=new LoginForm();
		Yii::app()->user->setState('VerifyCode','1111');
		$model->verifyCode = '1111';
		if( isset($data['0']) ){
			$model->attributes = $data['0'];
		}
		if($model->validate() && $model->login()){
			$result = array(true,Yii::app()->session->sessionId);
			echo json_encode($result);
		}else{
			$errors = $model->getErrors();
			$errors = current($errors);
			echo json_encode( array(false,$errors['0']));
		}
	}

	/**
	* 手机端注册接口
	*/
	public function actionReg(){
		$data = Yii::app()->request->getPost('params');
		$model = new RegForm();
		if( isset($data['0']) ){
			$model->attributes = $data['0'];
		}

		if( $model->validate() && $model->register()){
			$json = new AjaxData(true);
		}else{
			$errors = $model->getErrors();
			$errors = current($errors);
			$json = new AjaxData(false,$errors['0']);
		}
		echo $json->toJson();
		Yii::app()->end( 200 );
	}

	/**
	* 忘记密码step1
	*/
	public function actionForget1(){
		$data = Yii::app()->request->getPost('params');
		$model = new SetPasswordForm('forget');
		if( isset($data['0']) ){
			$model->attributes = $data['0'];
		}

		if( $model->validate() ){
			$model->forgetStep1();
			$json = new AjaxData(true);
		}else{
			$errors = $model->getErrors();
			$errors = current($errors);
			$json = new AjaxData(false,$errors['0']);
		}
		echo $json->toJson();
		Yii::app()->end( 200 );
	}

	/**
	* 忘记密码step2,重置密码
	*/
	public function actionForget2(){
		$data = Yii::app()->request->getPost('params');
		$deadline = Yii::app()->user->getState('forgetDeadline');

		if( $deadline < time() ){  //需做ajax处理
			$message = Yii::t('user','Page has expired');
		}

		$model = new SetPasswordForm('reset');
		if( isset($data['0']) ){
			$model->attributes = $data['0'];
		}
		if( $model->validate() && $model->restetPassword()){
			$json = new AjaxData(true);
		}else{
			$errors = $model->getErrors();
			$errors = current($errors);
			$json = new AjaxData(false,$errors['0']);
		}
		echo $json->toJson();
		Yii::app()->end( 200 );
	}
}
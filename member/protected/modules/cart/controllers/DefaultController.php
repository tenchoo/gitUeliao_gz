<?php
/**
* 购物车和确认订单
*/
class DefaultController extends Controller {

 	/**
 	 * 模板布局文件
 	 * @var string
 	 */
	public $layout='libs.commons.views.layouts.cart';

	public $priceType = '0'; //价格类型：0散剪价，1大货价

	public function init(){
		parent::init();

	}



	/**
	 * 购物车
	 * @access 购物车
	 */
	public function actionIndex() {

		$cart = new tbCart();
		$data['list'] =  $cart->allCartLists();
		if( empty( $data['list'] ) ){
			$data['totalItems'] = $data['totalPrice'] = 0;
			goto end;
		}

		$items = $total = array();
		$userType = Yii::app()->user->getState('usertype');
		if( $userType == tbMember::UTYPE_MEMBER ){
			$this->priceType = tbMember::model()->getPriceType( Yii::app()->user->id );
		}

		foreach ( $data['list'] as &$val ){
			if( array_key_exists('tailId',$val ) && $val['tailId']>0 ){
				$val['url'] = $this->homeUrl.'/tailproduct/detail-'.$val['tailId'].'.html';
			}else{
				$val['url'] = $this->homeUrl.'/product/detail-'.$val['productId'].'.html';
			}

			$val['price'] = ( $this->priceType )?$val['tradePrice']:$val['price'];
			if( $val['state'] == '0' ) {
				$total[] = $val['totalPrice'] = $val['num']*$val['price'];
				$items[] = $val['num'];
			}

		}
		$data['totalItems'] = array_sum ( $items );
		$data['totalPrice'] = array_sum ( $total );
		end:
		$this->render( 'index',$data );
	}

	/**
	* 确认订单信息,购物车内的信息全部提交
	* @access 确认订单信息
	*/
	public function actionConfirm() {
		$order = Yii::app()->request->getPost( 'order' );
		if( $order ){
			$AddNewOrder = new AddOrder('web');
			if ( $AddNewOrder->add( $order )){
				Yii::app()->session->add('confirm_list',null);
				$orderids = implode(',',$AddNewOrder->orderids);
				//需要支付跳转到支付页面，不需要支付跳转到支付成功页面。
				if( $AddNewOrder->needPay &&  count( $AddNewOrder->orderids ) == 1 ){
					$url = $this->createUrl('/cart/pay/index',array('orderids'=>$orderids));
				}else{
					$url = $this->createUrl('/cart/pay/success');
				}
				$this->dealSuccess( $url );
			}else{
				$this->dealError( $AddNewOrder->getErrors() );
				$list = Yii::app()->session->get('confirm_list');
			}
		}else{
			$cart = new tbCart();
			$list = $cart->getConfirms( true );
			if( empty( $list ) ){
				$this->redirect ( 'index' );
			}
			ksort($list);
			Yii::app()->session->add('confirm_list',$list );
		}

		$this->confirmpage( $list );
	}

	/**
	 * 添加产品到购物车
	 * @param int $productId 产品ID
	 * @param array $cart 规格/数量
	 * @access 添加产品到购物车
	 */
	public function actionAdd(){
		$productId = Yii::app()->request->getParam( 'productId' );
		$cart = Yii::app()->request->getParam( 'cart' );

		$model = new tbCart();
		if( $model->addCart( $productId, $cart ) ){
			$json = new AjaxData( true,null,$this->createUrl('index') );
		}else{
			$error = $model->getErrors();
			$msg = current( current( $error ) );
			$json = new AjaxData( false, $msg  );
		}

		echo $json->toJson();
		Yii::app()->end( 200 );
	}


	/**
	 * 添加产品到购物车--尾货产品
	 * @param int $tailId 尾货产品ID
	 * @param array $cart 规格/数量
	 * @access 添加产品到购物车
	 */
	public function actionAddtail(){
		$tailId = Yii::app()->request->getParam( 'tailId' );
		$cart = Yii::app()->request->getParam( 'cart' );

		$model = new tbCart();
		if( $model->addTailCart( $tailId, $cart ) ){
			$json = new AjaxData( true,null,$this->createUrl('index') );
		}else{
			$error = $model->getErrors();
			$msg = current( current( $error ) );
			$json = new AjaxData( false, $msg  );
		}

		echo $json->toJson();
		Yii::app()->end( 200 );
	}


	/**
	 * 从购物车删除产品
	 * @param $cartId 购物车ID
	 * @access 从购物车删除产品
	 */
	public function actionDelete(){
		$cartId = Yii::app()->request->getParam( 'cartId');
		if( is_numeric($cartId) && $cartId>0){
			$criteria = new CDbCriteria;
			$criteria->compare('cartId', $cartId);
			$criteria->compare('memberId', Yii::app()->user->id  );

			if( tbCart::model()->deleteAll( $criteria ) ){
				$json = new AjaxData( true,null,$this->createUrl('index') );
				goto end;
			}
		}

		$json = new AjaxData( false );
		end:
		echo $json->toJson();
		Yii::app()->end( 200 );
	}

	/**
	 * 更新购物车产品数量
	 * @param $cartId 购物车Id
	 * @param $num 数量
	 * @access 更新购物车产品数量
	 */
	public function actionQty(){
		$model = new tbCart();

		$cartId = Yii::app()->request->getPost( 'cartId');
		$num = Yii::app()->request->getPost( 'num' );
		if( $model->qty( $cartId,$num ) ){
			$json = new AjaxData( true,null,$this->createUrl('index') );
		}else{
			$json = new AjaxData( false );
		}
		echo $json->toJson();
		Yii::app()->end( 200 );
	}

	/**
	* 业务员下单选择客户后刷新form
	* @access 业务员下单选择客户后刷新form
	*/
	public function actionChoosemember(){
		$userType = Yii::app()->user->getState('usertype');
		if( $userType != tbMember::UTYPE_SALEMAN ){
			$json = new AjaxData( false, 'no permission' );
			goto end;
		}

		$memberId = Yii::app()->request->getParam( 'memberId' );
		$list = Yii::app()->session->get('confirm_list');
		if(empty( $list ) || !is_numeric( $memberId ) || empty( $memberId )){
			$json = new AjaxData( false, 'no data' );
			goto end;
		}

		//判断此客户是否当前业务员在服务
		$isserve = tbMember::checkServe( $memberId,Yii::app()->user->id );
		if( !$isserve ){
			$json = new AjaxData( false, 'Not your customers' );
			goto end;
		}

		$model = new Cart();
		$address = $model->getlist( $memberId );

		$data['memberId'] = $memberId;

		if(is_array( $address )){
			foreach ( $address as $val ){
				if($val['isDefault'] == '1'){
					$data['addressId'] = $val['addressId'];
				}
			}
		}

		$data['price'] = array();
		$this->priceType = tbMember::model()->getPriceType( $memberId );

		//取得当前客户支付的批发价格
		$productIds = array();
		foreach ( $list as $val ){
			foreach ($val['list'] as $pval ){
				$productIds[] = $pval['productId'];
			}
		}

		$specPrices = tbMemberApplyPrice::model()->getMemberPrice( $memberId ,$productIds );
		foreach ( $list as $val ){
			foreach ($val['list'] as $pval ){
				if( !array_key_exists( $pval['productId'],$data['price'] ) ){
					if( array_key_exists($pval['productId'],$specPrices ) ){
						$price = $specPrices[$pval['productId']];
					}else{
						$price = ($this->priceType =='1')?$pval['tradePrice']:$pval['price'];
					}

					$data['price'][$pval['productId']] = $price;
					//$data['price'][$pval['productId']] = array('price'=>$price,'intprice'=>bcmul($price,100,0));
				}
			}
		}

		$data['addressHtml'] =$this->renderPartial('_confirm',array('memberId'=>$memberId,'address'=>$address,'userType'=>$userType), true, true );


		//判断是否月结客户
		$creditInfo = tbMemberCredit::creditInfo( Yii::app()->user->id );
		$data['isMonthPay'] = empty( $creditInfo )?false:true;
		$json = new AjaxData( true,null,$data );

		end:
		echo $json->toJson();
		Yii::app()->end();
	}


	/**
	* 立即购买下单页---普通产品
	* @access 立即购买下单页
	*/
	public function actionBuynow(){
		$productId = Yii::app()->request->getPost( 'productId' );
		$cart =  Yii::app()->request->getPost( 'cart' );
		$order = Yii::app()->request->getPost( 'order' );
		if( !is_numeric( $productId ) || ( empty($order) && (!is_array($cart) || empty( $cart ) ))) {
			$this->redirect ( 'index' );
		}

		$product = tbProduct::model()->findByPk( $productId ,'state = 0 ');
		if( !$product ){
			throw new CHttpException(404,'the product you require does not exists');
		}
		if( $order ){
			$AddNewOrder = new AddOrder('web');
			 if ( $AddNewOrder->addBuyNow( $order,$product->attributes )){

				 Yii::app()->session->add('confirm_list',null);
				 Yii::app()->session->add('confirm_productId',null);
				$orderids = implode(',',$AddNewOrder->orderids);
				//需要支付跳转到支付页面，不需要支付跳转到支付成功页面。
				if( $AddNewOrder->needPay ){
					$url = $this->createUrl('/cart/pay/index',array('orderids'=>$orderids));
				}else{
					$url = $this->createUrl('/cart/pay/success');
				}
				$this->dealSuccess( $url );
			}else{
				$this->dealError( $AddNewOrder->getErrors() );
				$list = Yii::app()->session->get('confirm_list');
				$productId = Yii::app()->session->get('confirm_productId');
			}
		}else{
			$data = tbProductStock::model()->specStock ( $productId, array_keys( $cart ) );
			$specids = $list = array();
			foreach ( $data as &$val ){
				$val['relation'] = tbProductStock::relationToArray( $val['relation'] );
				$specids = array_merge( $specids ,$val['relation'] );
			}
			$specs = tbSpecvalue::getSpecs( $specids );

			foreach ( $data as &$val ){
				//可销售量
				$val['total'] = ProductModel::total( $val['singleNumber'] );
				$relation = '';
				foreach ( $val['relation'] as $reval ){
					if(isset($specs[$reval])){
						$relation .= $specs[$reval]['specName'].':'.$specs[$reval]['title'].' '.$specs[$reval]['serialNumber'];
					}
				}

				$val['relation'] = $relation;
				$val['num'] = $cart[$val['stockId']] ;
				if( $val['num'] >$val['total'] ){
					$key = 1;
				}else{
					$key = 0;
				}
				$list[$key]['list'][] = array_merge( $product->attributes ,$val);
			}
			ksort($list);

			Yii::app()->session->add('confirm_list',$list );
			Yii::app()->session->add('confirm_productId',$productId );
		}

		$this->confirmpage($list,$productId);
	}


	/**
	* 立即购买下单页---尾货产品
	* @access 立即购买下单页
	*/
	public function actionTailbuynow(){
		$tailId = Yii::app()->request->getPost( 'tailId' );
		$cart =  Yii::app()->request->getPost( 'cart' );

		if( !is_numeric( $tailId ) && $tailId< 1 ) {
			$this->redirect ( 'index' );
		}

		//判断此产品是否存在
		$tbTail = tbTail::model()->findByPk( $tailId,'state = :state and isSoldOut = 0 ',array( ':state'=>'selling' ) );
		if( !$tbTail ){
			throw new CHttpException(404,"产品不存在或已下架或已售完.");
		}

		$productinfo = $tbTail->product->getAttributes( array('title','mainPic','serialNumber') );
		$productinfo =  array_merge ( $tbTail->getAttributes( array('tailId','productId','price','tradePrice','saleType') ),$productinfo );

		$list = array();
		$key = tbOrder::TYPE_TAIL;
		foreach( $tbTail->single as $val ){
			if( $tbTail->saleType != 'whole' &&  !array_key_exists( $val->singleNumber,$cart ) ){
				continue;
			}

			$_list['singleNumber'] = $val->singleNumber;
			$_list['relation'] =  tbProductStock::relationTitle( $val->singleNumber,$_list['color'],$_list['stockId'] );

			$total = ProductModel::total( $val->singleNumber ,true );

			if( $tbTail->saleType != 'whole' ){
				if( $cart[$val->singleNumber] > $total ){
					$this->dealError( array( array( $val->singleNumber.'购买数量不能大于库存数量' ) ) );
				}

				$_list['num'] = $cart[$val->singleNumber];
			}else{
				$_list['num'] = $total;
			}

			$list[$key]['list'][] = array_merge( $productinfo,$_list );
		}

		$order = Yii::app()->request->getPost( 'order' );
		if( $order ){
			$AddNewOrder = new AddOrder('web');
			$order[$key]['product'] = $list[$key]['list'];
			 if ( $AddNewOrder->addTailBuyNow( $order ) ){
				Yii::app()->session->add('confirm_list',null);
				$orderids = implode(',',$AddNewOrder->orderids);
				//尾货产品需支付
				$url = $this->createUrl('/cart/pay/index',array('orderids'=>$orderids));
				$this->dealSuccess( $url );
			}else{
				$this->dealError( $AddNewOrder->getErrors() );
			}
		}

		Yii::app()->session->add('confirm_list',$list );
		$this->confirmpage($list,$tailId,'tail');
	}



	private function confirmpage($list,$productId = '',$type = 'normal' ){
		if( empty ($list)){
			$this->redirect( $this->createUrl('index') );
			exit;
		}

		$model = new Cart();
		$keeyday= Cart::getKeeyday();  //留货天数
		$address = '';
		$userType = Yii::app()->user->getState('usertype');

		$payModels  = tbPayMent::model()->getPcPayment();
		krsort(  $payModels );
		if( array_key_exists('1',$payModels ) ){
			unset( $payModels['1'] );
		}

		if( $userType == tbMember::UTYPE_MEMBER ){
			$this->priceType = tbMember::model()->getPriceType( Yii::app()->user->id );
			$address = $model->getlist( Yii::app()->user->id );
			//判断是否月结客户
			$creditInfo = tbMemberCredit::creditInfo( Yii::app()->user->id );
			if( !empty( $creditInfo ) ){
				$payModels  = array();
			}
		}

		$order = new Order();
		$deliveryMethod = $order->deliveryMethod();

		$defaultAddress = '';
		if(is_array( $address )){
			foreach ( $address as $val ){
				if($val['isDefault'] == '1'){
					$defaultAddress = $val['addressId'];
				}
			}
		}

		$realToalPrice = '0';
		$hasDeposit = false;

		foreach ( $list as $key=>&$sublist ){
			$sublist['totalPrice'] = 0;
			//订金
			if ( $key == '1' ){
				$sublist['deposit'] = 0;
			}

			foreach ( $sublist['list'] as &$pval){
				if( array_key_exists('tailId',$pval ) && $pval['tailId']>0 ){
					$pval['url'] = $this->homeUrl.'/tailproduct/detail-'.$pval['tailId'].'.html';
				}else{
					$pval['url'] = $this->homeUrl.'/product/detail-'.$pval['productId'].'.html';
				}

				$pval['realPrice'] = ($this->priceType =='1')?$pval['tradePrice']:$pval['price'];
				$pval['sumprice'] = bcmul($pval['num'], $pval['realPrice'],2);
				$sublist['totalPrice'] = bcadd($sublist['totalPrice'],$pval['sumprice'],2);

				if ( $key == '1' ){
					$pval['deposit'] = bcmul($pval['sumprice'], $pval['depositRatio']/100 );
					$sublist['deposit'] = bcadd($sublist['deposit'],$pval['deposit']);
				}
			}

			$realToalPrice = bcadd($realToalPrice,$sublist['totalPrice'],2);

			//尾款
			if ( $key == '1' ){
				$sublist['balanceDue'] = bcsub($sublist['totalPrice'],$sublist['deposit']);
				if( $sublist['deposit']>0 ){
					$hasDeposit = true;
				}
			}
		}
		//订金不能使用货到付款支付。
		if( $hasDeposit && array_key_exists('4',$payModels ) ){
			unset( $payModels['4'] );
		}

		//获取仓库
		$warehouseList = tbWarehouseInfo::model()->getAll( '1' );

		//获取用户的默认分拣仓库
		$saleModel = tbProfile::model()->findByPk( Yii::app()->user->id );
		$defaulthouse= $saleModel->sortingWarehouseId;

		$this->render( 'confirm',array('userType'=>$userType,'list'=>$list,'realToalPrice'=>$realToalPrice,'address'=>$address,'keeyday'=>$keeyday,'productId'=>$productId,'type'=>$type,'deliveryMethod'=>$deliveryMethod,'defaultAddress'=>$defaultAddress,'payModels'=> array_values( $payModels),'warehouseList'=>$warehouseList,'defaulthouse'=>$defaulthouse ) );
	}

}

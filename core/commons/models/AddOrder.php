<?php
/**
* 生成订单
* @version 0.2
* @package CFormModel
*/

class AddOrder extends CFormModel {

	private $_obj;

	public $orderids;

	public $needPay;

	function __construct( $source ='web',$memberId = null ,$userType = null ) {
		parent::__construct();

		$utype = Yii::app()->user->getState('usertype');
		if( !empty( $utype ) ){
			$userType = $utype;
			$memberId = Yii::app()->user->id ;
		}

		$className = ucfirst($userType) . 'AddNewOrder';
		if( class_exists($className,true) ) {
			$this->_obj = new $className( $memberId,$source );
			return;
		}

		throw new CHttpException(500,'No Class Instance');
	}

	/**
	 * 购物车确定购买
	 * @param string $account 账号
	 * @return true/false
	 *
	 */
	public function add( $dataArr ){
		$result = $this->_obj->add( $dataArr );
		return $this-> doResult( $result );
	}

	/**
	 * 购物车确定购买---普通产品
	 * @param array $dataArr 提交的购买信息
	* @param array $productData 产品信息
	 */
	public function addBuyNow(  $dataArr,$productData ){
		$result = $this->_obj->addBuyNow(  $dataArr,$productData );
		return $this->doResult( $result );
	}


	/**
	 * 购物车确定购买---尾货产品
	 * @param array $dataArr 提交的购买信息
	* @param array $productData 产品信息
	 */
	public function addTailBuyNow(  $dataArr ){
		$result = $this->_obj->addTailBuyNow(  $dataArr );
		return $this->doResult( $result );
	}



	private function doResult( $result ){
		if( !$result ){
			$this->addErrors( $this->_obj->getErrors() );
		}else{
			if( count ( $this->_obj->orderids )>1 ){
				$this->_obj->needPay = false;
			}
			$this->orderids = $this->_obj->orderids;
			$this->needPay =  $this->_obj->needPay;
		}
		return $result;
	}
}



class AddNewOrder extends CFormModel {

	public $addressId;

	//当前下单所属客户memberId
	public $memberId;

	public $orderType,$deliveryMethod;

	public $memo ='';

	public $iskeep = 0;

	public $freight = 0;

	public $orderids,$batches;

	//是否需要付款
	public $needPay = false;

	public $product;

	//订单来源
	protected $_source;

  //发货仓库ID
   public  $warehouseId;

   //默认分拣仓库ID
   public  $packingWarehouseId;
	/**
	* 创建订单者（操作者）的memberId
	*/
	public $createMemberId;

	function __construct( $memberId,$source ) {
		parent::__construct();
		$this->createMemberId = $memberId;
		$this->_source = $source;
	}

	public function rules()	{
		return array(
			array('memberId,addressId,batches,warehouseId,packingWarehouseId', 'required'),
			array('memberId,iskeep,addressId,deliveryMethod,warehouseId,packingWarehouseId', "numerical","integerOnly"=>true ),
			array('freight', "numerical",'min'=>'0','max'=>'10000' ),
			array('memo','length','max'=>'100'),
			array('memo,product,batches','safe'),
		);
	}


	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签
	*/
	public function attributeLabels() {
		return array(
			'addressId' => '收货地址',
			'orderType' => '订单类型',
			'memberId' => '客户',
			'memo' => '给卖家留言',
			'iskeep' => '是否留货',
			'freight'=>'运费',
			'batches'=>'分批发货信息',
			'warehouseId'=>'发货仓库',
			'packingWarehouseId'=>'默认分拣仓库',
		);
	}

	/**
	* 设置初始data模型
	* @param array $dataArr 前端页面提交的数据
	* @param integer $addCart 立即购买提交订单时,未保存的加入购物车，
	*/
	protected function initData( $dataArr,$addCart = '0' ,&$cartArr = null ){

		if( empty( $dataArr ) || !is_array( $dataArr ) ){
			$this->addError( 'product','无可提交产品' );
			return false;
		}

		$this->addressId =  array_key_exists('addressId',$dataArr)?$dataArr['addressId']:'';
		$this->warehouseId = array_key_exists('warehouseId',$dataArr)?$dataArr['warehouseId']:'';
		$this->packingWarehouseId = array_key_exists('packingWarehouseId',$dataArr)?$dataArr['packingWarehouseId']:'';
		$this->setMemeberId( $dataArr );

		$_model = $cartArr = array();
		$otypes = array('0' => 'spot','1' => 'booking','3' => 'tail');
		foreach ( $otypes as $i=>$tag ){
			if( !array_key_exists($i,$dataArr) ) continue ;

			if( !array_key_exists($tag,$dataArr) || $dataArr[$tag] != '1' ){
				if( $addCart == '1' && in_array($i,array('0','1') ) ){
					foreach ( $dataArr[$i]['product'] as $val ){
						$cartArr[$val['stockId']] = $val['num'];
					}
				}
				unset( $dataArr[$i] );
				continue ;
			}

			$_model[$i] = clone $this;
			$_model[$i]->attributes =  $dataArr[$i];
			$_model[$i]->orderType = $i;
			if( !$_model[$i]->validate() ) {
				$this->addErrors( $_model[$i]->getErrors() );
				return false;
			}
		}
		if( empty($_model) ){
			$this->addError( 'product','无可提交产品' );
			return false;
		}
		return $_model;
	}


	/**
	* 购物车产品确认提交订单
	*/
	public function add( $dataArr ){
		$_model = $this->initData(  $dataArr );
		if( !$_model ){
			return false;
		}

		$cartIds = array();
		foreach ( $_model as $obj ){
			foreach( $obj->product as $val ){
				$cartIds[] = $val['cartId'];
			}
		}

		$productData = $this->getProductInfo( $cartIds );
		if( !$productData ){
			$this->addError( 'product','无可提交产品' );
			return false;
		}

		$saveModels = $this->setSaveModel( $_model,$productData,'cartId' );
		return $this->saveData( $saveModels,$cartIds );
	}

	/**
	* 保存分批信息
	* @param array $batchesData 需要保存的分批发货数据
	*/
	private function saveBatcheModels( $batchesData ){
		$models = array();
		if( is_array( $batchesData )){
			$batches = new tbOrderBatches();
			foreach ( $batchesData as $bval  ){
				$_batches = clone $batches;
				$_batches->attributes = $bval;
				$models[] =  $_batches;
			}
		}
		return $models;
	}

	private function getOrderModel(){
		$orderModel = $this->initOrder();

		$payModel = Yii::app()->request->getPost( 'payModel' );
		if( in_array( $payModel,array( '2','3','4','5' ) ) ){
			$orderModel->payModel = $payModel;
		}

		$address = tbMemberAddress::model()->findOne( $this->addressId ,$orderModel->memberId );
		if( $address ){
			$orderModel->name = $address->name;
			$orderModel->tel = $address->mobile;
			$orderModel->address = tbArea::getAreaStrByFloorId( $address->areaId );
			$orderModel->address .= $address->address;
		}
		return $orderModel;
	}

	/**
	* 普通产品立即购买确认提交订单,没提交的，需要保存到购物车。
	*/
	public function addBuyNow( $dataArr,$product ){
		$cartArr = array();

		$_model = $this->initData(  $dataArr,'1',$cartArr );
		if( !$_model ){
			return false;
		}

		$stockIds = array();
		foreach ( $_model as $obj ){
			foreach( $obj->product as $val ){
				$stockIds[] = $val['stockId'];
			}
		}

		$stocks = tbProductStock::model()->findAllByPk( $stockIds,'state = :state',array(':state'=>0) );
		if( !$stocks ){
			$this->addError( 'product','无可提交产品' );
			return false;
		}

		$productData = array();
		foreach ( $stocks as $val ){
			$productData[$val->stockId] = array_merge($product,$val->attributes );
			$productData[$val->stockId]['color'] = $val->color;
		}

		//加入到购物车
		if(!empty( $cartArr )){
			$Cart = new tbCart();
			$Cart->memberId = $this->createMemberId;
			$productId = $product['productId'];
			$Cart->addCart( $productId, $cartArr );
		}
		$saveModels = $this->setSaveModel( $_model,$productData,'stockId' );
		return $this->saveData( $saveModels );
	}


	/**
	* 尾货产品立即购买确认提交订单
	*/
	public function addTailBuyNow( $dataArr ){
		$_model = $this->initData( $dataArr );
		if( !$_model ){
			return false;
		}

		$orderModel = $this->getOrderModel();

		//判断客户支付价格
		$priceType = tbMember::model()->getPriceType( $this->memberId );
		$OrderProduct = new tbOrderProduct();

		$saveModels  = $amount = array();
		foreach ( $_model as $key=>$obj ){
			$_order = $this->setOrderAttr( clone $orderModel,$obj );
			$amount[] = $_order->freight;
			foreach( $obj->product as $val ){
				$price =  ($priceType =='1')?$val['tradePrice']:$val['price'];
				unset($val['tradePrice'],$val['price'] );

				$_productmodel					 = clone $OrderProduct;

				unset($val['relation']);
				$_productmodel->attributes       = $val;


				$_productmodel->specifiaction	 = tbProductStock::relationTitle( $_productmodel->singleNumber,$color,$stockId );
				$_productmodel->color			= $color;
				$_productmodel->stockId			= $stockId;

				$_productmodel->orderType 		 = $_order->orderType;
				$_productmodel->price	 		 = $price;
				$_productmodel->saleType		 = $val['saleType'];

				$saveModels[$key]['products'][] = $_productmodel;
				$amount[] = bcmul( $_productmodel->price,$_productmodel->num,2 ) ;
			}

			$_order->realPayment = array_sum( $amount );

			//保存分批信息
			$saveModels[$key]['batches'] = $this->saveBatcheModels( $obj->batches );
			$saveModels[$key]['order'] =  $_order;
		}

		return $this->saveData( $saveModels );
	}


	/**
	* 判断当前是否需要支付
	* @param $array $orderType 订单类型
	* @param $boolean $isMPay 是否月结客户
	* @param $boolean $deposit 是否有订金
	*/
	protected function checkNeedPay( $orderType,$payModel,$isMPay,$deposit){
		if( $this->needPay == true ) return;

		if( $payModel>0 && $payModel != '5' ){
			return ;
		}

		//预订订单含有订金，需支付
		if( $orderType ==  tbOrder::TYPE_BOOKING && $deposit ){
			$this->needPay = true;
			return;
		}

		//现货订单非月结客户，需支付
		if ( !$isMPay && $orderType ==  tbOrder::TYPE_NORMAL ){
			$this->needPay = true;
			return;
		}

		//尾货订单需支付
		if( $orderType ==  tbOrder::TYPE_TAIL ){
			$this->needPay = true;
			return;
		}
	}


	/**
	* 设置保存的model
	* @param array $_model
	* @param array $productData
	* @param string $pkey  $productData 的key,购物车确定购买pkey为：'cartId'，直接购买时为：'stockId'。
	*/
	private function setSaveModel( $_model,$productData,$pkey ){
		$orderModel = $this->getOrderModel();

		//判断客户支付价格
		$priceType = tbMember::model()->getPriceType( $this->memberId );

		$OrderProduct = new tbOrderProduct();

		$OrderDeposit = new tbOrderDeposit();
		$OrderDeposit->state	= 0;

		$saveModels = array();

		//取得当前客户支付的批发价格
		$productIds = array_map( function ( $i ){ return $i['productId'];}, $productData );
		$specPrices = tbMemberApplyPrice::model()->getMemberPrice( $this->memberId,$productIds );

		foreach ( $_model as $key=>$obj ){
			$deposit = 0;
			$_order = $this->setOrderAttr( clone $orderModel,$obj );
			$amount = array();
			$amount[] = $_order->freight;
			$saveModels[$key]['products'] = array();

			foreach( $obj->product as $val ){
				$k = $val[$pkey];
				if( !array_key_exists( $k, $productData ) || $productData[$k]['state']!='0' ){
					$this->addError( 'product','您提交的产品不存在或已下架' );
					return false;
				}

				$_productmodel					 = clone $OrderProduct;

				if( array_key_exists( $productData[$k]['productId'],$specPrices ) ){
					$_productmodel->price	 		 = $specPrices[$productData[$k]['productId']];
				}else{
					$_productmodel->price	 		 = ($priceType =='1')?$productData[$k]['tradePrice']:$productData[$k]['price'];
				}


				$_productmodel->saleType 		 = array_key_exists( 'saleType', $productData[$k] )?$productData[$k]['saleType']:'normal';
				$_productmodel->orderType 		 = $_order->orderType;


				if( array_key_exists( 'tailId', $productData[$k]) ){
					$_productmodel->tailId 			 = $productData[$k]['tailId'];
				}
				$_productmodel->productId 		 = $productData[$k]['productId'];
				$_productmodel->title 			 = $productData[$k]['title'];
				$_productmodel->serialNumber 	 = $productData[$k]['serialNumber'];
				$_productmodel->mainPic		 	 = $productData[$k]['mainPic'];
				$_productmodel->singleNumber	 = $productData[$k]['singleNumber'];
				$_productmodel->specifiaction	 = tbProductStock::relationTitle( $_productmodel->singleNumber,$color,$stockId );
				$_productmodel->color			 =  empty($color) ? $productData[$k]['color']:$color;
				$_productmodel->stockId			 = $stockId;
				$_productmodel->num			 	 =  $val['num'];

				if( $_order->orderType == tbOrder::TYPE_NORMAL ){
					$_productmodel->isSample 		 = $this->setIsSample( $val );
				}



				//如果是整批销售，那么数量取 $productData里的数量,从数据库里读取单品信息和数量
				if( $_productmodel->saleType =='whole' ){
					$tbTail = tbTail::model()->findByPk( $_productmodel->tailId,'state = :state and isSoldOut = 0 ',array( ':state'=>'selling' ) );
					if( !$tbTail ){
						$this->addError( 'productId','尾货产品已下架或已售完' );
						return false;
					}

					foreach ( $tbTail->single as $_single ){
						$stockNum = ProductModel::total( $_single->singleNumber, true );
						if( $stockNum >0 ){
							$_tailproductmodel 					= clone $_productmodel;
							$_tailproductmodel->num 			= $stockNum;
							$_tailproductmodel->singleNumber	= $_single->singleNumber;
							$_tailproductmodel->specifiaction	= tbProductStock::relationTitle( $_single->singleNumber,$color,$stockId );
							$_tailproductmodel->color			= $color;
							$_tailproductmodel->stockId 		= $stockId;

							$sumprice =  bcmul( $_tailproductmodel->num, $_tailproductmodel->price,2 );
							$amount[] = $sumprice;

							$saveModels[$key]['products'][] = $_tailproductmodel;
						}
					}
				}else{
					$saveModels[$key]['products'][] = $_productmodel;

					//赠板不算钱
					if( $_productmodel->isSample != '1' ){
						$sumprice =  bcmul( $_productmodel->num, $_productmodel->price,2 );
						$amount[] = $sumprice;

						//计算订金
						if ( $_order->orderType == tbOrder::TYPE_BOOKING && $productData[$k]['depositRatio']>0 ){
							$pdeposit =  bcmul( $sumprice, $productData[$k]['depositRatio']/100,2 );
							$deposit = bcadd( $deposit,$pdeposit,2 );
						}
					}
				}

				if( empty($saveModels[$key]['products']) ){
					$this->addError( 'productId','尾货产品已下架或已售完' );
					return false;
				}


			}

			$_order->realPayment = array_sum( $amount );

			if( $deposit >0 ){
				$_deposit = clone $OrderDeposit;
				$_deposit->amount = $deposit;
				$saveModels[$key]['deposit']['0'] =  $_deposit;

				$_order->hasDeposit = true;
			}

			//保存分批信息
			$saveModels[$key]['batches'] = $this->saveBatcheModels( $obj->batches );
			$saveModels[$key]['order'] =  $_order;
		}

		return $saveModels;
	}


	/**
	* 保存订单到订单数据库表
	* @param array $saveModels 要保存的数据库模型
	* @param array $cartIds  要删除的购物车IDS
	*/
	private function saveData( $saveModels,$cartIds = null ){
		if( !$this->checkMonthPay( $saveModels ) ){
			return false;
		}

		$transaction = Yii::app()->db->beginTransaction();
		try {
			foreach ( $saveModels as $val ){
				$order = $val['order'];
				if( !$order->save() ){
					$this->addErrors( $order->getErrors() );
					return false;
				}

				unset( $val['order'] );
				foreach ( $val as $_models ){
					foreach ( $_models as $_model ){
						$_model->orderId = $order->orderId;
						if( !$_model->save() ){
							$this->addErrors( $_model->getErrors() );
							return false;
						}
					}
				}

				$this->orderids[] = $order->orderId;
				$this->afterAddOrders( $order );
			}

			if( !empty( $cartIds ) ){
				//保存完成后删除购物车中已提交的内容
				$Cart =  new tbCart();
				$Cart->memberId = $this->createMemberId;
				$Cart->del( $cartIds );
			}

			$transaction->commit();

		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			throw new CHttpException(500,$e);
			return false;
		}

		return true;
	}

	/**
	* 根据购物车ID取得相应的产品的信息
	* @param array $cartIds
	*/
	private function getProductInfo( $cartIds ){
		if( empty ( $cartIds ) ){
			return ;
		}

		//取得产品信息
		$cart = new tbCart();
		$cart->memberId = $this->createMemberId ;
		$data =  $cart->allCartLists( $cartIds );

		$productData = array();
		foreach ( $data as $val ){
			$key = $val['cartId'];
			$productData[$key] = $val;
		}

		return $productData;
	}

	/**
	* 判断月结支付信息，若是月结客户，业务员下单时需判断信用额度是否足够支付除订金外的货款，信用额度不足时不让下单
	* @param $array $saveModels 需保存的数据模型
	*/
	protected function checkMonthPay( &$saveModels ){
		$creditInfo = tbMemberCredit::creditInfo( $this->memberId );
		$isMPay = empty( $creditInfo )?false:true;

		$paynum = 0;
		foreach ( $saveModels as &$_model ){
			//留货订单，跳过
			if( $_model['order']->orderType == tbOrder::TYPE_KEEP ||  $_model['order']->orderType == tbOrder::TYPE_TAIL ){
				continue;
			}

			if( $isMPay ){
				//订算本次订单所需付款，并生成对应的信用使用记录
				$_model['creditDetail'] = array();

				$balanceDue = $_model['order']->realPayment;
				if( $_model['order']->hasDeposit ){
					$deposit = $_model['deposit']['0']->amount;
					//尾款
					$balanceDue = bcsub( $balanceDue,$deposit,2 );
				}else{
					$_model['order']->payModel = 1; //标记支付方式为月结
				}

				$creditDetail = $this->initCreditDetail();
				$creditDetail->amount = $balanceDue;
				$creditDetail->memberId =  $_model['order']->memberId;
				$_model['creditDetail'][] = $creditDetail;

				$paynum = bcadd($paynum,$balanceDue,2);
			}

			$this->checkNeedPay( $_model['order']->orderType,$_model['order']->payModel,$isMPay,$_model['order']->hasDeposit);
		}

		return $this->checkValidCredit( $creditInfo['validCredit'], $paynum );
	}

}

/**
* 业务员下单
*/
class SalemanAddNewOrder extends AddNewOrder {

	/**
	* 业务员下单，新建order model并初始化值
	*/
	public function initOrder(){
		$orderModel = new tbOrder();
		$orderModel->source = $this->_source;
		//业务员下单,memberId从前端传值
		$orderModel->memberId = $this->memberId;
		$orderModel->userId = $this->createMemberId;
		$orderModel->originatorType = 1;
		return $orderModel;
	}

	//业务员下单,memberId从前端传值
	protected function setMemeberId( $dataArr ){
		$this->memberId =  array_key_exists('memberId',$dataArr)?$dataArr['memberId']:'';
	}

	/**
	* 业务员下单，设置order model值
	*/
	protected function setOrderAttr( $_order,$obj ){
		$_order->setAttributes( $obj->getAttributes( array('orderType','memo','freight','deliveryMethod','warehouseId','packingWarehouseId') ) );
		if( $obj->iskeep == '1' ){
			$_order->state = '0';
			$_order->orderType = '2'; //留货订单,留货订单需审核,留货不需要支付，确定购买后才支付。
		}else{
			$_order->state = '1';//业务员下单时，不需要审核
		}
		return $_order;
	}

	/**
	* 设置是否为样板
	*/
	protected function setIsSample( $val ){
		if ( array_key_exists ('isSample', $val) && $val['isSample']=='1' && $val['num']<=5 ){
			return 1;
		}

		return 0;
	}

	/**
	* 判断月结可用额度，业务员下单时需判断信用额度是否足够支付除订金外的货款，信用额度不足时不让下单
	* @param $number $validCredit 当前可用额度
	* @param $number $validCredit 当前需使用额度
	*/
	protected function checkValidCredit( $validCredit,$needPay ){
		$comp = bccomp( $validCredit, $needPay );
		if( $comp<0 ){
			$this->addError('memberId','客户信用额度不足以支付当前下单总金额');
			return false;
		}
		return true;
	}

	/**
	* 初始化月结信用使用记录模型
	*/
	protected function initCreditDetail(){
		$model = new tbMemberCreditDetail();
		$model->isCheck = '1';
		return $model;
	}


	/**
	* 订单保存成功后的后续操作，业务员下单，月结客户的现货订单和无订金的预订订单，直接进入下一流程。
	* 根据订单类型，进行相对应的操作。
	* 注释的部分在tbOrder 的afterSave() 方法里。
	*/
	protected function afterAddOrders( $order ){

		//生成订单追踪信息
		tbOrderMessage::addMessage( $order->orderId,'saleman_add',$order->source );

		//现货订单，若为月结，PUSH进待分配，非月结需走支付流程
		if( $order->orderType == tbOrder::TYPE_NORMAL ){
			$falg = tbOrderDistribution::addOne( $order->orderId );
		}else if( $order->orderType == tbOrder::TYPE_BOOKING ){
			//预定订单，若无订金，直接进入待采购，有订金需支付订金
			if( !$order->hasDeposit ){
				tbOrderPurchase2::importOrder( $order );
			}
		}else{
			//留货
			/** $addModel = new tbOrderKeep();
			$addModel->orderId = $order->orderId;
			$keeyday = tbConfig::model()->get( 'order_keep_time' );
			$addModel->expireTime = strtotime( $keeyday.' days');
			if( !$addModel->save() ) {
				$this->addErrors( $addModel->getErrors() );
				return false;
			} */
		}
	}
}

/**
* 客户下单
*/
class MemberAddNewOrder extends AddNewOrder {

	//业务员下单,memberId从前端传值
	protected function setMemeberId( $dataArr ){
		$this->memberId = $this->createMemberId ;
	}

	/**
	* 客户下单，新建order model并初始化值
	*/
	public function initOrder(){
		$orderModel = new tbOrder();
		$orderModel->source = $this->_source;
		$orderModel->memberId = $this->createMemberId ; //客户下单
		$orderModel->originatorType = 0;

		//查找客户对应的userId
		$member = tbMember::model()->findByPk( $orderModel->memberId );
		$orderModel->userId = $member->userId;

		return $orderModel;
	}

	/**
	* 客户下单，设置order model值
	*/
	protected function setOrderAttr( $_order,$obj ){
		$_order->setAttributes( $obj->getAttributes( array('orderType','memo','freight','deliveryMethod','warehouseId','packingWarehouseId') ) );
		return $_order;
	}

	/**
	* 是否样板赋值
	*/
	protected function setIsSample( $val ){
		return 0;
	}

	/**
	* 判断月结可用额度，客户下单暂时不判断
	* @param $number $validCredit 当前可用额度
	* @param $number $validCredit 当前需使用额度
	*/
	protected function checkValidCredit( $validCredit,$needPay ){
		return true;
	}

	/**
	* 订单保存成功后的后续操作
	* 根据订单类型，进行相对应的操作。
	*/
	protected function afterAddOrders( $order ){
		//生成订单追踪信息
		tbOrderMessage::addMessage( $order->orderId,'member_add',$order->source );
	}

	/**
	* 初始化月结信用使用记录模型
	*/
	protected function initCreditDetail(){
		$model = new tbMemberCreditDetail();
		$model->isCheck = '0';
		return $model;
	}
}

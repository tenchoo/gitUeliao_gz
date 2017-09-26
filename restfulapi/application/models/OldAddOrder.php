<?php
/**
* 生成订单
* @version 0.1
* @package CFormModel
*/
class OldAddOrder extends CFormModel {

	public $addressId;

	public $tel;

	public $name;

	public $memberId;

	public $orderType;

	public $product;

	public $memo ='';

	public $iskeep = 0;

	public $userId = 0;

	public $freight = 0;

	public $orderids,$batches,$logistics,$deliveryMethod;

	public $createMemberId;//创建订单的memberId

	//是否需要付款
	public $needPay = false;

	/**
	* 用户类型
	*/
	public $userType;

	private $_source;


	function __construct( $source,$memberId,$userType) {
		parent::__construct();

		$this->userType = $userType;
		$this->createMemberId = $memberId;
		$this->_source = $source;
		if( $this->userType == tbMember::UTYPE_SALEMAN ){
			$this->userId = $memberId;
		}else{
			$this->memberId = $memberId;
		}
	}


	public function rules()	{
		return array(
			array('addressId,batches', 'required'),
			array('memberId,iskeep,addressId,logistics,deliveryMethod', "numerical","integerOnly"=>true ),
			array('freight', "numerical" ),
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
			'memberId' => '客户ID',
			'memo' => '给卖家留言',
			'iskeep' => '是否留货',
			'freight'=>'运费',
			'batches'=>'分批发货信息',
		);
	}

	/**
	* 设置初始data模型
	* @param array $dataArr 前端页面提交的数据
	* @param integer $addCart 立即购买提交订单时,未保存的加入购物车，
	*/
	private function initData( array $dataArr,$addCart = '0' ){
		$this->addressId = isset($dataArr['addressId'])?$dataArr['addressId']:'';

		//业务员下单,memberId从前端传值
		if( $this->userType == tbMember::UTYPE_SALEMAN ){
			$this->memberId = isset( $dataArr['memberId'] )?$dataArr['memberId']:'';
		}

		$cartArr = array();
		if( isset ( $dataArr['0'] ) ){
			if( !isset($dataArr['spot']) || $dataArr['spot'] != '1' ){
				if( $addCart == '1'){
					foreach ( $dataArr['0']['product'] as $val ){
						$cartArr[$val['stockId']] = $val['num'];
					}
				}
				unset( $dataArr['0'] );
			}
		}

		if( isset ( $dataArr['1'] ) ){
			if( !isset($dataArr['booking']) || $dataArr['booking'] != '1' ){
				if( $addCart == '1'){
					foreach ( $dataArr['1']['product'] as $val ){
						$cartArr[$val['stockId']] = $val['num'];
					}
				}
				unset( $dataArr['1'] );
			}
		}


		if(!empty( $cartArr )){
			$Cart =  new Cart( $this->createMemberId,$this->userType);
			$Cart->productId = Yii::app()->request->getPost( 'productId' );
			$Cart->cart = $cartArr;
			$Cart->save();
		}


		$_model = array();
		for ( $i=0;$i<2;$i++){
			if( !isset( $dataArr[$i] ) ){
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
	* 保存订单到订单数据库表
	* @param CFormModel $obj
	*/
	private function saveOrder( $obj ){
		$model = new tbOrder();
		$model->source = $this->_source;
		$model->setAttributes( $obj->getAttributes( array('memberId','userId','orderType','memo','freight','deliveryMethod') ) );

		if( $this->userType == tbMember::UTYPE_SALEMAN ){
			$model->originatorType = '1';
			if( $obj->iskeep == '1' ){
				$model->orderType = '2'; //留货订单,留货订单需审核
			}else{
				$model->state = '1';//业务员下单时，不需要要审核,进入备货中
			}
		}else{
			$model->originatorType = '0';
			$model->memberId = $this->memberId;

			//查找客户对应的userId
			$member = tbMember::model()->findByPk( $model->memberId );
			$model->userId = $member->userId;
		}

		$address = tbMemberAddress::model()->findOne( $this->addressId ,$model->memberId );
		if( $address ){
			$model->name = $address->name;
			$model->tel = $address->mobile;
			$model->address = tbArea::getAreaStrByFloorId( $address->areaId );
			$model->address .= $address->address;

		}

		if( !$model->save() ) {
			$this->addErrors( $model->getErrors() );
			return false;
		}

		//保存分批信息
		if( is_array( $obj->batches )){
			$batches = new tbOrderBatches();
			$batches->orderId = $model->orderId;
			foreach ( $obj->batches as $bval  ){
				$_batches = clone $batches;
				$_batches->attributes = $bval;

				if( !$_batches->save() ) {
					$this->addErrors( $_batches->getErrors() );
					return false;
				}
			}

		}
		return $model;
	}


	/**
	* 立即购买确认提交订单,没提交的，需要保存到购物车。
	*/
	public function addBuyNow( $dataArr,$productData ){
		$_model = $this->initData(  $dataArr,$addCart = '1' );
		if( !$_model ){
			return false;
		}

		$this->orderids = array();
		//使用事务处理
		$transaction = Yii::app()->db->beginTransaction();
		try {
			foreach ( $_model as $obj ){
				$model = $this->saveOrder( $obj );
				if( !$model ){
					return false;
				}
				$model->realPayment = $model->freight;
				$OrderProduct = new tbOrderProduct();
				$OrderProduct->orderId = $model->orderId ;
				$OrderProduct->orderType = $model->orderType ;
				foreach( $obj->product as $val ){
					$_model = clone $OrderProduct;
					$val['relation']	 = trim($val['relation']);
					$spec					 = explode(':',$val['relation']);
					$spec					 = explode(' ',$spec['1']);
					$_model->color	 		 = $spec['0'];
					$_model->specifiaction	 = $val['relation'];
					$_model->num			 = $val['num'];
					$_model->price	 		 = $val['price'];
					$_model->productId 		 = $productData['productId'];
					$_model->stockId 		 = $val['stockId'];
					$_model->singleNumber 	 = $val['singleNumber'];
					$_model->title 			 = $productData['title'];
					$_model->serialNumber 	 = $productData['serialNumber'];
					$_model->mainPic		 = $productData['mainPic'];
					if( isset($val['isSample']) && $val['isSample']=='1' && $val['num']<=5 ){
						$_model->isSample	= 1;
					}

					if( !$_model->save() ) {
						$this->addErrors( $_model->getErrors() );
						return false;
					}

					//赠板不算钱
					if( $_model->isSample != '1' ){
						$model->realPayment += $val['num']*$val['price'];
					}
				}
				$model->save();
				$this->orderids[] = $model->orderId;
				if( $model->payState == '0' ){
					$this->needPay = true;
				}
			}
			$transaction->commit();
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			throw new CHttpException(503, $e);
			//$this->addError( 'memberId','发生系统错误' );
			return false;
		}
		$this->afterAddOrders();
		return true;
	}

	/**
	* 购物车产品确认提交订单
	*/
	public function add( $dataArr ){
		if( empty( $dataArr ) || !is_array( $dataArr ) ){
			$this->addError( 'product','无可提交产品' );
			return false;
		}
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

		$this->orderids = array();
		//使用事务处理
		$transaction = Yii::app()->db->beginTransaction();
		try {
			foreach ( $_model as $val ){
				if( !$this->save( $val ,$productData ) ){
					return false;
				}
			}

			//保存完成后删除购物车中已提交的内容
			$model =  new Cart($this->createMemberId,$this->userType);
			$model->delete( $cartIds );

			$transaction->commit();
		} catch (Exception $e) {
			print_r($e);
			$transaction->rollback(); //如果操作失败, 数据回滚
			$this->addError( 'memberId','发生系统错误' );
			return false;
		}
		$this->afterAddOrders();
		return true;
	}

	/**
	* 提交订单
	*/
	private function save( $obj ,$productData ){
		$model = $this->saveOrder( $obj );
		if( !$model ){
			return false;
		}
		$model->realPayment = $model->freight;
		$OrderProduct = new tbOrderProduct();
		$OrderProduct->orderId = $model->orderId ;
		$OrderProduct->orderType = $model->orderType ;
		foreach( $obj->product as $val ){
			$k = $val['cartId'];
			$_model = clone $OrderProduct;
			$_model->num			 = $val['num'];
			$_model->price	 		 = $val['price'];
			$_model->productId 		 = $productData[$k]['productId'];
			$_model->stockId 		 = $productData[$k]['stockId'];
			$_model->title 			 = $productData[$k]['title'];
			$_model->serialNumber 	 = $productData[$k]['serialNumber'];
			$_model->mainPic		 = $productData[$k]['mainPic'];
			$_model->specifiaction	 = $productData[$k]['relation'];
			$_model->color			 = $productData[$k]['color'];
			$_model->singleNumber	 = $productData[$k]['singleNumber'];

			if( isset($val['isSample']) && $val['isSample']=='1' && $val['num']<=5 ){
				$_model->isSample	= 1;
			}

			if( !$_model->save() ) {
				$this->addErrors( $_model->getErrors() );
				return false;
			}

			//赠板不算钱
			if( $_model->isSample != '1' ){
				$model->realPayment += $val['num']*$val['price'];
			}
		}
		$model->save();
		$this->orderids[] = $model->orderId;
		if( $model->payState == '0' ){
			$this->needPay = true;
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
		//取得产品信息
		$cart = new tbCart();
		$cart->memberId = $this->createMemberId;
		$data =  $cart->cartLists( $cartIds );
		$productData = array();
		foreach ( $data as $val ){
			if ( !isset($val['color'] ) ) $val['color'] = '';
			$key = $val['cartId'];
			$productData[$key] = $val;
		}
		return $productData;
	}

	/**
	* 订单保存成功后，若是业务员下单，无需审核，直接进入下一流程。
	* 根据订单类型，进行相对应的操作。
	*/
	public function afterAddOrders(){
		if(   $this->userType != tbMember::UTYPE_SALEMAN  || empty( $this->orderids )) return ;

		$c = new CDbCriteria();
		$c->compare('orderId',$this->orderids);
		$c->compare('orderType','1'); //采购订单，往待采购表加数据。
		$model = tbOrder::model()->find( $c );
		if( $model ){
			$this->attachEventHandler('onAfterSave', array("tbOrderPurchase2","importOrder"));
			$this->onAfterSave( $model );
		}
	}

	protected function onAfterSave( $event ) {
		$this->raiseEvent('onAfterSave', $event);
	}
}

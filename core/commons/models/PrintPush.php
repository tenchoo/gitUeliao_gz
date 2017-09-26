<?php
/**
* 单据打印
* @package CFormModel
*/
class PrintPush extends CFormModel
{
	/**
	* 打印结算单
	* @param integer $id  结算单ID
	* @param string $msg 返回信息
	* @param boolean $isManage
	*/
	public static function printSettlement( $id,&$msg,$isManage = false  ){
		$msg = '添加进打印队列失败';

		$model = null;
		if( is_numeric( $id ) && $id>0  ){
			$model = tbOrderSettlement::model()->findByPk( $id );
		}

		if( !$model ){
			$msg = '你请求打印的对象不存在';
			return false;
		}

		$order = $model->order;
		if(  $isManage ){
			//取得打印号
			$user = tbUser::model()->findByPk( Yii::app()->user->id );
		}else{
			//如果是前台打印，判断一下是否为当前账号服务客户
			$isserve = tbMember::checkServe( $order->memberId,Yii::app()->user->id );
			if( !$isserve ){
				$msg = '你无权打印此结算单';
				return false;
			}

			$user = tbMemberSaleman::model()->findByPk( Yii::app()->user->id );
			if( !$user ){
				$msg = '请先配置打印机';
				return false;
			}
		}

		$tbPrintOrder = new tbPrintOrder();
		$tbPrintOrder->orderId = $model->orderId;//结算单ID $model->settlementId;
		$tbPrintOrder->saleOrderId = $model->orderId;//销售订单ID
		$tbPrintOrder->order_type = 1;//结算单为1

		$customInfo = tbProfileDetail::model()->findByPk( $order->memberId );
		$tbPrintOrder->custom_name = $customInfo->companyname;
		$tbPrintOrder->custom_phone = $order->tel;

		if( $model->type == '0' ){
			$member = tbProfile::model()->findByPk( $model->originatorId );
		}else{
			$member = tbUser::model()->findByPk( $model->originatorId );
		}

		$tbPrintOrder->create_by = $member->username;

		$orderClass = new Order();
		$tbPrintOrder->delivery = $orderClass->deliveryMethod( $order->deliveryMethod );
		$tbPrintOrder->payMethod = '';
		if( $order->payModel ){
			$payment = tbPayMent::model()->findByPk( $order->payModel );
			if( $payment ){
				$tbPrintOrder->payMethod = $payment->paymentTitle;
			}
		}
		$tbPrintOrder->create_time = strtotime ( $model->createTime );
		$tbPrintOrder->quality = ''; //质检
		$tbPrintOrder->cutting = ''; //剪料
		$tbPrintOrder->hoseware = ''; //仓管

		$saveDetailModel = array();

		//明细
		$tbPrintOrderDetail = new tbPrintOrderDetail();
		$tbPrintOrderDetail->orderId = $tbPrintOrder->orderId;
		$tbPrintOrderDetail->detail = '';
		$productIds = array();
		foreach ( $order->products as $_product ){
			$printDetail = clone $tbPrintOrderDetail;
			$printDetail->product = $_product->singleNumber;
			$printDetail->total = $_product->num;

			//传实际销售价格，若是赠板，则传0
			$printDetail->price = ($_product->isSample == '1')?0: $_product->price;
			$printDetail->subprice = bcmul ( $printDetail->price,$_product->num,2 );

			$printDetail->mark = $_product->remark;
			$printDetail->unit = $_product->productId; //先塞productId,后再根据productId转成单位名称

			$productIds[] = $_product->productId;
			$saveDetailModel[] = $printDetail;
		}

		return self::saveData( $tbPrintOrder,$saveDetailModel,$user->printerId,$productIds, $msg );
	}



	/**
	* 打印分拣单
	* @param integer $id  分拣单ID
	* @param string $msg 返回信息
	*/
	public static function printPacking( $id,&$msg ){
		$msg = '添加进打印队列失败';

		$model = null;
		if( is_numeric( $id ) && $id>0  ){
			$model = tbPacking::model()->findByPk( $id );
		}

		if( !$model ){
			$msg = '你请求打印的对象不存在';
			return false;
		}

		$tbPrintOrder = new tbPrintOrder();
		$tbPrintOrder->orderId = $model->packingId;//分拣单ID
		$tbPrintOrder->saleOrderId = $model->orderId;//销售订单ID

		$tbPrintOrder->order_type = 2;//分拣单为2

		$order = $model->order;
		$customInfo = tbProfileDetail::model()->findByPk( $order->memberId );
		$tbPrintOrder->custom_name = $customInfo->companyname;
		$tbPrintOrder->custom_phone = $order->tel;

		$orderClass = new Order();
		$tbPrintOrder->delivery = $orderClass->deliveryMethod( $order->deliveryMethod );
		$tbPrintOrder->create_time = strtotime ( $model->createTime );

		$tbPrintOrder->quality = '';//质检002
		$tbPrintOrder->cutting = tbUser::model()->getUserName( $model->packingerId );//剪料,分拣员
		$tbPrintOrder->hoseware = '';//仓管03

		$saveDetailModel = array();

		//明细
		$tbPrintOrderDetail = new tbPrintOrderDetail();
		$tbPrintOrderDetail->detail = '';
		$tbPrintOrderDetail->orderId = $tbPrintOrder->orderId;
		$productIds = array();

		if(  $model->state == '0' ){
			if( $order->state == '7' ){
				$msg ='订单已取消，不能打印分配分拣单';
				return false;
			}

			$distModel = tbDistribution::model()->findByPk( $model->distributionId );
			$tbPrintOrder->create_by = tbUser::model()->getUserName( $distModel->userId );
			$details = tbDistributionDetail::model()->findAll( 'distributionId=:id and warehouseId = :wid ',array(':id'=>$model->distributionId,':wid'=>$model->warehouseId ) );
			foreach ( $details as $_product ){
				$printDetail = clone $tbPrintOrderDetail;
				$printDetail->product = $_product->singleNumber;
				$printDetail->total = $_product->distributionNum;

				$printDetail->price = 0;
				$printDetail->subprice = 0;
				$printDetail->batch = $_product->productBatch;
				$printDetail->unit = $_product->productId; //先塞productId,后再根据productId转成单位名称

				$printDetail->position = $_product->positionTitle;
				$printDetail->mark = $_product->productBatch;
				$productIds[] = $_product->productId;


				$saveDetailModel[] = $printDetail;
			}
		}else{
			$tbPrintOrder->create_by = tbUser::model()->getUserName( $model->userId );
			$details = $model->detail;
			foreach ( $details as $_product ){
				$printDetail = clone $tbPrintOrderDetail;
				$printDetail->product = $_product->singleNumber;
				$printDetail->total = $_product->packingNum;

				$printDetail->price = 0;
				$printDetail->subprice = 0;
				$printDetail->batch = $_product->productBatch;
				$printDetail->unit = $_product->productId; //先塞productId,后再根据productId转成单位名称

				$printDetail->position = '';
				$position = tbWarehousePosition::model()->findByPk( $_product->positionId );
				if( $position ){
					$printDetail->position = $position->title;
				}

				$printDetail->mark = $_product->productBatch;
				$productIds[] = $_product->productId;


				$saveDetailModel[] = $printDetail;
			}
		}

		//取得打印号
		//$user = tbUser::model()->findByPk( Yii::app()->user->id );
		$printmodel = tbWarehousePrinter::model()->find( 'warehouseId =:wid' ,array(':wid'=>$model->warehouseId ) );
		if( $printmodel ){
			$printerId = $printmodel->printerId;
		}else{
			$printerId = 0;
		}

		return self::saveData( $tbPrintOrder,$saveDetailModel,$printerId,$productIds, $msg );
	}

	/**
	* 打印退货单
	* @param integer $id  分拣单ID
	* @param string $msg 返回信息
	*/
	public static function printRefund( $id,&$msg,$isManage = false ){
		$msg = '添加进打印队列失败';

		$OrderRefund = new OrderRefund( Yii::app()->user->id,Yii::app()->user->getState('usertype') );
		$data = $OrderRefund->getOne( $id,null,$isManage );


		if( empty( $data ) ){
			$msg = '你请求打印的对象不存在';
			return false;
		}

		$tbPrintOrder = new tbPrintOrder();
		$tbPrintOrder->orderId = $data['refundId'];//退货单ID
		$tbPrintOrder->saleOrderId = $data['orderId'];//销售订单ID

		$tbPrintOrder->order_type = 3;//退货单为3
		$tbPrintOrder->custom_name = $data['member']['companyname'];
		$tbPrintOrder->custom_phone = $data['member']['tel'];
		$tbPrintOrder->delivery = '';
		$tbPrintOrder->payMethod = $data['payModel'];
		$tbPrintOrder->create_time = strtotime ( $data['createTime'] );
		$tbPrintOrder->create_by = '';

		$tbPrintOrder->quality = '';//质检002
		$tbPrintOrder->cutting = '';//剪料,分拣员
		$tbPrintOrder->hoseware = '';//仓管03

		$saveDetailModel = array();

		//明细
		$tbPrintOrderDetail = new tbPrintOrderDetail();
		$tbPrintOrderDetail->orderId = $tbPrintOrder->orderId;
		$productIds = array();

		foreach ( $data['products'] as $val ){
			$_detail = clone $tbPrintOrderDetail;
			$_detail->unit = $val['productId'];
			$_detail->price = $val['price'];
			$_detail->mark = $val['color'];
			$_detail->detail = $val['color'];
			$_detail->product = $val['singleNumber'];
			$_detail->total = $val['num'];

			$productIds[] = $val['productId'];
			$saveDetailModel[] = $_detail;
		}


		if(  $isManage ){
			//取得打印号
			$user = tbUser::model()->findByPk( Yii::app()->user->id );
		}else{
			//如果是前台打印
			$user = tbMemberSaleman::model()->findByPk( Yii::app()->user->id );
			if( !$user ){
				$msg = '请先配置打印机';
				return false;
			}
		}
		return self::saveData( $tbPrintOrder,$saveDetailModel,$user->printerId,$productIds, $msg );
	}

	/**
	* 打印备货单
	* @param integer $orderId  订单ID
	* @param string $msg 返回信息
	*/
	public static function printOrderProduct( $orderId,&$msg ){
		$msg = '添加进打印队列失败';

		$OrderModel = tbOrder::model()->findByPk( $orderId,'state !=0 and state !=7' );
		if( empty( $OrderModel ) ){
			$msg = '你请求打印的对象不存在';
			return false;
		}

		$packings = tbPack::model()->findAllByAttributes( array('orderId'=>$orderId,'state'=>array(1,2) ) );
		if( !$packings ){
			$msg ='订单未分拣';
			return false;
		}

		$tbPrintOrder = new tbPrintOrder();
		$tbPrintOrder->orderId = $OrderModel->orderId;//订单ID
		$tbPrintOrder->saleOrderId = $OrderModel->orderId;//销售订单ID
		$tbPrintOrder->create_by = ''; //制单人

		$tbPrintOrder->order_type = 2;//分拣单为2

		$customInfo = tbProfileDetail::model()->findByPk( $OrderModel->memberId );
		$tbPrintOrder->custom_name = $customInfo->companyname;
		$tbPrintOrder->custom_phone = $OrderModel->tel;

		$orderClass = new Order();
		$tbPrintOrder->delivery = $orderClass->deliveryMethod( $OrderModel->deliveryMethod );
		$tbPrintOrder->create_time = strtotime ( $OrderModel->createTime );		

		$products = $OrderModel->products;
		$printData = $productIds = array();

		foreach ( $products as $_product ){
			$printData[$_product->orderProductId] = array(
									'product' => $_product->singleNumber,
									'price' => $_product->price,
									'total' => $_product->packingNum,
									'unit' => $_product->productId,
									'detail'=>$_product->remark,
									'position'=>'' //这里不打印仓位，打印分区
								);
			$productIds[] = $_product->productId;
		}

		foreach( $packings  as $val ){
			//分拣仓库
			$warehouseId = $val->warehouseId;
			$packUserId = $val->packUserId;
			$positionTitle = tbWarehousePosition::model()->positionName( $val->positionId,$w );
			$printData[$val->orderProductId]['position'] = $positionTitle;
		}

		$tbPrintOrder->quality = '';//质检002
		$tbPrintOrder->cutting = tbUser::model()->getUserName( $packUserId );//剪料,分拣员
		$tbPrintOrder->hoseware = '';//仓管03

		$saveDetailModel = array();

		//明细
		$tbPrintOrderDetail = new tbPrintOrderDetail();
		$tbPrintOrderDetail->orderId = $tbPrintOrder->orderId;
		$tbPrintOrderDetail->subprice = 0;
		$tbPrintOrderDetail->batch = '';
		$tbPrintOrderDetail->mark = '';

		foreach ( $printData as $val ){
			$printDetail = clone $tbPrintOrderDetail;
			$printDetail->product = $val['product'];
			$printDetail->total = $val['total'];
			$printDetail->price = $val['price'];
			$printDetail->unit = $val['unit'];
			$printDetail->detail = $val['detail'];
			$printDetail->position = $val['position'];
			$saveDetailModel[] = $printDetail;
		}


		//取得打印号
		$printmodel = tbWarehousePrinter::model()->find( 'warehouseId =:wid' ,array(':wid'=>$warehouseId ) );
		if( $printmodel ){
			$printerId = $printmodel->printerId;
		}else{
			$printerId = 0;
		}

		return self::saveData( $tbPrintOrder,$saveDetailModel,$printerId,$productIds, $msg );
	}

	/**
	* 打印备货标签
	* @param integer $orderProductId  订单产品ID
	* @param string $msg 返回信息
	*/
	public static function printOrderTag( $orderProductId,&$msg ){
		$msg = '添加进打印队列失败';
		
		$packings = tbPack::model()->findByPk( $orderProductId,'state in(1,2)' );
		if( !$packings ){
			$msg ='订单未分拣';
			return false;
		}

		$OrderModel = tbOrder::model()->findByPk( $packings->orderId,'state !=0 and state !=7' );
		if( empty( $OrderModel ) ){
			$msg = '你请求打印的订单不允许打印标签'; //
			return false;
		}		

		$tbPrintOrder = new tbPrintOrder();
		$tbPrintOrder->orderId = $OrderModel->orderId;//订单ID
		$tbPrintOrder->saleOrderId = $OrderModel->orderId;//销售订单ID
		$tbPrintOrder->create_by = ''; //制单人
		$tbPrintOrder->order_type = 4;//备货标签为4

		$customInfo = tbProfileDetail::model()->findByPk( $OrderModel->memberId );
		$tbPrintOrder->custom_name = $customInfo->companyname;
		$tbPrintOrder->custom_phone = $OrderModel->tel;

		$orderClass = new Order();
		$tbPrintOrder->delivery = $orderClass->deliveryMethod( $OrderModel->deliveryMethod );
		$tbPrintOrder->create_time = strtotime ( $OrderModel->createTime );

		$products = $OrderModel->products;
		$printData = $productIds = array();


		$printData[] = array(
								'product' => $packings->singleNumber,
								'price' => 0,
								'total' => $packings->packNum,
								'unit' => $packings->productId,
								'detail'=>$packings->remark,
								'position'=>''
							);
		$productIds[] = $packings->productId;

		$tbPrintOrder->quality = '';//质检002

		$packUserId = $packings->packUserId;
		$tbPrintOrder->cutting = tbUser::model()->getUserName( $packUserId );//剪料,分拣员
		$tbPrintOrder->hoseware = '';//仓管03

		$saveDetailModel = array();

		//明细
		$tbPrintOrderDetail = new tbPrintOrderDetail();
		$tbPrintOrderDetail->orderId = $tbPrintOrder->orderId;
		$tbPrintOrderDetail->price = 0;
		$tbPrintOrderDetail->subprice = 0;
		$tbPrintOrderDetail->batch = '';
		$tbPrintOrderDetail->mark = '';

		foreach ( $printData as $val ){
			$printDetail = clone $tbPrintOrderDetail;
			$printDetail->product = $val['product'];
			$printDetail->total = $val['total'];

			$printDetail->unit = $val['unit'];
			$printDetail->detail = $val['detail'];
			$printDetail->position = $val['position'];
			$saveDetailModel[] = $printDetail;
		}


		//分拣区域---取得分拣区域的默认打印机并送去打印，如果默认区域的没有，取得仓库的默认打印机
		$position = tbWarehousePosition::model()->findByPk( $packings->positionId );
		if( $position ){
			$printerId = $position->printerId;
		}else{
			$printerId = 0;
		}

		return self::saveData( $tbPrintOrder,$saveDetailModel,$printerId,$productIds, $msg );
	}

	private static function saveData( $tbPrintOrder,$saveDetailModel,$printerId,$productIds, &$msg ){

		//取得得打印机
		$printer = tbPrinter::model()->findByPk( $printerId,'state=0' );
		if( !$printer ){
			$msg = '请先配置打印机';
			return false;
		}

		$tbPrintTask = new tbPrintTask();
		$tbPrintTask->printer = $printer->printerSerial;
		$tbPrintTask->createTime = time();
		$tbPrintTask->printed = 0;

		//取得单位名称
		$units = tbProduct::model()->getUnitConversion( $productIds );


		//使用事务处理
		$transaction = Yii::app()->db->beginTransaction();

		if( !$tbPrintOrder->save() ){
			$transaction->rollback();
			return false;
		}

		foreach ( $saveDetailModel as $_model ){
			$_model->printId = $tbPrintOrder->printId;

			$_model->unit = array_key_exists( $_model->unit, $units )?$units[$_model->unit]['unit']:'';

			if( array_key_exists( $_model->unit, $units ) && empty ( $_model->detail ) ){
				if( $units[$_model->unit]['unitConversion']>0 && $_model->total >= $units[$_model->unit]['unitConversion'] ){
					$n1 = floor($_model->total/$units[$_model->unit]['unitConversion']);
					$n2 = Order::unitMod( $_model->total, $units[$_model->unit]['unitConversion'] );
					//单位换算显示：1*20+10米
					$_model->detail = $n1.'*'.$units[$_model->unit]['unitConversion'].'+'.$n2.$units[$_model->unit]['unit'];;
				}else{
					$_model->detail = $_model->total.$units[$_model->unit]['unit'];
				}
			}

			if( !$_model->save() ){
				$transaction->rollback();
				return false;
			}
		}

		$tbPrintTask->printId = $tbPrintOrder->printId;
		if( !$tbPrintTask->save() ){
			$transaction->rollback();
			return false;
		}

		$transaction->commit();
		$msg = '已添加进打印队列';
		return true;
	}
}
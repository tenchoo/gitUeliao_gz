<?php
/**
 * 仓库调拨单管理，确认调拨入库时才更新双方仓库对应产品的库存量
 * @author liang
 * @version 0.2
 * @package CFormModel
 */
class AllocationForm1 extends CFormModel {

	public $targetWarehouseId;

	public $driverId;

	public $vehicleId;

	public $products;

	public $warehouseId;



	public $singleNumber,$color,$remark,$productBatch,$num,$productId,$positionId;

	public $positionTitle = '';

	public $unitName;

	public $unit;

	public $orderId = 0;

	private $_type = 0;

	private $_allocation;

	public function rules()	{
		return array(
			array('warehouseId,targetWarehouseId,singleNumber,positionId,productBatch,num,productId','required','on'=>'add'),
			array('singleNumber,productBatch,num,productId,positionId','required','on'=>'receipt'),
			array('targetWarehouseId,driverId,vehicleId','required','on'=>'add,confirmation'),
			array('driverId,vehicleId,warehouseId,targetWarehouseId,productId,positionId', "numerical","integerOnly"=>true),
			array('num', "numerical"),
			array('products,singleNumber,color,remark,positionTitle,productBatch,unitName,unit','safe'),
		);
	}

	public function attributeLabels() {
		return array(
			'driverId' => '驾驶员',
			'vehicleId' => '车辆编号',
			'products'=>'调拨单产品明细',
			'warehouseId' => '原仓库',
			'targetWarehouseId'=>'目标仓库',
			'singleNumber' => '产品编号',
			'color'=>'颜色',
			'remark' => '备注',
			'positionId'=>'仓位',
			'productBatch'=>'产品批次',
			'num'=>'调拨数量',
			'storageNum'=>'入库数量',
		);
	}

	/**
	* 待确认调拨订单--确定收货,三个要点，
	* 1.产品入目标仓库，更改相对应的库存量，
	* 2.调拨单状态调整，
	* 3.若是客户订单，检查是否已全部分拣调拨完成，是的话更改订单状态为备货完成，并发送相关的通知消息。
	* @param CActiveRecord $model
	*/
	public function receipt( $dataArr,$model ){
		if( empty( $dataArr) && !is_array( $dataArr ) ){
			$this->addError('products',Yii::t('warehouse','No data'));
			return false ;
		}

		foreach ( $dataArr as $val ){
			$this->attributes = $val;
			if( !$this->validate() ) {
				return false ;
			}
		}

		//检查提交的数据 ，比较入库的数量与调拨的数量是否一致
		$allocationProducts = $products  = array();
		foreach ( $model->detail as $detail ){
			if(!isset($allocationProducts[$detail->productId][$detail->singleNumber][$detail->productBatch])){
				$allocationProducts[$detail->productId][$detail->singleNumber][$detail->productBatch] = $detail->num;
			}else{
				$allocationProducts[$detail->productId][$detail->singleNumber][$detail->productBatch] += $detail->num;
			}
		}

		foreach ( $dataArr as $val ){
			if(!isset($allocationProducts[$val['productId']][$val['singleNumber']][$val['productBatch']])){
				$this->addError('productBatch',
							Yii::t('warehouse',
									'Not included in the allocation of the number is: {product} products, the product batch is: {batch}.',
									array('{product}' => $val['singleNumber'],'{productBatch}' => $val['productBatch'])));
				return false ;
			}

			if(!isset($products[$val['productId']][$val['singleNumber']][$val['productBatch']])){
				$products[$val['productId']][$val['singleNumber']][$val['productBatch']] = $val['num'];
			}else{
				$products[$val['productId']][$val['singleNumber']][$val['productBatch']] += $val['num'];
			}
		}

		foreach ( $allocationProducts as $k=>$pro ){
			foreach ( $pro as $k2=>$val ){
				foreach ( $val as $k3=>$vval ){
					if(!isset($products[$k][$k2][$k3])){
						$this->addError('productBatch',Yii::t('warehouse', 'The products of {product}, the product batch is: {batch} are not in storage.', array('{product}' => $k2,'{productBatch}' => $k3)));
						return false ;
					}

					if( $vval!=$products[$k][$k2][$k3] ){
						$this->addError('productBatch',Yii::t('warehouse', 'The products of: {product} , the product batch is: {batch} are not equal to the number of storage and allocation', array('{product}' => $k2,'{productBatch}' => $k3)));
						return false ;
					}
				}
			}
		}

		$transaction = Yii::app()->db->beginTransaction();
		try {
			//step1:调拨单状态调整
			$model->state = '2';
			$model->comfirmUserId = Yii::app()->user->id;
			$model->comfirmUser =  Yii::app()->user->getState('username');
			$model->comfirmTime =  new CDbExpression('NOW()');
			if(!$model->save()){
				$this->addErrors( $model->getErrors() );
				return false;
			}

			//step1.1 :原仓库生成调拨出库单，对应产品出库
			if(!$this->output( $model )){
				return false;
			}

			//step1.2 :目标仓库生成调拨入库单，对应产品入库
			if(!$this->import( $model,$dataArr )){
				return false;
			}


			//step2:订单状态调整和订单产品仓库锁定，跟踪记录等。
			$this->updateOrder( $model,$products,$dataArr );

			if( $this->hasErrors() ){
				return false;
			}
			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			throw new CHttpException(503,$e);
			return false;
		}
	}

	/**
	* 确定调拨入库----出库
	* step1.1 :原仓库生成调拨出库单，对应产品出库
	* @param obj $model			调拨单对象
	*/
	private function output( $model ){
		//生成出库单
		$outbound = new tbWarehouseOutbound();
		$outbound->attributes = array(
								'warehouseId'=>$model->warehouseId,
								'source'=>tbWarehouseOutbound::TO_ALLOCATION,
								'sourceId'=>$model->allocationId );

		if( !$outbound->save() ){
			$this->addErrors( $outbound->getErrors() );
			return false;
		}

		//出库单明细
		$detail = new tbWarehouseOutboundDetail();
		$detail->outboundId = $outbound->outboundId;
		foreach ( $model->detail as $val ){
			$_detail = clone $detail;
			$_detail->attributes = $val->getAttributes(
									array('num','positionId','singleNumber','color'	,'productBatch'));
			if( !$_detail->save() ){
				$this->addErrors( $_detail->getErrors() );
				return false;
			}
		}

		//释放锁定
		$this->freeLock( $model );

		return true;
	}

	/**
	* 确定调拨入库----入库
	* step1.2 :目标仓库生成调拨入库单，对应产品出库入库
	* @param obj $model			调拨单对象
	* @param array $dataArr			入库数据
	*/
	private function import( $model,$dataArr ){
		$OrderImport = new OrderImport();
		$source = tbWarehouseWarrant::FROM_CALLBACK;

		$OrderImport->warrant = array('postId'=>$model->allocationId, //调拨单ID
									  'warehouseId'=> $model->targetWarehouseId ,
									  'source'=>$source );

		$details  = array();
		foreach ( $dataArr as $pval ){
			$record = new tbWarehouseWarrantDetail();
			$record->orderId = $model->orderId; //来源订单ID
			$record->num = $pval['num'];
			$record->singleNumber = $pval['singleNumber'];
			$record->color = $pval['color'];
			$record->batch = $pval['productBatch'];
			$record->positionId = $pval['positionId'];
			array_push( $details, $record );
		}
		$OrderImport->details = $details;
		if( !$OrderImport->save() ) {
			$error = $OrderImport->getErrors();
			$this->addErrors( $error );
			return false;
		}

		return true;
	}


	/**
	* 订单锁定----如果是订单调拔，调拔后为待发货状态锁定，发货后或订单取消后解锁
	* 要判断订单状态，如果订单已取消，那么不必锁定。
	* @param obj $model			调拨单对象
	* @param array $dataArr			入库数据
	*/
	private function lock( $model,$dataArr ){
		if( empty( $model->orderId ) || $model->type != tbAllocation::TYPE_ORDER  ) return true;

		//如果是订单调拔，调拔后为待发货状态锁定，发货后或订单取消后解锁
		$lock = new tbWarehouseLock();
		$lock->type = tbWarehouseLock::TYPE_ORDER;
		$lock->sourceId = $model->orderId;
		$lock->warehouseId = $model->targetWarehouseId;
		$lock->orderId = $model->orderId;
		foreach ( $dataArr as $pval ){
			$_lock = clone $lock;
			$_lock->num 		 = $pval['num'];
			$_lock->singleNumber = $pval['singleNumber'];
			$_lock->productBatch = $pval['productBatch'];
			$_lock->positionId   = $pval['positionId'];
			if( !$_lock->save() ){
				$this->addErrors( $_lock->getErrors() );
				return false;
			}
		}
		return true;
	}

	/**
	* 确定调拨入库----更新订单表中备货数量，若备货完成，更订单状态
	* @param obj $model				调拨单对象
	* @param array   $products		备货的数据
	*/
	private function updateOrder( $model,$products,$dataArr ){
		if( empty( $model->orderId ) ) return false;

		$order = tbOrder::model()->findByPk( $model->orderId );
		if( empty( $order ) || $order->state == '7' ) return false;

		$this->lock( $model,$dataArr );

		foreach ( $order->products as $pval ){
			if(!isset($products[$pval->productId][$pval->singleNumber])) continue;

			$pval->packingNum = $pval->packingNum + array_sum($products[$pval->productId][$pval->singleNumber]);
			if(!$pval->save()){
				$this->addErrors( $pval->getErrors() );
				return false;
			}
		}
		//step3:更改订单状态为备货完成，并发送相关的通知消息
		$this->checkOrderState( $model->orderId ,$order->memberId );

		//生成订单追踪信息
		tbOrderMessage::addMessage( $model->orderId,'has_packing' );
	}

	/**
	* 客户订单，查找备货是否全部完成。
	* @param integer $orderId 客户订单ID
	* @param integer $memberId 客户ID
	*/
	private function checkOrderState( $orderId,$memberId ){
		if( empty( $orderId ) || empty( $memberId ) ) return false;

		//查找是否有未分拣完成的
		$c = tbPacking::model()->count('orderId = :orderId and state = 0 ',array(':orderId'=>$orderId));
		if( $c ) return false;

		//查找是否有未调拨完成的
		$c1 = tbAllocation::model()->count('orderId = :orderId and state<2',array(':orderId'=>$orderId));
		if( $c1 ) return false;

		//全部分拣调拨完成，更改订单状态为备货完成。
		$order = tbOrder::model()->findByPk( $orderId );
		if( $order->state == '1' ){
			$order->state = ( $order->isSettled == '1' )?3:2;
			if( !$order->save() ){
				$this->addErrors( $order->getErrors() );
				return false;
			}
		}

		//并发送相关的通知消息
		$tbMessage = new tbMessage();
		$tbMessage->title = '您的订单号为：'.$orderId.' 的订单已经备货完成';
		$tbMessage->content = '您的订单号为：'.$orderId.' 的订单已经备货完成';
		$tbMessage->memberId = $memberId;
		if( !$tbMessage->save() ){
			$this->addErrors( $tbMessage->getErrors() );
			return false;
		}
	}


	/**
	* 调拨订单--确定调拨
	* @param integer $id  归单记录ID
	* @param array $vehicle
	* @param array $drivers
	*/
	public function allocationOrder( $mergeId,$vehicle ,$drivers ){
		if(!isset($drivers[$this->driverId])){
			$this->driverId = '';
		}

		if(!isset($vehicle[$this->vehicleId])){
			$this->vehicleId = '';
		}

		if( !$this->validate() ) {
			return false ;
		}

		$OrderMerge = tbOrderMerge::model()->findByPk( $mergeId ,'state =1 and actionType = 0' );
		if( !$OrderMerge ){
			$this->addError( 'orderId','此订单已被操作' );
			return false;
		}

		$products = array();
		$Pack = tbPack::model()->with('detail')->findAllByAttributes( array('orderId'=>$OrderMerge->orderId,'state'=>array('1','2') ) );
		foreach ( $Pack as $_pack ){
			foreach ( $_pack->detail as $_val ){
				$k = $_pack->orderProductId;
				$p = $_val->positionId;
				if( isset( $products[$k][$p] ) ){
					$products[$k][$p]['packingNum'] = bcadd( $products[$k][$p]['packingNum'],$_val->packingNum,1 );
					$products[$k][$p]['wholes'] = bcadd( $products[$k][$p]['wholes'],$_val->wholes,0 );
				}else{
					$products[$k][$p] = array(
								'productId'=>$_pack->productId,
								'singleNumber'=>$_pack->singleNumber,
								'color'=>$_pack->color,
								'remark'=>$_pack->remark,
								'positionId'=>$_val->positionId,
								'packingNum'=>$_val->packingNum,
								'positionTitle'=>$_val->positionTitle,
								'productBatch'=>$_val->productBatch,
								'wholes'=>$_val->wholes
							);
				}

			}
		}

		$model =  new tbAllocation();
		$model->warehouseId = $OrderMerge->warehouseId;
		$model->targetWarehouseId = $this->targetWarehouseId;
		$model->orderId =  $OrderMerge->orderId;
		$model->vehicleId = $this->vehicleId;
		$model->plateNumber = $vehicle[$this->vehicleId];
		$model->driverUserId = $this->driverId;
		$model->driverName = $drivers[$this->driverId];
		$model->userId = Yii::app()->user->id;
		$model->userName =  Yii::app()->user->getState('username');
		$model->state = '1';

		$transaction = Yii::app()->db->beginTransaction();
		try {
			$OrderMerge->actionType = tbOrderMerge::ACTION_ALLOTTED;
			if( !$OrderMerge->save() ){
				$transaction->rollback();
				$this->addErrors( $OrderMerge->getErrors() );
				return false;
			}

			if( !$model->save() ){
				$transaction->rollback();
				$this->addErrors( $model->getErrors() );
				return false;
			}

			//调拨明细
			$detail = new tbAllocationDetail();
			$detail->allocationId = $model->allocationId;
			foreach ( $products  as $_product ){
				foreach ( $_product as $val ){
					$_detail = clone $detail;
					$_detail->singleNumber = $val['singleNumber'];
					$_detail->color = $val['color'];
					$_detail->productId = $val['productId'];
					$_detail->positionTitle = $val['positionTitle'];
					$_detail->num = $val['packingNum'];
					$_detail->positionId = $val['positionId'];
					$_detail->remark = $val['remark'];
					$_detail->productBatch = $val['productBatch'];

					if( !$_detail->save() ){
						$transaction->rollback();
						$this->addErrors( $_detail->getErrors() );
						return false;
					}
				}
			}
			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			throw new CHttpException(503,$e);
			return false;
		}
	}

	private function freeLock( $model ){
		//释放锁定
		$lock = new tbWarehouseLock();
		if( $model->packingId > 0 ){
			$lock->deleteAllByAttributes( array('type'=>tbWarehouseLock::TYPE_PACKING,'sourceId'=>$model->packingId,'warehouseId'=>$model->warehouseId) );
		}

		$lock->deleteAllByAttributes( array('type'=>tbWarehouseLock::TYPE_ALLOCATION,'sourceId'=>$model->allocationId,'warehouseId'=>$model->warehouseId) );
	}


	/**
	* 调拨单列表显示 -- 后台
	*
    * @param  array $condition 查找条件
	* @param  string $order  排序
	*/
	public function search( $condition = array() ){
		$pageSize = tbConfig::model()->get( 'page_size' );
		$criteria = new CDbCriteria;

		if(!empty($condition)){
			foreach ($condition as $key=>$val){
				if( $val == '' ){
					continue ;
				}

				if( $key =='createTime' ){
					$criteria->addCondition("t.createTime>'$val'");
					$createTime2 = date("Y-m-d",strtotime( $val )+86400 ) ;
					$criteria->addCondition("t.createTime < '$createTime2' ");
				}else{
					$criteria->compare('t.'.$key,$val);
				}
			}
		}

		$criteria->order = 'createTime desc';
		$model = new CActiveDataProvider('tbAllocation', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));
		$data = $model->getData();
		$return['list'] = array();
		$warehouse = tbWarehouseInfo::model()->getAll();
		foreach ( $data as $val ){
			$return['list'][] = $this->setData( $val,$warehouse );
		}

		$return['closed'] =  array();
		if( isset( $condition['state'] ) && $condition['state'] == '2' ){
			//判断是否出回调按纽
			$orderIds = array();
			foreach ( $return['list'] as $val ){
				if( $val['type'] == tbAllocation::TYPE_ORDER && $val['isCallback'] == 0 && !empty($val['orderId'] ) ){
					$orderIds[] = $val['orderId'];
				}
			}

			$c = new CDbCriteria;
			$c->select='orderId';
			$c->compare('orderId',$orderIds);
			$c->compare('state',7);


			$closed = tbOrder::model()->findAll( $c );
			if ( $closed ){
				$return['closed'] = array_map( function($i){return $i->orderId;},$closed );
			}
		}

		$return['pages'] = $model->getPagination();
		return $return;
	}

	/**
	* 组装数据
	* @param CActiveRecord $model
	* @param array $warehouse
	* @param integer $type 组装的类型，1为按批次为单位组装，2为按仓位为单位组装
	*/
	public function setData( $model,$warehouse,$type='1' ){
		$info = $model->attributes;
		$info['warehouse'] = isset($warehouse[$model->warehouseId])?$warehouse[$model->warehouseId]:'';
		$info['targetWarehouse'] = isset($warehouse[$model->targetWarehouseId])?$warehouse[$model->targetWarehouseId]:'';
		$info['rowspan'] = count($model->detail);
		$info['detail'] = $productIds = array();

		if( empty($info['userName']) && !empty($model->userId) ){
			$info['userName'] = tbUser::model()->getUsername( $model->userId );
		}

		if( empty($info['comfirmUser']) && !empty($model->comfirmUserId) ){
			$info['comfirmUser'] = tbUser::model()->getUsername( $model->comfirmUserId );
		}

		$productIds = array_map(function ($i){return $i->productId;},$model->detail);
		$pInfo =  tbProduct::model()->getUnitConversion( $productIds );
		$detail = $num = array();
		foreach ($model->detail as $val ){
			if(  $type == '2' ){
				if( !isset($detail[$val->singleNumber]) ){
					$detail[$val->singleNumber] = $val->getAttributes( array('productId','singleNumber','color'));
					$detail[$val->singleNumber]['rowspan'] = 0;
					$detail[$val->singleNumber]['totalNum'] = 0;
					$detail[$val->singleNumber]['distributionNum'] = 0;
				}
				$detail[$val->singleNumber]['positions'][$val->positionId][] = $val->getAttributes( array('positionId','num','positionTitle','productBatch'));
				$detail[$val->singleNumber]['rowspan'] ++;
				$detail[$val->singleNumber]['totalNum'] += $val->num;
			}else{
				$num[$val->singleNumber][] = $val->num;
				if( !isset($detail[$val->singleNumber][$val->productBatch]) ){
					$detail[$val->singleNumber][$val->productBatch] = $val->getAttributes( array('productId','singleNumber','color','productBatch','num'));
					$detail[$val->singleNumber][$val->productBatch]['unit'] = (isset($pInfo[$val->productId]['unit']))?$pInfo[$val->productId]['unit']:'';
				}else{
					$detail[$val->singleNumber][$val->productBatch]['num']   += $val->num;
					$info['rowspan'] --;
				}
				$detail[$val->singleNumber][$val->productBatch]['total'] = sprintf("%.1f", array_sum($num[$val->singleNumber]));
			}
		}

		if(  $type == '2' && $model->orderId && $model->packingId ){
			//查找分拣单并分配的信息。。
			$pack = tbPacking::model()->with('orderTime','distribution')->findByPk( $model->packingId );
			foreach ( $pack->distribution as $val ){
				if( isset($detail[$val->singleNumber]) ){
					$detail[$val->singleNumber]['distributionNum'] += $val->distributionNum;
				}
			}
			$info['orderTime'] = $pack->orderTime->createTime;
		} else {
			$info['orderTime'] = '';
		}
		$info['detail'] = array_values($detail);
		return $info;
	}


	public function getCallbackInfo( $id ){
		if( !is_numeric( $id ) || $id < 1 ){
			return ;
		}

		$allocation = tbAllocation::model()->findByPk( $id,'isCallback = 0' );

		if( !$allocation ){
			return ;
		}

		$this->_allocation = $allocation;

		//入库信息
		$inputModel = tbWarehouseWarrant::model()->find(
										'source = :s and postId = :p',
										array(':s'=>tbWarehouseWarrant::FROM_CALLBACK,':p'=>$id)
						);
		$products = $singles = array();
		foreach ( $inputModel->detail as $val ){
			$products[] = array(
								'singleNumber'=>$val->singleNumber,
								'color'=>$val->color,
								'positionId'=>$val->positionId,
								'positionTitle'=>$val->positionName,
								'productBatch'=>$val->batch,
								'num'=>$val->num,
								);
			$singles[] = $val->singleNumber;
		}

		$singles = implode( "','",$singles );

		$sql = "select s.singleNumber,s.productId,u.unitName from {{product_stock}} s
						left join {{product}} p on p.productId = s.productId
						left join {{unit}} u on p.unitId = u.unitId
						where s.singleNumber in( '$singles' )";
		$command = Yii::app()->db->createCommand($sql);
		$result = $command->queryAll();

		$singles = array();
		foreach( $result as $val ){
			$singles[$val['singleNumber']] = $val;
		}

		foreach ( $products as &$val ){
			if( !isset( $singles[$val['singleNumber']] ) ){
				return ;
			}
			$val['productId'] = $singles[$val['singleNumber']]['productId'];
			$val['unitName'] = $singles[$val['singleNumber']]['unitName'];
		}

		$this->warehouseId = $allocation->targetWarehouseId;
		$this->targetWarehouseId = $allocation->warehouseId;
		$this->products  = $products;
		$this->orderId = $allocation->orderId;
		$this->_type = tbAllocation::TYPE_CALLBACK;

		return true;
	}
}

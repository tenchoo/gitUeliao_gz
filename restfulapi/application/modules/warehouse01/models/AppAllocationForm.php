<?php
/**
 * 仓库调拨单管理--APP扫描确认调拨
 * @author liang
 * @version 0.1
 * @package CFormModel
 */
class AppAllocationForm extends CFormModel {

	public $_model;

	private $_userId;

	private $_username;

	function __construct( $userId,$username ) {
		parent::__construct();

		$this->_userId = $userId;
		$this->_username = $username;
	}

	/**
	* 手机端扫描确认调拨--确定收货,三个要点，
	* 1.产品入目标仓库，更改相对应的库存量，
	* 2.调拨单状态调整，
	* 3.若是客户订单，检查是否已全部分拣调拨完成，是的话更改订单状态为备货完成，并发送相关的通知消息。
	* @param CActiveRecord $model
	*/
	public function appcomfirm( $positions ){
		if( empty( $positions) && !is_array( $positions ) || empty( $this->_model ) ){
			$this->addError('products',Yii::t('msg','Missing parameter'));
			return false ;
		}

		$tbWarehousePosition = new tbWarehousePosition;

		$details = $this->_model->detail;
		$products =  array_unique ( array_map( function ( $i ){ return $i->singleNumber;},$details ) );
		foreach ( $products as $_pro ){
			if( !array_key_exists( $_pro, $positions ) || empty( $positions[$_pro] )  ){
				//此产品没有选择仓位，返回
				$this->addError('products',Yii::t('warehouse','The products of: {product} must choose the position',array('{product}'=>$_pro )));
				return false ;
			}

			//判断此仓位是否属于目标入库仓库，不属于则返回提示所选择的仓位不存在
			$flag = tbWarehousePosition::model()->exists(
								'positionId = :position and state =0 and parentId >0 and warehouseId =:wid',
								array( ':position'=>$positions[$_pro],':wid'=>$this->_model->targetWarehouseId ) );
			if( !$flag ){
				//此产品选择仓位不属于目标入库仓库
				$this->addError('products',Yii::t('warehouse','The position of the product {product} selection does not belong to the current warehouse',array('{product}'=>$_pro )));
				return false ;
			}
		}

		$transaction = Yii::app()->db->beginTransaction();
		try {
			//step1:调拨单状态调整
			$this->_model->state = '2';
			$this->_model->comfirmUserId = $this->_userId;
			$this->_model->comfirmUser =  $this->_username;
			$this->_model->comfirmTime =  new CDbExpression('NOW()');

			if(!$this->_model->save()){
				$transaction->rollback();
				$this->addErrors( $this->_model->getErrors() );
				return false;
			}

			//step1.1 :原仓库生成调拨出库单，对应产品出库
			if(!$this->output( $this->_model )){
				$transaction->rollback();
				return false;
			}

			//step1.2 :目标仓库生成调拨入库单，对应产品入库
			if(!$this->import( $this->_model,$positions )){
				$transaction->rollback();
				return false;
			}


			//step2:订单状态调整和订单产品仓库锁定，跟踪记录等。
			$this->updateOrder( $this->_model,$positions );

			if( $this->hasErrors() ){
				$transaction->rollback();
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

	public function getModel( $orderId  ){
		if( !is_numeric( $orderId ) || $orderId<1 ) return ;
		//去调拨单中查找待确认调拨的
		$model = tbAllocation::model()->with('detail')->find(  'orderId = :orderId and state =1',
										array( ':orderId'=>$orderId ) );
		if( $model ){
			$this->_model = $model;
			return true;
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
		$outbound->userId = $this->_userId;
		$outbound->operator =  $this->_username;
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

	private function freeLock( $model ){
		//释放锁定
		$lock = new tbWarehouseLock();
		if( $model->packingId > 0 ){
			$lock->deleteAllByAttributes( array('type'=>tbWarehouseLock::TYPE_PACKING,'sourceId'=>$model->packingId,'warehouseId'=>$model->warehouseId) );
		}

		$lock->deleteAllByAttributes( array('type'=>tbWarehouseLock::TYPE_ALLOCATION,'sourceId'=>$model->allocationId,'warehouseId'=>$model->warehouseId) );
	}


	/**
	* 确定调拨入库----入库
	* step1.2 :目标仓库生成调拨入库单，对应产品出库入库
	* @param obj $model			调拨单对象
	* @param array $dataArr			入库数据
	*/
	private function import( $model,$positions ){
		$warrant = new tbWarehouseWarrant;
		$source = tbWarehouseWarrant::FROM_CALLBACK;
		$warrant->attributes = array('postId'=>$model->allocationId, //调拨单ID
									  'warehouseId'=> $model->targetWarehouseId ,
									  'source'=>$source );
		$warrant->userId = $this->_userId;
		$warrant->operator =  $this->_username;
		if( !$warrant->save() ){
			$this->addErrors( $warrant->getErrors() );
			return false;
		}

		$detail =  new tbWarehouseWarrantDetail();
		$detail->warrantId = $warrant->warrantId;
		foreach ( $model->detail as $val ){
			$_detail = clone $detail;
			$_detail->orderId = $model->orderId; //来源订单ID
			$_detail->num =  $val->num;
			$_detail->singleNumber = $val->singleNumber;
			$_detail->color = $val->color;
			$_detail->batch = $val->productBatch;
			$_detail->positionId = $positions[$val->singleNumber];

			if( !$_detail->save() ){
				$this->addErrors( $_detail->getErrors() );
				return false;
			}
		}

		return true;
	}


	/**
	* 确定调拨入库----更新订单表中备货数量，若备货完成，更订单状态
	* @param obj $model				调拨单对象
	*/
	private function updateOrder( $model,$positions ){
		if( empty( $model->orderId ) ) return false;

		$order = tbOrder::model()->findByPk( $model->orderId );
		if( empty( $order ) || $order->state == '7' ) return false;

		$this->lock( $model,$positions );

		if( $this->addToMergeList( $model ) ){
			return false;
		}



		//step3:更改订单状态为备货完成，并发送相关的通知消息
		//全部分拣调拨完成，更改订单状态为备货完成。
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
		$tbMessage->memberId = $order->memberId;
		if( !$tbMessage->save() ){
			$this->addErrors( $tbMessage->getErrors() );
			return false;
		}

		//生成订单追踪信息
		tbOrderMessage::addMessage( $model->orderId,'has_packing' );
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
		foreach ( $model->detail as $val ){
			$_lock = clone $lock;
			$_lock->num 		 = $val->num;
			$_lock->singleNumber = $val->singleNumber;
			$_lock->productBatch = $val->productBatch;
			$_lock->positionId   = $positions[$val->singleNumber];
			if( !$_lock->save() ){
				$this->addErrors( $_lock->getErrors() );
				return false;
			}
		}
		return true;
	}

	/**
	* 加入目标仓库已归单完成队列
	*/
	public function addToMergeList( $model ){
		$OrderMerge = new tbOrderMerge();
		$OrderMerge->state = tbOrderMerge::STATE_ALLOTTED;
		$OrderMerge->actionType = 0;
		$OrderMerge->orderId = $model->orderId;
		$OrderMerge->warehouseId = $model->targetWarehouseId;

		if( !$OrderMerge->save() ){
			$this->addErrors( $OrderMerge->getErrors() );
			return false;
		}
	}
}

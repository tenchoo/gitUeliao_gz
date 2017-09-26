<?php
/**
* 订单发货---仓库管理
* @version 0.2
* @package CFormModel
* @time 2016/11/11
*/

class DeliveryOrder extends CFormModel {

	private $_obj;

	public $orderId;

	/**
	* 确认发货
	* 	待处理事项：
	*　1.发货后订单状态调整，
	*　2.进入发货界面数据检测
	*　3.进入调拨界面数据检测
	*　4.发货后出库处理
	*/
	public function doDelivery( $model ){
		//使用事务处理
		$transaction = Yii::app()->db->beginTransaction();
		try {
			$model->actionType = tbOrderMerge::ACTION_DELIVERY;
			if( !$model->save() ){
				$transaction->rollback();
				$this->addErrors( $model->getErrors() );
				return false;
			}

			$OrderModel = tbOrder::model()->findByPk( $model->orderId,'state in(2,3) and isRecognition = 1' );
			if( !$OrderModel ){
				$this->addError( 'order',Yii::t('warehouse','The current status of the order is not allowed to ship operation') );
				return false;
			}

			//更改订单状态
			$OrderModel->state = 4;
			if( !$OrderModel->save() ){
				$transaction->rollback();
				$this->addErrors( $OrderModel->getErrors() );
				return false;
			}

			//生成发货记录
			$tbDelivery = new tbDelivery();
			$tbDelivery->orderId = $model->orderId;
			$tbDelivery->logistics = '';
			$tbDelivery->logisticsNo = '';
			$tbDelivery->address = $OrderModel->address;
			if( !$tbDelivery->save() ){
				$this->addErrors( $tbDelivery->getErrors() );
				return false;
			}

			//生成发货出库单，对应产品出库
			if(!$this->output( $model )){
				return false;
			}


			//生成订单追踪信息
			$message = '操作人：'. Yii::app()->user->getState('username').'(userId:'.Yii::app()->user->id.')';
			tbOrderMessage::addMessage2( $OrderModel->orderId,'仓库已发货',$message );

			//释放仓库锁定
			$lock = new tbWarehouseLock();
			$lock->deleteAllByAttributes( array( 'orderId'=>$OrderModel->orderId ) );
			$transaction->commit();

			//短信通知
			OrderSms::deliveryNotify( $OrderModel );
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			throw new CHttpException(503,$e);
			return false;
		}
	}

	/**
	* 生成发货出库单
	* @param obj $model
	*/
	private function output( $model ){
		//生成出库单
		$outbound = new tbWarehouseOutbound();
		$outbound->attributes = array(
								'warehouseId'=>$model->warehouseId,
								'source'=>tbWarehouseOutbound::TO_DELIVERY,
								'sourceId'=>$model->orderId,
								);
		if( !$outbound->save() ){
			$this->addErrors( $outbound->getErrors() );
			return false;
		}

		$data = array();
		//当前仓库分拣的出库
		if( $model->state === '1' ){
			$details = array();
			$Pack = tbPack::model()->with('detail')->findAllByAttributes( array('orderId'=>$model->orderId,'state'=>array('1','2') ) );
			foreach ( $Pack as $_pack ){
				foreach ( $_pack->detail as $_val ){
					$k = $_pack->orderProductId;
					$p = $_val->positionId;
					if( isset( $details[$k][$p] ) ){
						$details[$k][$p]['num'] = bcadd( $details[$k][$p]['num'],$_val->packingNum,1 );
					}else{
						$details[$k][$p] = array(
									'singleNumber'=>$_pack->singleNumber,
									'color'=>$_pack->color,
									'positionId'=>$_val->positionId,
									'num'=>$_val->packingNum,
									'productBatch'=>$_val->productBatch,

								);
					}
				}
			}
			foreach ( $details as $val ){
				$data = array_merge( $data ,$val );
			}
		}else{
			//备货信息全部出库，查找此订单的全部调拨入库信息，并把对应产品出库。
			$c = new CDbCriteria;
			$c->compare('t.orderId',$model->orderId);
			$c->compare('w.source',tbWarehouseWarrant::FROM_CALLBACK);
			$c->join = 'left join {{warehouse_warrant}} w on t.warrantId = w.warrantId ';

			$model = tbWarehouseWarrantDetail::model()->findAll( $c );

			foreach ( $model as $val ){
				$k = $val->positionId.'_'.$val->singleNumber.'_'.$val->batch;
				if(!isset($data[$k])){
					$data[$k] =  $val->getAttributes(array('num','positionId','singleNumber','color'));
					$data[$k]['productBatch'] = $val->batch;
				}else{
					$data[$k]['num'] = bcadd( $data[$k]['num'],$val->num,1);
				}
			}
		}

		//出库单明细
		$detail = new tbWarehouseOutboundDetail();
		$detail->outboundId = $outbound->outboundId;
		foreach ( $data as $val ){
			$_detail = clone $detail;
			$_detail->attributes = $val;
			if( !$_detail->save() ){
				$this->addErrors( $_detail->getErrors() );
				return false;
			}
		}
		return true;
	}
}


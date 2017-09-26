<?php
/**
 * 扫描订单管理
 * @author liang
 * @version 0.1
 * @package Controller
 */
class OrderController extends MController {

	/**
	* 订单--获取订单的全部调拨信息
	* @param integer $id 订单ID
	*/
	public function actionShow( $id ){
		//去调拨单中查找待确认调拨的
		$model = tbAllocation::model()->with('detail')->find(  'orderId = :id and state =1',
										array( ':id'=>$id ) );


		if( !$model ){
			$this->notFound();
		}

		$OrderModel = tbOrder::model()->findByPk( $model->orderId );
		$OrderModel = tbOrder::model()->find();
		$OrderClass = new Order();

		$member = $OrderClass->getMemberDetial( $OrderModel->memberId );
		$warehouse = tbWarehouseInfo::model()->findByPk( $OrderModel->warehouseId );

		$details = $model->detail;

		//取得单位和辅助单位
		$productIds = array_map( function ( $i ){ return $i->productId;},$details );
		$units = tbProduct::model()->getUnitConversion( $productIds );

		$list = array();
		$wholes = array();
		foreach ( $details as $val ){
			if( !array_key_exists( $val->singleNumber, $list ) ){
				$list[$val->singleNumber] = array(
					'product'=>$val->singleNumber,
					'total'=>$val->num,
					'unit'=>$units[$val->productId]['unit'],
					'color'=>$val->color,
					'auxUnit'=>$units[$val->productId]['auxiliaryUnit'],
					'auxiliaryUnit'=>$units[$val->productId]['unitConversion'].$units[$val->productId]['unit'].'/'.$units[$val->productId]['auxiliaryUnit'],
					'detail'=>array(),
				);
			}else{
				$list[$val->singleNumber]['total'] = bcadd( $list[$val->singleNumber]['total'],$val->num,1 );
			}

			if( $val->wholes >0 ){
				//记录整卷数量
				$wholes[$val->singleNumber][] = $val->wholes;
			}else{
				$list[$val->singleNumber]['detail'][] = $val->num.$units[$val->productId]['unit'];
			}
		}

		foreach( $list as $key=>&$val ){
			if( array_key_exists( $key,$wholes ) && !empty ( $wholes[$key] ) ){
				$detail = array_sum( $wholes[$key] ) .$val['auxUnit'];
				array_unshift( $val['detail'],$detail);
			}

			$val['detail'] = implode( '+',$val['detail'] );
			unset( $val['auxUnit'] );
		}

		$data = array(
				'orderId'=>$model->orderId,
				'custom_name'=>$member['companyname'],
				'orderType'=>$OrderModel->orderType,
				'createTime'=>$OrderModel->createTime,
				'delivery'=>$OrderClass->deliveryMethod( $OrderModel->deliveryMethod ),
				'Dwarehouse'=>$warehouse->title,
				'meno'=>$OrderModel->memo,
				'list'=>array_values( $list ),
			);

		$this->data = $data;
		$this->state = true;
		$this->showJson();
	}


	/**
	* 确认调拔信息
			确认调拔的仓位信息：
		如：
		position[K559-240]= 1223
		position[K559-241]= 1222

		产品单品编号，如：K559-240,
		值为下拉选择仓位ID，分区ID不需要存储
	*
	*/
	public function actionCreate(){
		$orderId = Yii::app()->request->getPost('orderId'); //订单编码
		$position = Yii::app()->request->getPost('position'); //订单单品编码

		$model = new AppAllocationForm( $this->userId,$this->username );
		$flag = $model->getModel( $orderId );

		if( !$flag ){
			$this->notFound();
		}

		if( $model->appcomfirm( $position ) ){
			$this->state = true;
		}else{
			$this->message = $model->getErrors();
		}

		$this->showJson();
	}
}
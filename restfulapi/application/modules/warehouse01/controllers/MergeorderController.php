<?php
/**
 * 扫描订单管理--归单
 * @author liang
 * @version 0.1
 * @package Controller
 */
class MergeorderController extends MController {

	public function actionIndex(){
		$orderId = Yii::app()->request->getQuery('orderId'); //订单编码
		$product = Yii::app()->request->getQuery('product'); //订单单品编码

		$model = tbPack::model()->with('detail')->findByAttributes( array('orderId'=>$orderId,'singleNumber'=>$product,'state'=>'1') );
		if( !$model ){
			$this->notFound();
		}

		$OrderModel = tbOrder::model()->findByPk( $model->orderId );
		$OrderClass = new Order();

		$member = $OrderClass->getMemberDetial( $OrderModel->memberId );
		$warehouse = tbWarehouseInfo::model()->findByPk( $OrderModel->warehouseId );

		//取得单位和辅助单位
		$units = tbProduct::model()->getUnitConversion( array( $model->productId ) );
		$units = current( $units );

		$details = $model->detail;
		$wholeNum = 0;
		$wholePosition = '';
		$pieces = array();
		foreach ( $details as $val ){
			//整码
			if( $val->wholes > '0' ){
				$wholeNum = $val->wholes;
				$wholePosition = $val->positionTitle;
			}else{
				//零码
				$pieces[] = array( 'num'=>$val->packingNum,''=>$val->positionTitle );

			}
		}

		//分拣总数量
		$total = array_sum(	array_map( function ( $d ){ return $d->packingNum;},$model->detail));

		$data = array(
				'orderId'=>$model->orderId,
				'custom_name'=>$member['companyname'],
				'orderType'=>$OrderModel->orderType,
				'createTime'=>$OrderModel->createTime,
				'delivery'=>$OrderClass->deliveryMethod( $OrderModel->deliveryMethod ),
				'Dwarehouse'=>$warehouse->title,
				'meno'=>$OrderModel->memo,
				'product'=>$model->singleNumber,
				'color'=>$model->color,
				'total'=>$total,
				'unit'=>$units['unit'],
				'auxiliaryUnit'=>$units['auxiliaryUnit'],
				'auxiliary'=>$units['unitConversion'],
				'wholeNum'=>$wholeNum,
				'wholePosition'=>$wholePosition,
				'pieces'=>$pieces,
			);

		$this->data = $data;
		$this->state = true;
		$this->showJson();
	}

	/**
	* 确认归单
	*
	*/
	public function actionCreate(){
		$orderId = Yii::app()->request->getPost('orderId'); //订单编码
		$product = Yii::app()->request->getPost('product'); //订单单品编码

		$model = tbPack::model()->findByAttributes( array('orderId'=>$orderId,'singleNumber'=>$product,'state'=>'1') );
		if( !$model ){
			$this->notFound();
		}

		//归单处理
		$MergeOrder = new MergeOrder( $this->userId );
		if( $MergeOrder->doMerge( $model ) ){
			$this->state = true;
		}else{
			$this->message = $MergeOrder->getErrors();
		}
		$this->showJson();
		exit;
	}

}
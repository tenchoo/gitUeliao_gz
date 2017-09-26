<?php
/**
 * 分配分拣
 * @author liang
 * @version 0.1
 * @package CFormModel
 */
class DistributionForm extends CFormModel {

	public $warehouseId;

	public $deliveryWarehouseId;

	public $productBatch;

	public $unitRate;

	public $distributionNum;

	public $inventory;

	public $positionId,$positionTitle ;

	public $packinger;

	public function rules()	{
		return array(
			array('deliveryWarehouseId,warehouseId,positionId,distributionNum,productBatch','required'),
			array('deliveryWarehouseId,warehouseId,positionId', "numerical","integerOnly"=>true,'min'=>'1'),
			array('distributionNum,unitRate,inventory', "numerical"),
			array('productBatch,positionTitle','safe'),
		);
	}

	public function attributeLabels() {
		return array(
			'warehouseId' => '仓库',
			'deliveryWarehouseId'=>'发货仓',
			'productBatch'=>'产品批次',
			'distributionNum'=>'分配数量',
			'unitRate'=>'单位换算值',
			'positionId' => '仓位ID',
			'positionTitle' => '仓位',

		);
	}

	/**
	* 分配分拣
	* @param array $dataArr 分拣的数据
	* @param obj $model
	*/
	public function save( $dataArr,$model,$unitRate ){
		if(empty($dataArr)){
			$this->addError('distributionNum',Yii::t('base','No distribution data'));
			return false ;
		}

		$warehouse = tbWarehouseInfo::model()->getAll();
		if( !array_key_exists( $this->deliveryWarehouseId , $warehouse ) ){
			$this->deliveryWarehouseId = '';
		}

		$products = array();
		foreach ( $model->order->products as $val){
			$products[$val->orderProductId] = $val->attributes;
		}


		//按仓库分，一个仓库一张分拣单
		$packData = array();
		foreach( $dataArr as $key=>$val ){
			if(!isset($products[$key])){
				unset($dataArr[$key]);
				continue;
			}
			foreach ( $val as $ddval){
				if( !array_key_exists( $ddval['warehouseId'] , $warehouse ) ){
					$ddval['warehouseId'] = '';
				}
				$this->attributes = $ddval;
				$this->unitRate = $unitRate[$key];
				if( !$this->validate() ) {
					return false ;
				}

				$dnum[$key][] =  $ddval['distributionNum'];

				$packData[$ddval['warehouseId']] = array('warehouseId'=>$ddval['warehouseId']);
				$detailData[] = array(	'orderProductId'=>$key,
										'productBatch'=>$ddval['productBatch'],
										'distributionNum'=>$ddval['distributionNum'],
										'productId'=>$products[$key]['productId'],
										'singleNumber'=>$products[$key]['singleNumber'],
										'color'=>$products[$key]['color'],
										'unitRate'=>$unitRate[$key],
										'warehouseId'=>$ddval['warehouseId'],
										'positionId'=>$ddval['positionId'],
										'positionTitle'=>$ddval['positionTitle'],
										);
			}
		}
		if(empty($packData)){
			$this->addError('distributionNum',Yii::t('base','No distribution data'));
			return false ;
		}

		$packinger = is_array( $this->packinger ) ? $this->packinger : array();
		foreach ( $packData as $key=>$val ){
			if( !is_numeric( $val ) && $val< 1 ){
				$this->addError('distributionNum',Yii::t('warehouse', 'There is no such sort of a member at {worehose}', array('{worehose}' => $warehouse[$key])));
				return false ;
			}

			//要求配置分拣员
			if( !array_key_exists( $key,$packinger )  ){
				$this->addError('distributionNum',Yii::t('warehouse', 'Please select the sorting clerk for {worehose}', array('{worehose}' => $warehouse[$key])));
				return false ;
			}

			//查找当前传的分拣员是否正确
			$exists = tbUserPackinger::model()->exists( 'state = 0 and userId = :userId and warehouseId =:w' , array(':userId'=>$packinger[$key],':w'=>$key) );
			if( !$exists ){
				$this->addError('distributionNum',Yii::t('warehouse', 'There is no such sort of a member at {worehose}', array('{worehose}' => $warehouse[$key])));
				return false ;
			}
		}

		//判断分配数量必须等于购买数量
		foreach ( $products as $k=> $val ){
			if( !array_key_exists($k,$dnum) || $val['num'] != array_sum($dnum[$k])){
				$this->addError('distributionNum',Yii::t('warehouse', '{product},The total amount of the distribution must be equal to the number of purchases', array('{product}' => $val['singleNumber'])));
				return false ;
			}
		}

		foreach ( $detailData as $val ){
			//实时查找可用数量
			$condition = array( 'warehouseId'=>$val['warehouseId'],'positionId'=>$val['positionId'],'singleNumber'=>$val['singleNumber'],'productBatch'=>$val['productBatch']);
			$ValidNum =  tbWarehouseProduct::model()->findValidNum( $condition );

			//对比库存数量
			if( $ValidNum <  $val['distributionNum']) {
				$this->addError('productBatch',Yii::t('warehouse', 'The products of: {product} ,the position number is {position}, the product batch is: {batch} ,The number can not be greater than the number of inventory，Currently available num is {num}', array('{product}' => $val['singleNumber'],'{position}' => $val['positionTitle'],'{batch}' => $val['productBatch'],'{num}' => $ValidNum)));
				return false ;
			}
		}

		$transaction = Yii::app()->db->beginTransaction();
		try {
			//step1 生成分配记录
			$distribution =  new tbDistribution();
			$distribution->orderId = $model->orderId;
			$distribution->deliveryWarehouseId = $this->deliveryWarehouseId;
			if(!$distribution->save()){
				$this->addErrors( $distribution->getErrors() );
				return false;
			}

			//step2 记录分配明细记录
			$detail = new tbDistributionDetail();
			$detail->orderId = $distribution->orderId;
			$detail->distributionId = $distribution->distributionId;
			foreach ( $detailData as $val ){
				$_detail = clone $detail ;
				$_detail->attributes = $val;
				$_detail->packingerId = $packinger[$_detail->warehouseId];
				if( !$_detail->save() ) {
					$this->addErrors( $_detail->getErrors() );
					return false ;
				}
			}

			//step3 同步生成分拣单
			$pack = new tbPacking();
			$pack->distributionId = $distribution->distributionId;
			$pack->orderId = $model->orderId;
			$pack->deliveryWarehouseId = $this->deliveryWarehouseId;
			$pack->state = 0;
			foreach ( $packData as $val){
				$_pack = clone $pack;
				$_pack->warehouseId = $val['warehouseId'];
				$_pack->packingerId = $packinger[$_pack->warehouseId];
				if(!$_pack->save()){
					$this->addErrors( $_pack->getErrors() );
					return false;
				}
			}

			//step4 改变待分配表对应记录的状态
			$model->state  = 1;
			$model->opTime = new CDbExpression('NOW()');
			$model->userId = Yii::app()->user->id;

			if( !$model->save() ) {
				$this->addErrors( $model->getErrors() );
				return false ;
			}

			//保存发货仓库
			$model->order->warehouseId = $this->deliveryWarehouseId;
			if( !$model->order->save() ) {
				$this->addErrors( $model->order->getErrors() );
				return false ;
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
	* 取得列表
	* @param array $condition 查询列表条件
	*/
	public  function search( $condition = array() ){
		$criteria = new CDbCriteria;
		$criteria->order = ' t.createTime desc ';
		if(!empty($condition)){
			foreach ($condition as $key=>$val){
				$val = trim($val);
				if( is_null( $val ) || $val === '' ){
					continue ;
				}
				switch( $key ){
					case 'singleNumber':
						$criteria->addCondition( "exists ( select null from {{order_product}} op where op.orderId = t.orderId and op.singleNumber like '$val%' and op.state = 0  ) " );
						break;
					case 'is_string':
						$criteria->addCondition($val);
						break;
					default:
						$criteria->compare('t.'.$key,$val);
						break;
				}
			}
		}
		$pageSize = tbConfig::model()->get( 'page_size' );
		$criteria->order = ' t.createTime desc ';
		$model = new CActiveDataProvider('tbOrderDistribution', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));
		$return['list'] = $model->getData();
		$ids = array_map(function ($i){ return $i->orderId;}, $return['list']);
		if( !empty($ids) ){
			$c = new CDbCriteria;
			$c->select = 't.orderId';
			$c->compare('t.orderId',array_unique($ids));
			$orders = tbOrder::model()->findAll( $c );
			foreach ( $orders as $key=>$val ){
				//$info =  $val->getAttributes( array('orderId') );
				$info['products'] = array();
				foreach ( $val->products as $pval ){
					$info['products'][] = $pval->getAttributes( array('orderProductId','salesPrice','num','color','singleNumber') );
				}
				$orders[$val->orderId] = $info;
				unset( $orders[$key] );
			}
		}


		foreach ( $return['list'] as &$val ){
			$val = array_merge( $val->getAttributes( array('id','orderId','state')),$orders[$val->orderId] );
		}

		$return['pages'] = $model->getPagination();
		return $return;
	}


	/**
	* 分配分拣--整单退货处理
	* @param obj $model
	*/
	public function closeDistribution( $model ){

		$transaction = Yii::app()->db->beginTransaction();

		$model->state = tbOrderDistribution::STATE_REFUND;
		if( !$model->save() ) {
			$transaction->rollback();
			$this->addErrors( $model->getErrors() );
			return false ;
		}

		$order = $model->order;
		$closeReason = '订单退货';
		if( !$order->closeOrder( '0',$closeReason,$order->memberId ) ){
			$transaction->rollback();
			$this->addErrors( $order->getErrors() );
			return false ;
		}

		$refunds = new OrderRefund( Yii::app()->user->id,'' );
		if( !$refunds->importNo( $order->orderId  ) ){
			$transaction->rollback();
			$this->addErrors( $refunds->getErrors() );
			return false ;
		}
		$transaction->commit();
		return true;
	}


}
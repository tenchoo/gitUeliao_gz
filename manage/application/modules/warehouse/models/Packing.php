<?php
/**
 * 订单分拣
 * @author liang
 * @version 0.1
 * @package CFormModel
 *
 */

class Packing extends CFormModel {

	public $positionId = 0;

	public $positionTitle;

	public $productBatch;

	public $packingNum;

	public $unitRate;

	/**
	* Declares the validation rules.
	* The rules state that account and password are required,
	* and password needs to be authenticated.
	*/
	public function rules()	{
		return array(
			array('positionId,packingNum,productBatch','required'),
			array('positionId', "numerical","integerOnly"=>true,'min'=>'1'),
			array('packingNum,unitRate', "numerical"),
			array('productBatch,positionTitle','safe'),
		);
	}

	public function attributeLabels() {
		return array(
			'packingNum' => '分拣数量',
			'positionId' => '仓位',
			'positionTitle'=>'仓位',
			'productBatch'=>'产品批次',
			'distributionNum'=>'分配数量',
			'unitRate'=>'单位换算值',
		);
	}

	/**
	* 新增分拣单
	* @param array $dataArr 分拣的数据
	* @param obj $model
	*/
	public function save( $dataArr,$model ,$unitRate ){
		if(empty($dataArr)){
			$this->addError('packingNum',Yii::t('base','No packing data'));
			return false ;
		}

		$productArr = array();
		foreach ( $model->distribution as $dval){
			if( !array_key_exists($dval->orderProductId,$productArr) ){
				$productArr[$dval->orderProductId] = $dval->getAttributes( array('orderProductId','productId','unitRate','singleNumber','color' ));
				$productArr[$dval->orderProductId]['unitRate'] = $unitRate[$dval->orderProductId];
				$productArr[$dval->orderProductId]['total'] = 0;
			}

			$productArr[$dval->orderProductId]['total']  = bcadd( $productArr[$dval->orderProductId]['total'], $dval->distributionNum ,1 ) ;
		}

		$saveData = $num = array();
		foreach( $dataArr as $key=>$val ){
			if(!isset( $productArr[$key]['singleNumber'] ) ){
				unset($dataArr[$key]);
			}
			if(empty( $val )){
				$this->addError('packingNum',$productArr[$key]['singleNumber'].' '.Yii::t('base','No packing data'));
				return false ;
			}

			foreach (  $val as $pval ){
				$num[$key][] = $pval['packingNum'];
				$this->attributes = $pval;
				$this->unitRate = $unitRate[$key];
				if( !$this->validate() ) {
					return false ;
				}

				$saveData[] = array_merge($pval,$productArr[$key]);
			}
		}

		foreach ($productArr as $key=>$val){
			if( array_key_exists($key,$num) && array_sum($num[$key]) > $val['total'] ){
				$this->addError('packingNum',Yii::t('warehouse','{product},The number of packing can not be greater than the number of distribution',array('{product}'=>$val['singleNumber'])));
				return false;
			}
		}

		if(empty( $saveData )){
			$this->addError('packingNum',Yii::t('base','No packing data'));
			return false ;
		}

		foreach ( $saveData as $val ){
			//实时查找可用数量
			$condition = array( 'positionId'=>$val['positionId'],'singleNumber'=>$val['singleNumber'],'productBatch'=>$val['productBatch']);
			$ValidNum =  tbWarehouseProduct::model()->findValidNum( $condition ,$model->orderId );

			//对比库存数量
			if( $ValidNum <  $val['packingNum']) {
				$this->addError('productBatch',Yii::t('warehouse', 'The products of: {product} ,the position number is {position}, the product batch is: {batch} ,The number can not be greater than the number of inventory，Currently available num is {num}', array('{product}' => $val['singleNumber'],'{position}' => $val['positionTitle'],'{batch}' => $val['productBatch'],'{num}' => $ValidNum)));
				return false ;
			}
		}

		//使用事务处理
		$transaction = Yii::app()->db->beginTransaction();
		try {
			//分拣明细
			$detail = new tbPackingDetail();
			$detail->packingId = $model->packingId;

			//分拣锁定
			$lock = new tbWarehouseLock();
			$lock->type = tbWarehouseLock::TYPE_PACKING;
			$lock->sourceId = $model->packingId;
			$lock->warehouseId = $model->warehouseId;
			$lock->orderId = $model->orderId;

			foreach( $saveData as $sval ){
				unset( $sval['total'] );
				if( array_key_exists ('id',$sval )){
					unset( $sval['id'] );
				}

				//分拣明细
				$_detail = clone $detail;
				$_detail->attributes = $sval;
				if( !$_detail->save() ){
					$this->addErrors( $_detail->getErrors() );
					return false;
				}

				//分拣锁定
				$_lock = clone $lock;
				$_lock->num = $_detail->packingNum;
				$_lock->singleNumber = $_detail->singleNumber;
				$_lock->productBatch = $_detail->productBatch;
				$_lock->positionId = $_detail->positionId;
				if( !$_lock->save() ){
					$this->addErrors( $_lock->getErrors() );
					return false;
				}
			}

			//更改分拣状态和记录分拣人、分拣时间
			$model->state = 1;
			$model->packingTime = new CDbExpression('NOW()');
			$model->userId = Yii::app()->user->id;
			if( !$model->save() ){
				$this->addErrors( $model->getErrors() );
				return false;
			}

			//释放原分配单的锁定
			$a = $lock->deleteAllByAttributes( array('type'=>tbWarehouseLock::TYPE_DISTRIBUTION,'sourceId'=>$model->distributionId,'warehouseId'=>$model->warehouseId) );

			//分拣完成,当分拣仓库与发货仓库不相同时,自动生成调拨单.
			if(!$this->createAllocation( $model,$saveData )){
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
	* 自动生成调拨单,同一仓库也要生成调拨单，仓位会变动。
	*
	*/
	private function createAllocation( $model,$saveData ){
		$tbAllocation = new tbAllocation();
		$tbAllocation->warehouseId = $model->warehouseId;
		$tbAllocation->targetWarehouseId = $model->deliveryWarehouseId;
		$tbAllocation->orderId = $model->orderId;
		$tbAllocation->packingId = $model->packingId;
		$tbAllocation->type = tbAllocation::TYPE_ORDER;
		if( !$tbAllocation->save() ){
			$this->addErrors( $tbAllocation->getErrors() );
			return false;
		}

		$tbAllocationDetail = new tbAllocationDetail();
		$tbAllocationDetail->allocationId = $tbAllocation->allocationId;
		foreach ( $saveData as $val ){
			$_detail = clone $tbAllocationDetail;
			$_detail->num = $val['packingNum'];
			unset($val['packingNum'],$val['orderProductId'],$val['unitRate']);
			$_detail->attributes = $val;
			if( !$_detail->save() ){
				$this->addErrors( $_detail->getErrors() );
				return false;
			}

		}
		return true;
	}




	/**
	 * 分拣单列表 -- 后台
	 * @param  array $condition 查找条件
	 * @param  string $order  排序
	 */
	public function search( $condition = array() ,$order = 't.createTime desc' ){
		$pageSize = tbConfig::model()->get( 'page_size' );
		$criteria = new CDbCriteria;

		if(!empty($condition)){
			foreach ($condition as $key=>$val){
				if( $val == '' ){
					continue ;
				}
				if( $key =='orderId' ){
					$criteria->compare('t.'.$key,$val,true);
				}else if( $key =='string' ){
					$criteria->addCondition($val);
				}else{
					$criteria->compare('t.'.$key,$val);
				}
			}
		}

		$criteria->with = array('distribution');
		$criteria->order = $order;
		$model = new CActiveDataProvider('tbPacking', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));

		$data = $model->getData();
		$return['list'] = array();
		if( $data ){
			$warehouse = tbWarehouseInfo::model()->getAll();
			foreach ( $data as $val) {
				$return['list'][] = $this->setData( $val,$warehouse );
			}

			foreach ( $return['list'] as &$val ){
				//计算行数。
				$rows = array();
				foreach( $val['distribution'] as $key=>&$dval  ){
					unset($dval['detail']);
					$rows[$key] = 0;
					if( isset( $val['detail'][$key] ) ){
						foreach ( $val['detail'][$key] as $detail ){
							foreach ( $detail['pack'] as $deval ){
								$rows[$key]++;
							}
						}
						$dval['pack'] = $val['detail'][$key];
					}else{
						$rows[$key]++;
					}
					$dval['rows'] = $rows[$key];
				}
				$val['rows'] = array_sum($rows);
				unset($val['detail']);
			}
		}
		$return['pages'] = $model->getPagination();
		return $return;
	}


	/**
	* 组装数据
	* @parma CActiveRecord $model
	*/
	public function setData( $model,$warehouse,$unitRate = array() ){
		$productIds = array();
		foreach ( $model->distribution as $dval){
			$productIds[] = $dval->productId;
		}

		//取得单位和辅助单位
		$units = tbProduct::model()->getUnitConversion( $productIds );

		$data = $model->attributes;
		$data['orderId'] = $model->orderId;
		$data['orderTime'] = $model->order->createTime;
		$data['orderState'] = $model->order->state;
		$data['deliveryWarehouse'] = (isset($warehouse[$model->deliveryWarehouseId]))?$warehouse[$model->deliveryWarehouseId]:'';
		$data['warehouse'] = (isset($warehouse[$model->warehouseId]))?$warehouse[$model->warehouseId]:'';
		$data['packinger'] = tbUser::model()->getUsername( $model->packingerId );

		if(  $model->hasRelated('distribution') ){
			$distributionModel = tbDistribution::model()->findByPk(  $model->distributionId );
			$data['distributioner'] = tbUser::model()->getUsername( $distributionModel->userId );
			$data['distributionTime'] = $distributionModel->createTime;
			foreach ( $model->distribution as $dval){
				$k = $dval->orderProductId;
				if(!isset($data['distribution'][$k])){
					$d = $dval->getAttributes( array('orderProductId','singleNumber','color','unitRate'));
					$d['total'] = 0;

					if( isset($units[$dval->productId]) ){
						$d['unit'] = $units[$dval->productId]['unit'];
						$d['auxiliaryUnit'] = $units[$dval->productId]['auxiliaryUnit'];
					}else{
						$d['unit'] = $d['auxiliaryUnit'] = '';
					}

					if( isset($unitRate[$k]) ){
						$d['unitRate'] = $unitRate[$k];
					}
					$data['distribution'][$k] = $d;
				}

				$data['distribution'][$k]['total'] += $dval->distributionNum;

				$detail = $dval->getAttributes( array('positionId','distributionNum','productBatch'));
				$detail['positionTitle'] = $dval->positionTitle;
				$data['distribution'][$k]['detail'][] =  $detail;
			}
		}

		$data['operator'] =($model->operator)?$model->operator->username:'';
		if( isset($model->detail) ){
			$detail = array();
			foreach ( $model->detail as $dval ){
				$k = $dval->orderProductId;
				$p = $dval->positionId;
				if(!isset($detail[$k][$p])){
					$detail[$k][$p] = $dval->getAttributes( array('positionId','positionTitle'));
				}
				$detail[$k][$p]['pack'][] = $dval->getAttributes( array('productBatch','packingNum'));
			}
			$data['detail'] = $detail;
		}
		return $data;
	}

	/**
	* 待分拣订单列表
	*/
	public function waitPackingList( $condition = array() ){
		$pageSize = tbConfig::model()->get( 'page_size' );

		$criteria = new CDbCriteria;
		if(!empty($condition)){
			foreach ($condition as $key=>$val){
				if( $val == '' ){
					continue ;
				}
				if( $key =='orderId' ){
					$criteria->compare('t.'.$key,$val,true);
				}else if( $key =='string' ){
					$criteria->addCondition($val);
				}else{
					$criteria->compare('t.'.$key,$val);
				}
			}
		}

		$criteria->with = array('distribution');
		$criteria->order = 't.createTime ASC';
		$model = new CActiveDataProvider('tbPacking', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));

		$data = $model->getData();

		$return['list'] = array();
		if( $data ){
			$warehouse = tbWarehouseInfo::model()->getAll();
			$packinger = array();
			foreach ( $data as $val) {
				if( !array_key_exists( $val->packingerId,$packinger )){
					$packinger[$val->packingerId] = tbUser::model()->getUsername( $val->packingerId );
				}
				$list = $val->attributes;
				$list['deliveryWarehouse'] = (isset($warehouse[$val->deliveryWarehouseId]))?$warehouse[$val->deliveryWarehouseId]:'';
				$list['warehouse'] = (isset($warehouse[$val->warehouseId]))?$warehouse[$val->warehouseId]:'';
				$list['packinger'] = $packinger[$val->packingerId];

				foreach ( $val->distribution as $dval){
					$distribution = $dval->attributes;
					$distribution['positionTitle'] = $dval->positionTitle;
					$list['distribution'][] = $dval;
				}
				$return['list'][] = $list;
			}
		}
		$return['pages'] = $model->getPagination();
		return $return;
	}


	/**
	* 关闭分拣单
	*
	*/
	public function cancle(  $model ){

		$model->state = '10';
		$model->packingTime = new CDbExpression('NOW()');
		$model->userId = Yii::app()->user->id;
		if( !$model->save() ){
			$this->addErrors( $model->getErrors() );
			return false;
		}

		//释放锁定
		$lock = new tbWarehouseLock();
		$lock->deleteAllByAttributes( array('type'=>tbWarehouseLock::TYPE_DISTRIBUTION,'sourceId'=>$model->distributionId,'warehouseId'=>$model->warehouseId) );


		return true;
	}
}
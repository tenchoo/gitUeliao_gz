<?php
/**
 * 订单分拣管理
 * @author liang
 * @version 0.2
 * @package CFormModel
 *
 */

class PackingForm extends CFormModel {

	public $orderProductId;

	public $wholeNum;

	public $wholePosition;

	public $piecePosition;

	public $pieces;

	private $_warehouseId; //仓库ID

	private $_orderId;

	private $_toMerge = false;

	/**
	* Declares the validation rules.
	* The rules state that account and password are required,
	* and password needs to be authenticated.
	*/
	public function rules()	{
		return array(
			array('orderProductId,wholeNum','required'),
			array('orderProductId,wholeNum', "numerical","integerOnly"=>true,'min'=>'0'),
			array('wholePosition,piecePosition,pieces','safe'),
		);
	}

	public function attributeLabels() {
		return array(
			'orderProductId' => '分拣产品',
			'wholeNum' => '整料数量',
			'piecePosition'=>'零码仓位',
			'wholePosition'=>'整料仓位',
			'pieces'=>'零码数量',
		);
	}

	/**
	 * 确定分拣 -- 后台
	 */
	public function confirm(){
		if( is_array( $this->pieces ) ){
			foreach ( $this->pieces as $key=>$val ){
				if( empty( $val ) || $val == '0' ){
					unset( $this->pieces[$key] ); continue;
				}

				if( !is_numeric( $val ) || $val<0 ){
					$this->addError( 'pieces',Yii::t('warehouse','piece must be numeric' ) );
					return false;
				}
			}
		}else{
			$this->pieces = null;
		}
		if( !$this->validate() ) return false;

		if( $this->wholeNum >0 && empty ( $this->wholePosition ) ){
			$this->addError( 'wholePosition',Yii::t('warehouse','wholePosition must fill' ) );
			return false;
		}

		if( !empty( $this->pieces ) && empty ( $this->piecePosition ) ){
			$this->addError( 'wholePosition',Yii::t('warehouse','piecePosition must fill' ));
			return false;
		}

		if( empty( $this->pieces ) && $this->wholeNum == '0' ){
			$this->addError( 'wholePosition',Yii::t('warehouse','Sorting products can not be empty' ));
			return false;
		}

		//查找需分拣的信息
		$model = tbPack::model()->findByPk( $this->orderProductId ,'state = 0');
		if( !$model ){
			$this->addError( 'wholePosition',Yii::t('warehouse','Did not find the corresponding order' ));
			return false;
		}

		$this->_warehouseId = $model->warehouseId;
		$this->_orderId = $model->orderId;


		//单位换算量
		$units = tbProduct::model()->getUnitConversion( array( $model->productId )  );
		$unitConversion = $units[$model->productId]['unitConversion'];

		//检查仓位信息
		$position = $packingNum = array();
		if( $this->wholeNum >0 ){
			$packingNum['whole'] = $this->wholeNum*$unitConversion;
			$position[$this->wholePosition]['num'] =  $packingNum['whole'];
		}

		if( !empty ( $this->pieces ) ){
			$packingNum['pieces'] = array_sum( $this->pieces );

			//如果是同一个仓位，数量要加起来检查
			if( isset( $position[$this->piecePosition]['num'] ) ){
				$position[$this->piecePosition]['num'] = bcadd( $packingNum['whole'],$packingNum['pieces'],1 );
			}else{
				$position[$this->piecePosition]['num'] = $packingNum['pieces'];
			}
		}

		$packingTotal = array_sum( $packingNum ); //实际分拣数量
		// 实际分拣数量不能大于购买数量
		if( $packingTotal > $model->num ){
			$this->addError( 'wholePosition',Yii::t('warehouse','The number of packing can not be greater than the number of order' ) );
			return false;
		}

		foreach ( $position as $key=>$val ){
			$positionId = $this->checkPositon( $key,$val['num'] , $model->singleNumber );
			if( empty( $positionId ) ){
				return false;
			}

			$position[$key]['positionId'] = $positionId;
		}

		$lockInfo = array_values( $position );

		//分拣明细
		$detail = $remark = array();
		if( $this->wholeNum >0 ){
			$detail[] = array(
					'packingNum'=>$packingNum['whole'],
					'wholes' => $this->wholeNum,
					'positionTitle'=>$this->wholePosition,
					'positionId'=>$position[$this->wholePosition]['positionId']
				);
			$remark[] = $this->wholeNum.'*'.$unitConversion;
		}

		if( !empty ( $this->pieces ) ){
			foreach(  $this->pieces as $val ){
				$detail[] = array(
					'packingNum'=>$val,
					'wholes' => 0,
					'positionTitle'=>$this->piecePosition,
					'positionId'=>$position[$this->piecePosition]['positionId'],
				);
				$remark[] = $val ;
			}
		}

		$remark = implode( ',',$remark );

		//step2,更改分拣状态，写入分拣员，分拣时间等
		$model->state = tbPack::STATE_DONE;
		$model->packUserId = Yii::app()->user->id;
		$model->packTime = date( 'Y-m-d H:i:s' );
		$model->packNum = $packingTotal;
		$model->remark = $remark;

		//开启事务
		$transaction = Yii::app()->db->beginTransaction();

		if( !$model->save()  ) {
			$transaction->rollback();
			$this->addErrors( $model->getErrors() );
			return false;
		}

		//保存分拣明细
		if( !$this->saveDetails( $detail,$model->singleNumber ) ){
			$transaction->rollback();
			return false;
		}

		//锁写仓库库存
		if( !$this->toLock( $lockInfo,$model->singleNumber ) ){
			$transaction->rollback();
			return false;
		}

		tbOrderProduct::model()->updateByPk( $this->orderProductId,
									array('packingNum'=>$packingTotal,'remark'=>$remark ) );

		if( !$this->gotoMerge( $model->orderId ) ){
			$transaction->rollback();
			return false;
		}

		$transaction->commit();

		//自动打印分拣标签,这句要放在commit 之外，必须commit之后才提交打印
		PrintPush::printOrderTag( $this->orderProductId,$msg );

		if( $this->_toMerge === true ){
			//自动备货单,这句要放在commit 之外，必须commit之后才提交打印
			PrintPush::printOrderProduct( $model->orderId,$msg );

		}

        return true;
	}

	/**
	* 保存分拣明细
	* @param array $detail 分拣明细信息
	*/
	private function saveDetails( $detail ){
		$model = new tbPackDetail();
		$model->orderProductId = $this->orderProductId;
		$model->productBatch = $this->getBatch();
		foreach ( $detail as $val ){
			$_model = clone $model;
			$_model->attributes = $val;
			if( !$_model->save()  ) {
				$this->addErrors( $_model->getErrors() );
				return false;
			}
		}
		return true;
	}

	/**
	* 仓库锁写库存
	* @param array $lock 分拣明细信息
	*/
	private function toLock( $lockInfo,$singleNumber ){
		$lock = new tbWarehouseLock();
		$lock->type = tbWarehouseLock::TYPE_PACKING;
		$lock->sourceId = $this->orderProductId;;
		$lock->warehouseId = $this->_warehouseId;
		$lock->orderId = $this->_orderId;
		$lock->productBatch = $this->getBatch();
		$lock->singleNumber = $singleNumber;

		foreach ( $lockInfo as $pval ){
			$_lock = clone $lock;
			$_lock->num 		 = $pval['num'];
			$_lock->positionId   = $pval['positionId'];
			if( !$_lock->save() ){
				$this->addErrors( $_lock->getErrors() );
				return false;
			}
		}
		return true;
	}

	/**
	* 加入归单队列
	*/
	private function gotoMerge( $orderId ){
		$flag = tbPack::model()->exists( 'orderId=:orderId and state=:state',
								array( ':orderId' => $orderId,':state'=>0 ) );
		if( $flag ) return true;

		$model = new tbOrderMerge();
		$model->orderId = $orderId ;
		$model->warehouseId = $this->_warehouseId;
		if( !$model->save() ){
			$this->addErrors( $model->getErrors() );
			return false;
		}

		$this->_toMerge = true; //加入归单队列，如果加入归单队列，commit后要自动打印备货单
		return true;
	}




	public function getBatch(){
		return tbWarehouseProduct::DEATULE_BATCH;
	}

	/**
	 * 分拣--检查仓位是否存在，并且判断当前库存数量是否大于分拣的数量
	 * @param  string $positionTile 仓位
	 * @param  string $packNum  分拣的数量
	 * @param  string $singleNumber  分拣的产品
	 * @return integer $positionId 返回仓位ID
	 */
	private function checkPositon( $positionTile,$packNum, $singleNumber ){
		//判断仓位是否存在
		$positionId = tbWarehousePosition::model()->positionExists( $this->_warehouseId,$positionTile );
		if( empty( $positionId ) ){
			$this->addError( 'piecePosition',Yii::t('warehouse', 'The position number is {position} does not exist',array('{position}'=>$positionTile ) ) );
			return false;
		}

		$batch = $this->getBatch();
		//实时查找可用数量
		$condition = array( 'warehouseId'=>$this->_warehouseId,'positionId'=>$positionId ,'singleNumber'=>$singleNumber,'productBatch'=>$batch);
		$ValidNum =  tbWarehouseProduct::model()->findValidNum( $condition );

		//对比库存数量
		if( $ValidNum <  $packNum ) {
			$this->addError('productBatch',Yii::t('warehouse', 'The products of: {product} ,the position number is {position}, the product batch is: {batch} ,The number can not be greater than the number of inventory，Currently available num is {num}', array('{product}' => $singleNumber,'{position}' => $positionTile,'{batch}' => $batch,'{num}' => $ValidNum)));
			return false ;
		}

		return $positionId;
	}


	/**
	 * 分拣单列表 -- 后台--调试列表
	 * @param  array $condition 查找条件
	 * @param  string $order  排序
	 */
	public function search( $condition = array() ,$order = 't.createTime desc' ){
		$pageSize = tbConfig::model()->get( 'page_size' );
		$criteria = new CDbCriteria;

		if(!empty($condition)){
			foreach ($condition as $key=>$val){
				if( is_null( $val ) ||  $val === '' ){
					continue ;
				}
				if( $key =='singleNumber' ){
					$criteria->compare('t.'.$key,$val,true);
				}else if( $key =='string' ){
					$criteria->addCondition($val);
				}else{
					$criteria->compare('t.'.$key,$val);
				}
			}
		}

		$criteria->order = $order;
		$model = new CActiveDataProvider('tbPack', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));

		$data = $model->getData();
		$return['list'] = array();
		if( $data ){
			foreach ( $data as $val) {
				$return['list'][] = $val->attributes;
			}
		}
		$return['pages'] = $model->getPagination();
		return $return;
	}

	/**
	* @access 分拣调度
	*/
	public function scheduling( $areas ){
		$positionId = Yii::app()->request->getPost('positionId');
		if( !array_key_exists( $positionId, $areas ) ){
			$this->addError( 'positionId',Yii::t('warning','Abnormal parameter') );
			return false;
		}

		$ids = explode(',',Yii::app()->request->getPost('ids') );
		if(  empty( $ids ) ){
			$this->addError( 'positionId',Yii::t('warehouse','No data') );
			return false;
		}
		foreach( $ids as $val ){
			if( (int)$val != $val && $val<1 ){
				$this->addError( 'positionId',Yii::t('warning','Abnormal parameter') );
				return false;
			}
		}

		tbPack::model()->updateByPk( $ids,array('positionId'=>$positionId),'state = 0' );
		return true;

	}

	/**
	* 取得仓库管理员所管理的区域IDs
	*/
	public function ManageWarehouse(){
		$userId = Yii::app()->user->id;
		if( empty( $userId ) ) return;

		$model = tbWarehouseUser::model()->find('userId=:userId',array(':userId'=>$userId ) );
		if( $model ){
			return $model->warehouseId;
		}
	}


	public function getOrderInfo( $orderProductId ){
		if( !is_numeric( $orderProductId ) || $orderProductId<1 ) return ;

		$model = tbPack::model()->findByPk( $orderProductId ,'state = 0');
		if( !$model ) return ;

		$OrderModel = tbOrder::model()->findByPk( $model->orderId );

		$OrderClass = new Order();

		$warehouse = tbWarehouseInfo::model()->findByPk( $OrderModel->warehouseId );

		$data['orderProductId'] = $model->orderProductId;
		$data['positionId'] = $model->positionId;//当前分拣区域
		$data['num']  = $model->num;
		$data['singleNumber'] = $model->singleNumber;

		$data['orderId'] = $OrderModel->orderId;
		$data['orderTime'] = $OrderModel->createTime;

		//发货仓库
		$data['Dwarehouse'] = empty($warehouse)?'':$warehouse->title;
		$data['deliveryMethod'] = $OrderClass->deliveryMethod( $OrderModel->deliveryMethod );
		$data['memo'] = $OrderModel->memo;

		$member = $OrderClass->getMemberDetial( $OrderModel->memberId );

		$units = tbProduct::model()->getUnitConversion( array( $model->productId )  );

		$data['unit'] = $units[$model->productId]['unit'];
		$data['auxiliaryUnit'] = $units[$model->productId]['auxiliaryUnit'];
		$data['unitConversion'] = $units[$model->productId]['unitConversion'];

		list( $data['whole'],$data['piece'] ) = $this->getVolume( $data['num'],$units[$model->productId]['unitConversion'] );
		$data['tags'] = $data['whole']+1;
		$data['companyname'] = $member['companyname'];

		return $data;

	}


	/**
	* 计算整卷和零码
	* @param  int $productId  产品id
	*/
	public function getVolume( $num,$unitConversion ){
		if( $unitConversion >0 && $num > $unitConversion ){
			$whole = bcdiv ( $num,$unitConversion ,0 );
			$piece = bcsub( $num,$whole*$unitConversion,1 );
		}else{
			$whole = 0;
			$piece = $num;
		}

		return array( $whole,$piece );
	}


	/**
	* 查找当前区域下有此产品的仓位
	* @param integer $parentId 分区ID，即仓位的所属parentId
	* @param string  $singleNumber 产品编号
	*/
	public function getPositions( $parentId,$singleNumber ){

		$c = new CDbCriteria;
		$c->compare( 't.parentId',$parentId);

		$position = new tbWarehouseProduct();
		$c->addCondition( 'exists (select null from  '.$position->tableName().' p where p.singleNumber = "'.$singleNumber .'" and p.positionId =t.positionId )' );

		$result = array();
		$data = tbWarehousePosition::model()->findAll( $c );
		foreach( $data as $val ){
			$result[$val->positionId] = $val->title;
		}

		return $result;
	}


}
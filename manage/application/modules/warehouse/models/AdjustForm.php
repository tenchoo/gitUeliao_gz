<?php
/**
 * 调整单管理
 * @author liang
 * @version 0.1
 * @package CFormModel
 */
class AdjustForm extends CFormModel {

	public $num;

	public $singleNumber;

	public $positionId;

	public $batch;

	public $positionTitle;

	public $oldbatch;

	public $remark;



	public function rules()	{
		return array(
			array('num,positionId,batch,oldbatch','required'),
			array('num', "numerical","integerOnly"=>false,'min'=>'0.1','max'=>'10000'),
			array('positionId', "numerical","integerOnly"=>true,'min'=>'1'),
			array('batch,oldbatch', "length",'min'=>'3','max'=>'20'),
			array('batch,oldbatch,positionTitle', "safe"),
		);
	}

	public function attributeLabels() {
		return array(
			'num' => '调整数量',
			'positionId' => '仓库',
			'warrantId'=>'仓库',
			'batch'=>'调整后产品批次',
			'oldbatch'=>'原产品批次',
		);
	}


	/**
	* 调整单--取得可调整的信息
	* @param string $singleNumber
	*/
	public function getAdjustInfo(){
		if( empty( $this->singleNumber ) ) return ;

		//查找产品可调理的订单比例，成交总数量并计算出总可调整数量
		$product = tbProductStock::model()->find( array(
						'select'=>'adjustRatio,relation',
						'condition'=>'singleNumber = :s',
						'params'=>array( ':s'=>$this->singleNumber ),
						) );

		if( !$product ) {
			$this->addError('product',Yii::t('warehouse','The product does not exist') );
			return ;
		}
		//判断产品是否有仓库销定，若有，不允许盘点。
		$falg = tbWarehouseLock::model()->exists( 'singleNumber=:singleNumber',array(':singleNumber'=>$this->singleNumber) );
		if( $falg ){
			$this->addError('product',Yii::t('warehouse','This product exists when the warehouse lock, please lock the release of all the release after the operation') );
			return false;
		}

		$data['adjustRatio'] = $product->adjustRatio;
		$data['color'] = $product->color;

		//查找上次调整时间
		$model = tbWarehouseAdjust::model()->find( array(
					'select'=>'createTime',
					'condition'=>'singleNumber = :s',
					'params'=>array( ':s'=>$this->singleNumber ),
					'order'=>'createTime desc'
					) );
		if( $model ){
			$data['lastAdjustTime'] = $model->createTime;
		}else{
			$data['lastAdjustTime'] = '';
		}

		$data['time'] = date('Y-m-d H:i:s');
		//查找上次调整时间到当前时间中成交总数量
		$deals = tbOrderProduct::model()->find(  array(
					'select'=>'sum( t.`num` ) as num ',
					'condition'=>"t.`singleNumber` = :s and t.`state` = 0 and  EXISTS ( select null from {{order}} o where o.`state`=6 and o.`orderId` = t.`orderId` and o.`dealTime`>'".$data['lastAdjustTime']."' )",
					'params'=>array( ':s'=>$this->singleNumber ),
					) );
		if( $deals ){
			$data['dealNum'] = $deals->num;
		}else{
			$data['dealNum'] = 0;
		}

		$data['adjustNum'] = bcmul( $data['dealNum'],$data['adjustRatio'],1 ) ;
		$data['adjustNum'] = bcdiv( $data['adjustNum'],1000,1 ) ;//千分比
		Yii::app()->session->add('adjustInfo',$data);

		return $data;

	}


	/**
	* 调整单
	* @param array $dataArr 调整的数据
	* @param obj $model
	*/
	public function add(){
		$dataArr = Yii::app()->request->getPost('data');
		$adjustInfo = Yii::app()->session->get('adjustInfo');
		if( empty($dataArr) || !is_array($dataArr) || empty($adjustInfo) ||  $adjustInfo['adjustNum'] <= 0  ){
			$this->addError('num',Yii::t('base','No save data'));
			return false ;
		}

		$total = array();
		foreach ( $dataArr as $val ){
			$this->attributes = $val;
			if( !$this->validate() ) {
				return false ;
			}
			$total[] = $val['num'];
		}

		$totalNum = array_sum( $total );
		if(  $totalNum > $adjustInfo['adjustNum'] ){
			$this->addError('num',Yii::t('warehouse', 'The total number of adjustments should not be greater than the number of adjustable'));
			return false ;
		}

		$transaction = Yii::app()->db->beginTransaction();

		$Adjust = new tbWarehouseAdjust();
		$Adjust->num = $totalNum; //总调整数量
		$Adjust->singleNumber = $this->singleNumber;
		$Adjust->createTime = $adjustInfo['time'];
		$Adjust->userId = Yii::app()->user->id;
		$Adjust->remark = $this->remark;

		if( !$Adjust->save() ){
			$this->addErrors( $Adjust->getErrors() );
			$transaction->rollback();
			return false;
		}

		$tbWarehouseProduct = new tbWarehouseProduct();
		$Detail = new tbWarehouseAdjustDetail();
		$Detail->adjustId = $Adjust->adjustId;
		$outbound = $inputs = array();
		foreach ( $dataArr as $val ){
			//判断此他们是否存在
			$positionName =  tbWarehousePosition::model()->positionName( $val['positionId'],$warehouseId );
			if(  empty( $positionName ) ){
				$this->addError('num',Yii::t('warehouse', 'The position number is {position} does not exist',array('{position}'=>$val['positionTitle'])));
				return false ;
			}


			//实时查找可用数量
			$condition = array( 'positionId'=>$val['positionId'],'singleNumber'=>$this->singleNumber,'productBatch'=>$val['oldbatch']);
			$ValidNum =  tbWarehouseProduct::model()->findValidNum( $condition );

			//对比库存数量
			if( $ValidNum <  $val['num']) {
				$this->addError('num',Yii::t('warehouse', 'The products of: {product} ,the position number is {position}, the product batch is: {batch} ,The number can not be greater than the number of inventory，Currently available num is {num}', array('{product}' => $this->singleNumber,'{position}' => $val['positionTitle'],'{batch}' => $val['oldbatch'],'{num}' => $ValidNum)));
				return false ;
			}

			unset( $val['positionTitle'] );
			$_detail = clone $Detail;
			$_detail->attributes = $val;
			$_detail->warehouseId = $warehouseId;
			if( !$_detail->save() ){
				$this->addErrors( $_detail->getErrors() );
				$transaction->rollback();
				return false;
			}

			//可调整库存情况,调整仓库库存
			$stock = $tbWarehouseProduct->findbyAttributes( $condition );
			if( !$stock ){
				$this->addError('num',Yii::t('warehouse', 'The products of: {product} ,the position number is {position}, the product batch is: {batch} ,The number can not be greater than the number of inventory，Currently available num is {num}', array('{product}' => $this->singleNumber,'{position}' => $val['positionTitle'],'{batch}' => $val['oldbatch'],'{num}' => 0 )));
				return false ;
			}

			if( $val['oldbatch'] != $val['batch'] ){
				//如果两个批次不相等，那么原批次全部出库，再把调整后的数量按新的批次名入库。
				$out_Num = $stock->num;

				$in_Num = bcsub( $stock->num,$val['num'],1 );
				if( $in_Num >0 ){
					$inputs[] =  array( 'warehouseId'=>$warehouseId,
										'num'=>$in_Num,
										'positionId'=>$val['positionId'],
										'batch'=>$val['batch'] );

				}

			}else{
				$out_Num = $val['num'];
			}

			$outbounds[$warehouseId][] = array(  'num'=>$out_Num,
												'positionId'=>$val['positionId'],
												'productBatch'=>$stock->productBatch );
		}

		//生成出库单
		$outModel = new tbWarehouseOutbound();
		$outModel->source = tbWarehouseOutbound::TO_ADJUST;
		$outModel->sourceId = $Adjust->adjustId;

		//出库单明细
		$detail = new tbWarehouseOutboundDetail();
		$detail->singleNumber = $this->singleNumber;
		$detail->color = $adjustInfo['color'];
		foreach ( $outbounds as $key=>$val ){
			$_outbound = clone $outModel;
			$_outbound->warehouseId = $key;
			if( !$_outbound->save() ){
				$transaction->rollback();
				$this->addErrors( $_outbound->getErrors() );
				return false;
			}

			foreach ( $val as $_vval ){
				$_detail = clone $detail;
				$_detail->outboundId = $_outbound->outboundId;
				$_detail->attributes = $_vval;
				if( !$_detail->save() ){
					$transaction->rollback();
					$this->addErrors( $_detail->getErrors() );
					return false;
				}
			}
		}

		if( !empty( $inputs ) ){
			//生成入库单
			$inModel = new tbWarehouseWarrant();
			$inModel->postId = $Adjust->adjustId;
			$inModel->source = tbWarehouseWarrant::FORM_ADJUST;

			if( !$inModel->save() ){
				$transaction->rollback();
				$this->addErrors( $inModel->getErrors() );
				return false;
			}

			//入库单明细
			$inDetail = new tbWarehouseWarrantDetail();
			$inDetail->warrantId = $inModel->warrantId;
			$inDetail->singleNumber = $this->singleNumber;
			$inDetail->color = $adjustInfo['color'];
			$inDetail->orderId = 0;

			foreach ( $inputs as $val ){
				$_inDetail = clone $inDetail;
				$_inDetail->attributes = $val;
				if( !$_inDetail->save() ){
					$transaction->rollback();
					$this->addErrors( $_inDetail->getErrors() );
					return false;
				}
			}
		}

		$transaction->commit();
		return true;
	}


	/**
	 * 发货单列表 -- 后台
	 * @param  array $condition 查找条件
	 * @param  string $order  排序
	 */
	public function search( $condition = array() ,$pageSize = 2 ){

		$criteria = new CDbCriteria;
		if(!empty($condition)){
			foreach ($condition as $key=>$val){
				if( is_null( $val ) || $val == '' ){
					continue ;
				}
				switch( $key ){
					case 'createTime1':
						$criteria->addCondition("t.createTime>'$val'");
						break;
					case 'createTime2':
						$createTime2 = date("Y-m-d H:i:s",strtotime( $val )+86400 ) ; //包含选择的当天
						$criteria->addCondition("t.createTime<'$createTime2'");
						break;
					case 'is_string':
						$criteria->addCondition( $val );//直接传搜索条件
						break;
					default:
						$criteria->compare('t.'.$key,$val);
						break;
				}
			}
		}

		$criteria->order = 't.createTime desc';
		$model = new CActiveDataProvider('tbWarehouseAdjust', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));

		$data = $model->getData();
		$return['list'] = array();
		if( $data ){
			foreach ( $data as $val) {
				$d = $val->attributes;
				$d['username'] = tbUser::model()->getUsername( $val->userId );
				$d['unit']	= ZOrderHelper::getUnitName( $val->singleNumber );
				$return['list'][] = $d ;
			}
		}
		$return['pages'] = $model->getPagination();
		return $return;
	}

}
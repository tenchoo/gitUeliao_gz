<?php
/**
 * 订单发货
 * @author liang
 * @version 0.1
 * @package CFormModel
 *
 */

class Delivery extends CFormModel {
	/**
	* Declares the validation rules.
	* The rules state that account and password are required,
	* and password needs to be authenticated.
	*/

	public $ladingCode;

	public $orderId;

	public $address;

	public $logistics = '';

	public $logisticsNo= '';

	public function rules()	{
		return array(
				array('address','required'),
				array('logistics,logisticsNo','required','on'=>'logistics'),
				array('ladingCode','required','on'=>'ladingCode'),
				array('logisticsNo,address,logistics,ladingCode', "safe"),
				array('ladingCode','checkCode','on'=>'ladingCode'),

				);
	}



	public function attributeLabels() {
		return array(
			'ladingCode' => '提货码',
			'address' => '收货地址',
			'logistics' => '物流公司',
			'logisticsNo' => '物流编号',
		);
	}

	/**
	* 验证码 rule 规则
	*/
	public function checkCode($attribute,$params){
		if( !$this->hasErrors() ) {
			if( $this->$attribute != OrderSms::getDeliveryCode( $this->orderId ) ) {
				$this->addError( $attribute,Yii::t('order','Verification code is not correct') );
			}
		}
	}

	/**
	* 发货
	* @param obj $model
	*/
	public function save( $dataArr,$model ){

		$this->attributes = $dataArr;
		if( !$this->validate() ) {
			return false ;
		}

		//使用事务处理
		$transaction = Yii::app()->db->beginTransaction();
		try {
			//step1 :生成发货单
			$tbDelivery = new tbDelivery();
			$tbDelivery->attributes = $this->getAttributes( array('address','logistics','logisticsNo') ) ;
			$tbDelivery->orderId = $model->orderId;
			if( !$tbDelivery->validate() ){
				$this->addErrors( $tbDelivery->getErrors() );
				return false;
			}

			if( !$tbDelivery->save() ){
				$this->addErrors( $tbDelivery->getErrors() );
				return false;
			}

			//step2 :生成发货出库单，对应产品出库
			if(!$this->output( $model )){
				return false;
			}

			//step3 :订单状态改为待确认收货
			$model->state = 4;
			if( !$model->save() ){
				$this->addErrors( $model->getErrors() );
				return false;
			}

			//生成订单追踪信息
			$message = '操作人：'. Yii::app()->user->getState('username').'(userId:'.Yii::app()->user->id.')';
			tbOrderMessage::addMessage2( $model->orderId,'仓库已发货',$message );

			//释放仓库锁定
			$lock = new tbWarehouseLock();
			$lock->deleteAllByAttributes( array( 'orderId'=>$model->orderId ) );
			$transaction->commit();

			//短信通知
			OrderSms::deliveryNotify( $model );

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

		//备货信息全部出库，查找此订单的全部调拨入库信息，并把对应产品出库。
		$c = new CDbCriteria;
		$c->compare('t.orderId',$model->orderId);
		$c->compare('w.source',tbWarehouseWarrant::FROM_CALLBACK);
		$c->join = 'left join {{warehouse_warrant}} w on t.warrantId = w.warrantId ';
		$model = tbWarehouseWarrantDetail::model()->findAll( $c );
		$data = array();
		foreach ( $model as $val ){
			$k = $val->positionId.'_'.$val->singleNumber.'_'.$val->batch;
			if(!isset($data[$k])){
				$data[$k] =  $val->getAttributes(array('num','positionId','singleNumber','color'));
				$data[$k]['productBatch'] = $val->batch;
			}else{
				$data[$k]['num'] += $val->num;
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

	/**
	 * 发货单列表 -- 后台
	 * @param  array $condition 查找条件
	 * @param  string $order  排序
	 */
	public function search( $condition = array() ){

		$criteria = new CDbCriteria;
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
		$criteria->with = array('operator');
		$criteria->order = 't.createTime desc';
		$model = new CActiveDataProvider('tbDelivery', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));

		$data = $model->getData();
		$return['list'] = array();
		if( $data ){
			foreach ( $data as $val) {
				$return['list'][] = $this->setList( $val );
			}
		}
		$return['pages'] = $model->getPagination();
		return $return;
	}

	/**
	* 组装数据
	* @parma CActiveRecord $model
	*/
	public function setList( $model ){
		$list = $model->attributes;
		$list['deliveryTime'] = $model->createTime;
		$list['operator'] =($model->operator)?$model->operator->username:'';
		$list['tel'] = $model->order->tel;
		$list['name'] = $model->order->name;

		$list['products'] = array();

		foreach ( $model->order->products as $pval ){
			$list['products'][] = $pval->attributes;

		}
		return $list;

		$pInfo =  tbProduct::model()->getUnitConversion( $productIds );
		foreach ( $model->detail as $dval ){
			$detail =  $dval->attributes;
			$detail['unit'] = $pInfo[$dval->productId]['unit'];
			$detail['num'] = array_sum($num[$dval->orderProductId]) ;
			$list['detail'][$dval->orderProductId][] = $detail;
		}
		$list['detail']  = array_values( $list['detail'] );
		return $list;
	}
}
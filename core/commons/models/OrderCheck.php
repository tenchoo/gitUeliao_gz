<?php
/**
 * 订单审核/确定购买，当订单为留货订单时，前台显示为确定购买。
 * @author liang
 * @version 0.1
 * @package CFormModel
 *
 */

class OrderCheck extends CFormModel {

	//是否需要支付，确定购买时，需判断
	public $needPay;


	/**
	* Declares the validation rules.
	* The rules state that account and password are required,
	* and password needs to be authenticated.
	*/
	public function rules()	{
		return array();
	}


	/**
	* 订单审核
	* @param array $dataArr 订单审核提交的数据
	* @param obj $model
	* @param boolean $isManage 是否后台操作
	*/
	public function save( $dataArr,$model,$isManage = false ){
		if( !array_key_exists('products',$dataArr) || !is_array( $dataArr['products'] ) || empty( $dataArr['products'] ) ){
			$this->addError('needPay','订单产品数据不能为空');
			return false;
		}

		$saveProducts = array();
		foreach( $model->products as $val ){
			if( array_key_exists($val->orderProductId, $dataArr['products']) ){
				$saveProducts[$val->orderProductId] = $dataArr['products'][$val->orderProductId]; //有效数据
			}
		}

		if( empty( $saveProducts ) ){
			$this->addError('needPay','订单产品数据不能为空');
			return false;
		}


		$orderType = $model->orderType; //订录原来的订单类型。

		$arr = array( 'deliveryMethod','freight','name','tel', 'memo','address' );
		foreach ( $arr as $val ){
			if( array_key_exists( $val, $dataArr ) ){
				$model->$val = $dataArr[$val];
			}
		}


		//判断客户是否月结，非月结额度需走支付流程
		$creditInfo = tbMemberCredit::creditInfo( $model->memberId );
		if( $model->payState != 2 ){
			if( !empty( $creditInfo ) ){
				$model->payModel = 1;
			}else{
				$model->payModel = $dataArr['payModel'];
			}
		}

		$realPayment = 0 ;

		//使用事务处理
		$transaction = Yii::app()->db->beginTransaction();
		try {
			foreach( $model->products as $val ){
				//采购订单暂不锁定
				if ( $model->orderType != '1' ){
					$val->isfree = 1;
				}
				if( !array_key_exists($val->orderProductId, $saveProducts) ){
					$val->state = 1; //状态标删
				}else{
					$products = $saveProducts[$val->orderProductId];
					if( array_key_exists('num',$products) && $val->saleType != 'whole' ){
						$val->num = $products['num'];
					}

					$val->remark = $products['remark'];
					if( array_key_exists('isSample',$products) && $products['isSample'] == '1' && $val->num < 5 ){
						$val->isSample = 1;
					}else{
						$val->isSample = 0;
					}

					//赠板不算钱
					if( $val->isSample != '1' ){
						$realPayment += $val->num*$val->price;
					}
				}
				if( !$val->save() ){
					$this->addErrors( $val->getErrors() );
					return false;
				}
			}

			$model->realPayment = $realPayment + $model->freight;
			$model->state = 1; //审核通过，进入备货中

			//留货订单确定购买后，转为现货订单,下单时间改为当前
			if( $orderType == '2' ){
				if( empty( $creditInfo ) ){
					//非月结额度需走支付流程
					$this->needPay = true;
				}else{
					$this->needPay = false;

					//判断当前客户可用信用额度是否足以支付
					$comp = bccomp(  $creditInfo['validCredit'], $model->realPayment );
					if( $comp<0 ){
						$this->addError('needPay','客户信用额度不足以支付当前下单总金额');
						return false;
					}
				}

				$model->orderType = 0;
				$model->createTime = new CDbExpression('NOW()');
			}

			if( !$model->save() ){
				$this->addErrors( $model->getErrors() );
				return false;
			}

			//生成订单追踪信息
			if( $model->originatorType == '1'){
				tbOrderMessage::addMessage( $model->orderId,'keep_to_buy' );
			}else{
				$originatorId = Yii::app()->user->id;
				if( $isManage ){
					$message = '操作人：'. Yii::app()->user->getState('username').'(后台 userId:'.$originatorId.')';
				}else{
					$message = '操作人：'. tbProfile::model()->getMemberUserName( $originatorId ).'(业务员 memberId:'.$originatorId.')';
				}

				tbOrderMessage::addMessage2( $model->orderId,'订单信息审核通过',$message );
			}

			//保存批量发货信息
			if( array_key_exists ('batches',$dataArr) && !empty($dataArr['batches']) ){
				foreach( $model->batches as $key=>$bval ){
					if(isset($dataArr['batches']['exprise'][$key])){
						$bval->exprise = $dataArr['batches']['exprise'][$key];
						$bval->remark = $dataArr['batches']['remark'][$key];
						if( !$bval->save() ){
							$this->addErrors( $bval->getErrors() );
							return false;
						}
						unset($dataArr['batches']['exprise'][$key]);
						unset($dataArr['batches']['remark'][$key]);
					}else{
						$bval->delete();
					}
				}
				if(!empty($dataArr['batches']['exprise'])){
					$batches = new tbOrderBatches();
					$batches->orderId = $model->orderId;
					foreach ( $dataArr['batches']['exprise'] as $ek =>$eval  ){
						$_batches = clone $batches;
						$_batches->exprise = $eval;
						$_batches->remark = $dataArr['batches']['remark'][$ek];
						if( !$_batches->save() ) {
							$this->addErrors( $_batches->getErrors() );
							return false;
						}
					}
				}
			}

			switch( $orderType ){
				case '1'://订货,往待采购列表塞数据
					tbOrderPurchase2::importOrder( $model );
					break;
				case '2'://留货订单，前台显示为确定购买。
					//更新留货单状态,并待分配列表塞数据
					$set = array('buyState'=>'1','buyTime'=>time());
					tbOrderKeep::model()->updateAll($set,'orderId=:orderId',array(':orderId'=>$model->orderId));
				case '0': //现货,往待分配列表塞数据
				case '3': //尾货,往待分配列表塞数据
					$falg = tbOrderDistribution::addOne( $model->orderId );
					break;
			}

			if( $model->payState != 2 && !empty( $creditInfo ) ){
				$creditDetail = new tbMemberCreditDetail();
				if( !$creditDetail->changeCredit( $model ) ){
					$this->addErrors( $creditDetail->getErrors() );
					return false;
				}

			}
			$transaction->commit();

			//订单生效短信通知，还有一个新增订单时 tborder
			OrderSms::effective( $model );

			return true;
		} catch (Exception $e) {
 			$transaction->rollback(); //如果操作失败, 数据回滚
			throw new CHttpException(503,$e);
			return false;
		}
	}
}
<?php
/**
 * 订单修改管理
 * @author liang
 * @version 0.1
 * @package CFormModel
 */
class OrderChange extends CFormModel {

	public $state;

	public $checkInfo;


	/**
	* Declares the validation rules.
	* The rules state that account and password are required,
	* and password needs to be authenticated.
	*/
	public function rules()	{
		return array(
			array('state', 'required'),
			array('state', "in","range"=>array(1,2)),
			array('checkInfo','safe'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签
	*/
	public function attributeLabels() {
		return array(
			'state' => '审核结果',
			'checkInfo' => '审核反馈',
		);
	}


	/**
	* 客户提交修改订单
	*/
	public function change( $dataArr,$model ){
		//已开具结算单不允许修改
		if( $model->isSettled >0 ){
			$this->addError( 'state',Yii::t('order','Order form has been issued, can not modify the order information!')  );
			return false;
		}

		if(empty($dataArr['products'])){
			$this->addError( 'state',Yii::t('order','No purchase of the product information, if you do not need to purchase, please cancel the order directly!') );
			return false;
		}

		$falg = tbDistribution::model()->exists( 'orderId=:orderId',array(':orderId'=>$model->orderId) );
		if( $falg ){
			$this->addError( 'state',Yii::t('order','Warehouse distribution sorting, can not modify the order information!')  );
			return false;
		}

		$detailInfo = array();
		foreach ( $model->products as $val){
			$info  = array('orderProductId'=>$val->orderProductId,
								  'oldNum'=>$val->num,
								  'applyNum'=>'0',
								  'checkNum'=>'0',
								  'remark'=>'',
								 );
			if( array_key_exists( $val->orderProductId,$dataArr['products'] ) ){
				$info['applyNum'] = $dataArr['products'][$val->orderProductId]['changeNum'];
				$info['remark'] = $dataArr['products'][$val->orderProductId]['remark'];
			}
			$detailInfo[] = $info;

		}

		if( empty( $detailInfo ) ){
			$this->addError( 'state',Yii::t('order','No purchase of the product information, if you do not need to purchase, please cancel the order directly!') );
			return false;
		}


		$detail = new tbOrderApplychangeDetail();
		$detail->applyId = 0;
		foreach ( $detailInfo as $val ){
			$detail->attributes = $val;
			if( !$detail->validate() ) {
				$this->addErrors( $detail->errors );
				return false ;
			}
		}

		//使用事务处理
		$transaction = Yii::app()->db->beginTransaction();
		try {
			//生成订单修改记录，订单编辑的信息先保存待审核后才更新到订单信息中
			$change = new tbOrderApplychange();
			$change->memberId = $model->memberId;
			$change->orderId = $model->orderId;
			$change->freight = $dataArr['freight'];
			$change->memo = $dataArr['memo'];
			$change->address = $dataArr['address'];
			if( !$change->save() ) {
				$this->addErrors( $change->errors );
				return false ;
			}

			//保存产品数量修改明细信息
			$detail->applyId = $change->applyId;
			foreach ( $detailInfo as $val ){
				$_detail = clone $detail;
				$_detail->attributes = $val;
				if( !$_detail->save() ) {
					$this->addErrors( $_detail->errors );
					return false ;
				}
			}

			//如果是业务员，无须审核，更新订单信息
			if(  Yii::app()->user->getState('usertype') == tbMember::UTYPE_SALEMAN ){
				if( !$this->updateOrderInfo($model,$change,$dataArr['products'])) return false;
			}

			if( !$this->saveBatches( $model,$dataArr['batches'] )) return false;

			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			$this->addError( 'state','发生系统错误' );
			return false;
		}
	}


	private function updateOrderInfo( $model,$change,$products ){

		$realPayment = array() ;

		//保存订单明细备注信息
		foreach ( $model->products as $val){
			$changeInfo = array();
			if( array_key_exists( $val->orderProductId,$products ) ){
				//数量有修改的产品
				if( $val->num != $products[$val->orderProductId]['changeNum'] ){
					$changeInfo[$val->orderProductId] = array('orderProductId'=>$val->orderProductId,
										  'singleNumber'=>$val->singleNumber,
										  'num'=>$val->num,
										  'changeNum'=>$products[$val->orderProductId]['changeNum']
										);
					$val->num = $products[$val->orderProductId]['changeNum'];
				}

				if( array_key_exists( 'isSample',$products[$val->orderProductId] ) && $products[$val->orderProductId]['isSample'] == '1' && $val->num <= 5 ){
					$val->isSample = 1;
				}else{
					$val->isSample = 0;
					$realPayment[] = bcmul( $val->num,$val->price,2 );
				}
				$val->remark = $products[$val->orderProductId]['remark'];
			}else{
				//取消购买的
				$val->state = 1;
				$changeInfo[$val->orderProductId] = array('orderProductId'=>$val->orderProductId,
										  'singleNumber'=>$val->singleNumber,
										  'num'=>$val->num,
										  'changeNum'=> 0
										);
			}

			if( !$val->save() ) {
				$this->addErrors( $val->errors );
				return false ;
			}
		}

		$model->freight = $change->freight;
		$realPayment[] = $change->freight;
		$model->realPayment = array_sum ( $realPayment )  ;
		$model->address =  $change->address;
		$model->memo =  $change->memo;

		if( !$model->save() ) {
			$this->addErrors( $model->errors );
			return false ;
		}

		//订单商品信息修改后，更新到采购或仓库订单的修改信息,并更改锁定量
		if(!empty($changeInfo)){
			if( !$this->notityChange($model,$changeInfo) ) return false;
		}

		//月结，更新使用额度信息
		if($model->payModel == '1'){
			$creditDetail = new tbMemberCreditDetail();
			if( !$creditDetail->changeCredit( $model ) ){
				$this->addErrors( $creditDetail->errors );
				return false ;
			}
		}

		return true;
	}

	/**
	* 订单商品信息修改后, 通知采购或仓库，并更改锁定量。
	*/
	private function notityChange($model,$changeInfo){

		//若已备货完成，无须通知
		if( $model->state >='2' ) return true;

		//修改通知消息
		$content = '订单编号为：'.$model->orderId.'的购买产品信息已更改：<br/>';


		foreach (  $changeInfo as $key=>$val ){
			$t = ($val['changeNum']>0)?$val['changeNum']:'取消购买';
			$content .=  $val['singleNumber'].'的购买数量由 '.$val['num'].' 改为 '.$t.'<br/>';
		}

		//判断订单类型，若为订货订单，看是否已采购完成，否则通知采购。
		if( $model->orderType == tbOrder::TYPE_BOOKING ){

			//修改待采购
			$Purchase = new tbOrderPurchase2();
			$i = 0;
			foreach (  $changeInfo as $val ){
				if( $val['num'] == '0'){
					$falg = $Purchase->cancleOrder( $model->orderId,$val['orderProductId'] );
				}else{
					$falg = $Purchase->changeQuantity( $model->orderId,$val['orderProductId'],$val['changeNum'] );
				}
				if( $falg ) $i++;
			}

			if($i>0) return true;

			//发送订单修改通知给采购
			tbWarehouseMessage::callPurchase( $model->orderId,$content );
		}


		$distribution = tbDistribution::model()->with('detail')->find( 't.orderId = :id ',array(':id'=>$model->orderId));
		//未分配分拣
		if( !$distribution ) return true;

		$detail = $total = array();
		foreach( $distribution->detail as $val ){
			//仓库总分配分拣数量.
			if( array_key_exists( $val->warehouseId,$total ) ){
				$total[$val->warehouseId] = bcadd( $total[$val->warehouseId],$val->distributionNum,2 );
			}else{
				$total[$val->warehouseId] = $val->distributionNum;
			}

			$detail[$val->orderProductId][$val->warehouseId] = $val->distributionNum;
		}

		//已分配，查看根据分配情况来处理减少的购买量，先匹配分配数量最少的。
		$content .= '建议修改为:<br/>';
		$message = array();
		foreach ( $detail as $key=>$val ){
			if( !isset($changeInfo[$key]) ) continue;

			asort($val); //排序,按分配数量从小到大

			//减少备货的数量 = 原购买数量-修改后的购买数量
			$t = $changeInfo[$key]['num'] - $changeInfo[$key]['changeNum'];
			foreach ( $val as $p=>$num ){
				if($changeInfo[$key]['changeNum'] == '0'){
					$message[$p][] = $changeInfo[$key]['singleNumber'].'变更为取消备货.';
					$total[$p] -= $num;
				}else{
					if( $t <= '0' ) continue;

					//仓库减少备货的数量为
					$n = ($t>$num)?$num:$t;

					//总数相应减少
					$total[$p] -= $n;

					$t = $t-$n;

					//写入通知消息
					$message[$p][] = $changeInfo[$key]['singleNumber'].'的备货数量从'.$num.'变更为'.($num - $n);
				}

			}
		}

		foreach ( $total as $key=>$val ){
			//备货无变化的仓库不通知
			if(!isset($message[$key])) continue;
			if( $val == '0' ){
				$opstate = tbWarehouseMessage::OP_CLOSE;
				$message[$key][] = '建议关闭此操作';
			}else{
				$opstate = tbWarehouseMessage::OP_MODIFY;
			}

			tbWarehouseMessage::callWarehouse( $key,$model->orderId,$opstate,$content.implode("<br>", $message[$key]) );
		}

		//更改锁定量
		$tbStorageLock = new tbStorageLock;
		foreach ( $changeInfo as $val ){
			$tbStorageLock->updateAll(array('total'=>$val['changeNum']),
											'orderId = :id and singleNumber =:s',
											array(':id'=>$model->orderId,':s'=>$val['singleNumber']));
		}
		return true;
	}

	/**
	* 保存批量发货信息
	*/
	private function saveBatches( $model,$saveData ){
		//保存批量发货信息
		foreach( $model->batches as $key=>$bval ){
			if(isset($dataArr['batches']['exprise'][$key])){
				$bval->exprise = $saveData['exprise'][$key];
				$bval->remark  = $saveData['remark'][$key];
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

		if(!empty($saveData['exprise'])){
			$batches = new tbOrderBatches();
			$batches->orderId = $model->orderId;
			foreach ( $saveData['exprise'] as $ek =>$eval  ){
				$_batches = clone $batches;
				$_batches->exprise = $eval;
				$_batches->remark = $saveData['remark'][$ek];
				if( !$_batches->save() ) {
					$this->addErrors( $_batches->getErrors() );
					return false;
				}
			}
		}
		return true;
	}

	/**
	* 订单取消的审核
	*/
	public function check( $dataArr,$applyChange,$model ){
		$this->state = isset( $dataArr['state'] )?$dataArr['state']:'';
		$this->checkInfo =  isset( $dataArr['checkInfo'] )?$dataArr['checkInfo']:'';
		if( !$this->validate() ) {
			return false ;
		}

		if( empty($dataArr['products']) ){
			$this->addError( 'state',Yii::t('order','No purchase of the product information, if you do not need to purchase, please cancel the order directly!') );
			return false;
		}

		if( $this->state == '1' ){
			//已开具结算单不允许修改
			if( $model->isSettled >0 ){
				$this->addError( 'state',Yii::t('order','Order form has been issued, can not modify the order information!')  );
				return false;
			}

			$falg = tbDistribution::model()->exists( 'orderId=:orderId',array(':orderId'=>$model->orderId) );
			if( $falg ){
				$this->addError( 'state',Yii::t('order','Warehouse distribution sorting, can not modify the order information!')  );
				return false;
			}
		}else{
			if( empty( $this->checkInfo ) ){
				$this->addError( 'checkInfo',Yii::t('order','Please fill in the audit results') );
				return false;
			}
		}

		//使用事务处理
		$transaction = Yii::app()->db->beginTransaction();
		try {
			//1.如果审核通过，更新订单信息
			if( $this->state == '1' ){
				$applyChange->freight = $dataArr['freight'];
				$applyChange->memo = $dataArr['memo'];
				$applyChange->address = $dataArr['address'];

				//1.1保存审核结果数量
				foreach ( $applyChange->detail as $val ){
					if( isset($dataArr['products'][$val->orderProductId]) && $dataArr['products'][$val->orderProductId]>0 ){
						$val->checkNum = $dataArr['products'][$val->orderProductId]['changeNum'];
						$val->remark = $dataArr['products'][$val->orderProductId]['remark'];
					}else{
						$val->checkNum = 0;
					}

					if( !$val->save() ) {
						$transaction->rollback();
						$this->addErrors( $val->errors );
						return false ;
					}
				}

				//1.2更新订单信息
				if( !$this->updateOrderInfo($model,$applyChange,$dataArr['products'])) return false;

				//1.3保存批量发货信息
				if( !$this->saveBatches( $model,$dataArr['batches'] )) return false;
			}

			//2.保存审核信息
			$applyChange->state = $this->state;
			$applyChange->checkInfo = $this->checkInfo;
			if(!$applyChange->save()){
				$transaction->rollback();
				$this->addErrors( $applyChange->errors );
				return false;
			}

			//3.记录订单追踪信息
			if( $this->state == '1' ){
				$track = 'check_applyChange_ok';
				$m = '通过。';
			}else{
				$track = 'check_applyChange_fail';
				$m = '不通过，审核反馈信息：'.$this->checkInfo;
			}
			tbOrderMessage::addMessage( $model->orderId,$track );

			//4.发送站内信通知客户
			$tbMessage = new tbMessage();
			$tbMessage->title = '订单修改申请审核通知';
			$tbMessage->content = '您的订单号为：'.$model->orderId.' 的订单修改申请已经审核，审核'.$m;
			$tbMessage->memberId = $model->memberId;
			if( !$tbMessage->save() ){
				$transaction->rollback();
				$this->addErrors( $tbMessage->getErrors() );
				return false;
			}

			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			$this->addError( 'state','发生系统错误' );
			return false;
		}
	}

	/**
	 * 修改订单列表
	 * @param  array   $condition 查找条件
	 * @param  integer $pageSize  每页显示条数
	 */
	public static function search( $condition = array(),$pageSize = 10 ){

		$criteria=new CDbCriteria;

		if(!empty($condition)){
			foreach ($condition as $key=>$val){
				if( is_string($val)) $val = trim($val);
				if( $val == '' ){
					continue ;
				}

				if( $key =='createTime1' ){
					$criteria->addCondition("t.createTime>='$val'");
				}else if( $key =='createTime2' ){
					$createTime2 = date("Y-m-d H:i:s",strtotime( $val )+86400 ) ; //包含选择的当天
					$criteria->addCondition("t.createTime<'$createTime2'");
				}else{
					$criteria->compare('t.'.$key,$val);
				}
			}
		}

		//join 表的字段，必须ad成原表有的字段名，否则会被过滤掉。
		$criteria->select = 't.orderId,t.state,t.realPayment,t.freight,a.state as payState';
		$criteria->join = 'inner join {{order_applychange}}  a on (t.orderId = a.orderId)';

		if( Yii::app()->user->getState('usertype') == tbMember::UTYPE_SALEMAN ){
			$userId[] =  Yii::app()->user->id;
			if( tbConfig::model()->get( 'default_saleman_id' ) == Yii::app()->user->id ){
				$userId[] = 0;
			}
			$userId = implode(',',$userId);
			$criteria->join .= ' left join {{member}}  m on (m.memberId = a.memberId and m.userId in ('.$userId.') )';
			$criteria->addCondition("m.memberId is not null ");
		}else {
			$criteria->compare('t.memberId',Yii::app()->user->id);
		}

		$criteria->with = array('products');
		$criteria->order = "t.createTime DESC";
		$model = new CActiveDataProvider('tbOrder', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));

		$data = $model->getData();
		$result['list'] = array();
		$result['pages'] = $model->getPagination();

		if(empty($data)) return $result;

		foreach ( $data as $key => $val ){
			$result['list'][$key] = $val->getAttributes(array('orderId','state','realPayment','freight'));
			$result['list'][$key]['cState'] = $val->payState;
			foreach( $val->products as $pval ){
				$result['list'][$key]['products'][] = $pval->attributes;
			}
		}
		return $result;
	}
}
?>

<?php
/**
 * 财务收款
 * @author liang
 * @version 0.1
 * @package CFormModel
 */
class ReceivablesForm extends CFormModel {

	public $amount;

	public $cause;

	public $memberId;

	public $realPayment;

	public $state ;

	public $type;

	//操作对象
	private $_model;


	public function rules()	{
		return array(
			array('amount','required','on'=>'add'),
			array('cause','required','on'=>'apply'),
			array('state,cause','required','on'=>'check'),
			array('state','in','range'=>array('pass','notpass'),'on'=>'check'),
			array('amount', "numerical","integerOnly"=>false,'min'=>'0.1' ,'on'=>'add'),
			array('cause', "length",'min'=>'3','max'=>'50','on'=>'apply,check'),
			array('cause', "safe"),
		);
	}

	public function attributeLabels() {
		return array(
			'amount' => '收款金额',
			'cause' => '理由',
			'state' => '审核结果',
		);
	}

	/**
	* 新增收款
	*/
	public function add(){
		$this->scenario = 'add';
		$this->amount = Yii::app()->request->getPost('amount');
		if( !$this->validate() ) {
			return false ;
		}

		$model = $this->_model;
		if( $this->type == '0' ){
			$settlementId = $model->settlementId;
		}else{
			$settlementId = date('Ym',strtotime($model->month) );
		}

		//查询当前所有的还款记录,
		$hasRep = $this->receiptCount( $settlementId );
		$receipt = bcadd( $hasRep ,$this->amount,2 );
		if( $receipt > $this->realPayment  ){
			$this->addError('amount','还款总金额不能大于应收款总金额。');
			return false;
		}

		$Deposit = new tbDepositRecords();
		$Deposit->amount = $this->amount;
		$Deposit->type = $this->type;
		$Deposit->settlementId = $settlementId;
		$Deposit->memberId = $this->memberId ;

		$transaction = Yii::app()->db->beginTransaction();
		if( !$Deposit->save() ) {
			$transaction->rollback();
			$this->addErrors( $Deposit->getErrors() );
			return false;
		}

		//如果全部款收齐
		if( $receipt == $this->realPayment ){
			if( $this->type == '0' ){
				$model->isDone = 1;
			}else{
				$t = date( 'Y-m' );
				$t1 = date( 'Y-m-d',strtotime( $t ) );
				if( $model->month < $t1 ){
					$model->isDone = 1;
				}
			}
		}

		$model->receipt = $receipt;
		if( !$model->save() ) {
			$transaction->rollback();
			$this->addErrors( $model->getErrors() );
			return false;
		}

		//同步一到的信用还款记录
		if( $Deposit->type == '1' ){
			$creditDetail = new tbMemberCreditDetail();
			$creditDetail->memberId =  $Deposit->memberId;
			$creditDetail->amount = - $Deposit->amount;
			$creditDetail->orderId = 0;
			$creditDetail->isCheck = 1;
			$creditDetail->mark = '还款：'.$Deposit->recordsId;

			if( !$creditDetail->save() ) {
				$transaction->rollback();
				$this->addErrors( $creditDetail->getErrors() );
				return false;
			}
		}

		$transaction->commit();
		return true;
	}

	/**
	* 申请结算
	*/
	public function apply(){
		$this->scenario = 'apply';
		$this->cause = Yii::app()->request->getPost('cause');
		if( !$this->validate() ) {
			return false ;
		}

		$model = $this->_model;
		if( $this->type == '0' ){
			$settlementId = $model->settlementId;
		}else{
			$t = date( 'Y-m' );
			$t1 = date( 'Y-m-d',strtotime( $t ) );
			if( $model->month == $t1 ){
				$this->addError('amount','当月的下个月才能申请结算');
				return false;
			}
			$settlementId = date('Ym',strtotime($model->month) );
		}

		//查询当前所有的还款记录,
		$hasRep = $this->receiptCount( $settlementId );

		$notReceive = bcsub(  $this->realPayment,$hasRep,2 );

		if( $notReceive <= 0 ){
			$model->isDone = 1;
			$model->save();

			$this->addError('amount','没有未收货款需要申请。');
			return false;
		}

		$receipt = bcadd( $hasRep ,$this->amount,2 );
		if( $receipt > $this->realPayment  ){
			$this->addError('amount','还款总金额不能大于应收款总金额。');
			return false;
		}

		$Deposit = new tbDepositSettleApply();
		$Deposit->amount = $notReceive;
		$Deposit->state = tbDepositSettleApply::STATE_NOCHECK;
		$Deposit->settlementId = $settlementId;
		$Deposit->memberId = $this->memberId ;
		$Deposit->applyCause = $this->cause ;
		$Deposit->type = $this->type ;

		$transaction = Yii::app()->db->beginTransaction();
		if( !$Deposit->save() ) {
			$transaction->rollback();
			$this->addErrors( $Deposit->getErrors() );
			return false;
		}

		$transaction->commit();
		return true;
	}

	/**
	* 申请撤销
	*/
	public function undo( $recordsId ){
		$this->scenario = 'apply';
		$this->cause = Yii::app()->request->getPost('cause');
		if( !$this->validate() ) {
			return false ;
		}

		$Deposit = new tbDepositRecordsUndo();
		$Deposit->recordsId = $recordsId;
		$Deposit->state = tbDepositRecordsUndo::STATE_NOCHECK;
		$Deposit->applyCause = $this->cause ;

		$transaction = Yii::app()->db->beginTransaction();
		if( !$Deposit->save() ) {
			$transaction->rollback();
			$this->addErrors( $Deposit->getErrors() );
			return false;
		}

		$transaction->commit();
		return true;
	}

	/**
	* 是否已申请结算,只检查未审核的。
	*/
	public function isApply( $settlementId ){
		if( empty( $settlementId ) ) return false;

		$flag = tbDepositSettleApply::model()->exists( 'memberId = :memberId and settlementId =:settlementId and state = 0',array( ':memberId'=>$this->memberId, ':settlementId'=>$settlementId ) );

		return $flag;
	}


	/**
	* 已收款总额
	*/
	public function receiptCount(  $settlementId ){
		if( empty( $this->memberId ) ) return 0;

		$model = tbDepositRecords::model()->find( array(
										'select'=>'sum( `amount` ) AS `amount` ',
										'condition'=>'memberId = :memberId and settlementId =:settlementId and state = 0',
										'params'=>array( ':memberId'=>$this->memberId, ':settlementId'=>$settlementId )
									));
		if( $model ){
			return $model->amount;
		}

		return 0;
	}







	/**
	* 收款记录
	*/
	public function receiptList( $settlementId ){
		if( empty( $this->memberId ) ) return ;
		$models = tbDepositRecords::model()->findAll( 'memberId = :memberId and settlementId =:settlementId and state = 0',
													array( ':memberId'=>$this->memberId, ':settlementId'=>$settlementId )	);
		$total = array_map( function( $i ){  return $i->amount;},$models );
		$return['totalReceipt'] = array_sum( $total );

		$return['list'] = array_map( function( $i ){  return $i->attributes;},$models );
		return $return;
	}


	/**
	* 根据客户取得待收款信息
	*/
	public function getInfo(){
		$memberId = Yii::app()->request->getQuery('memberId');
		if( empty( $memberId ) || !is_numeric( $memberId ) ) return ;

		//此客户是月结客户还是非月结客户，若月结客户，出月结账单，非月结客户，出结算单列表
		$isCredit = tbMemberCredit::model()->findByPk( $memberId );
		$return['isCredit'] = false;
		$return['mlist'] = array();
		if( $isCredit  ){
			//如果是月结客户，按月出对账单
			$mlist = tbOrderSettlementMonth::model()->findAll( 'memberId=:memberId and isDone = 0 ',array( ':memberId'=>$memberId ) );
			$return['mlist'] = array_map(function ($i){
				$i->month = date('Y-m',strtotime( $i->month ) )	;
				$notReceive = bcsub( $i->payments,$i->receipt,2 );
				return array('month'=>$i->month,
							  'memberId'=>$i->memberId,
							  'payments'=>$i->payments,
							  'receipt'=>$i->receipt,
							  'notReceive'=>$notReceive,
							);}, $mlist );
			if( $isCredit->state == '0' ){
				$return['isCredit'] = true;
			}
		}

		$criteria = new CDbCriteria;
		$criteria->order = ' t.createTime ASC ';
		$criteria->compare( 'isDone',0 );

		$criteria->addCondition(" exists( select null from {{order}} o where o.`orderId`=t.`orderId` and o.`memberId` = '$memberId' and o.`payModel`  <> '1' and o.`state` != 7 ) ");
		$pageSize = (int)tbConfig::model()->get( 'page_size' );
		$model = new CActiveDataProvider( 'tbOrderSettlement', array(
				'criteria'=>$criteria,
				'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));

		$return['list'] = array_map(function ($i){
			$realPayment = bcadd( $i->productPayments,$i->freight,2 );
			$notReceive = bcsub( $realPayment,$i->receipt,2 );
			return array('settlementId'=>$i->settlementId,
							  'orderId'=>$i->orderId,
							  'realPayment'=>$realPayment,
							  'receipt'=>$i->receipt,
							  'notReceive'=>$notReceive,
							  'createTime'=>$i->createTime
			);}, $model->getData() );
		$return['pages'] = $model->getPagination();

		return $return;
	}

	/**
	* 取得需要收款的账单列表
	*/
	public function getMonthInfo(){
		$nowM = date('Y-m');
		$nowD = date('d');
		$return['month'] = Yii::app()->request->getQuery( 'month',$nowM );
		$return['d'] = $d =  Yii::app()->request->getQuery( 'd' ,$nowD );
		$return['days'] = date( "t",strtotime( $return['month'] ) );

		$criteria = new CDbCriteria;
		$criteria->order = ' t.createTime ASC ';
		$criteria->compare( 'isDone',0 );

		$pageSize = (int)tbConfig::model()->get( 'page_size' );

		if( $d === 'monthPay' ){
			$month = date( 'Y-m-d',strtotime( $return['month'].'-01') );
			$criteria->compare( 'month',date( 'Y-m-d',strtotime( $month.'-01') ) );

			$model = new CActiveDataProvider( 'tbOrderSettlementMonth', array(
				'criteria'=>$criteria,
				'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
			));

			$return['list'] = array_map(function ($i){
				$i->month = date('Y-m',strtotime( $i->month ) )	;
				$notReceive = bcsub( $i->payments,$i->receipt,2 );
				$member  = $i->member->companyname;
				return array( 'month'=>$i->month,
							  'memberId'=>$i->memberId,
							  'payments'=>$i->payments,
							  'receipt'=>$i->receipt,
							  'notReceive'=>$notReceive,
							  'member'=>$member
							);},$model->getData() );
		}else{
			if( is_numeric( $d ) && $d>=1 && $d<= $return['days'] ){
				$t1 = date( 'Y-m-d',strtotime( $return['month'].'-'.$d ) );
				$t2 = date( 'Y-m-d',strtotime( '+1 day',strtotime( $t1 ) ) );// 下一天
			}else{
				$t1 = date( 'Y-m-d',strtotime( $return['month'].'-01') );
				$t2 =  date("Y-m-d", strtotime("+1 months", strtotime( $return['month'] )));
			}

			$criteria->select = 't.settlementId,t.orderId,t.productPayments,t.freight,t.receipt,t.createTime, o.memberId as originatorId';
			$criteria->join = " join {{order}} o on (o.`orderId`=t.`orderId` and o.`payModel`  <> '1' and o.`state`!=7 )";

			$criteria->addCondition("t.createTime>='$t1'");
			$criteria->addCondition("t.createTime<'$t2'");
			$criteria->addCondition("t.productPayments >'0'");

			$model = new CActiveDataProvider( 'tbOrderSettlement', array(
					'criteria'=>$criteria,
					'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
			));

			$return['list'] = array_map(function ($i){
				$member = tbProfileDetail::model()->findByPk( $i->originatorId );
				$realPayment = bcadd( $i->productPayments,$i->freight,2 );
				$notReceive = bcsub( $realPayment,$i->receipt,2 );
				return array('settlementId'=>$i->settlementId,
								  'orderId'=>$i->orderId,
								  'realPayment'=>$realPayment,
								  'receipt'=>$i->receipt,
								  'notReceive'=>$notReceive,
								  'createTime'=>$i->createTime,
								  'member'=>$member->companyname
				);}, $model->getData() );
		}

		$return['pages'] = $model->getPagination();
		return $return;
	}


	/**
	 * 收款记录查询 -- 后台
	 * @param  array $condition 查找条件
	 * @param  string $order  排序
	 */
	public function getDepositRecords(){
		$memberId = $return['memberId'] = Yii::app()->request->getQuery('memberId');
		$return['memberName'] =  Yii::app()->request->getQuery('memberName');
		$return['t1'] =  Yii::app()->request->getQuery('t1',date('Y-m-d') );
		$return['t2'] = $t2 =  Yii::app()->request->getQuery('t2');

		if( !empty( $return['memberName'] ) ){
			$member = tbProfileDetail::model()->findByAttributes( array( 'companyname'=> $return['memberName'] ) );
			if( empty( $member ) ){
				$this->addError( 'memberId','你请求的客户不存在' );
				return $return;
			}
			$memberId = $return['memberId'] = $member->memberId;
			$return['memberName'] = $member->companyname;
		}else{
			$return['memberName'] = '';
			if( empty( $return['t1'] ) ){
				$this->addError( 't1','开始时间不能为空' );
				return $return;
			}
		}

		$criteria = new CDbCriteria;
		$criteria->order = ' t.createTime DESC ';

		if( !empty( $return['t1'] ) ){
			$criteria->addCondition("t.createTime>='".$return['t1']."'");
		}

		$return['t2'] = $t2 =  Yii::app()->request->getQuery('t2');
		if( !empty( $return['t2'] ) ){
			$createTime2 = date("Y-m-d H:i:s",strtotime( $return['t2'] )+86400 ) ; //包含选择的当天
			$criteria->addCondition("t.createTime<'$createTime2'");
		}

		if( is_numeric( $memberId ) &&  $memberId>=1 ){
			$criteria->compare( 'memberId',$memberId );
			$return['memberId'] = $memberId ;
		}else{
			$return['memberId'] = '';
			if( !empty( $return['memberName']  ) ){
				$this->addError( 'memberId', '客户 '.$return['memberName'].' 不存在！' );
				return $return;
			}

		}

		$type  =  Yii::app()->request->getQuery('type');
		if( $type !== 'exportExcel' ){
			$pageSize = (int)tbConfig::model()->get( 'page_size' );

			$model = new CActiveDataProvider( 'tbDepositRecords', array(
				'criteria'=>$criteria,
				'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
			));

			$data = $model->getData();
			$return['pages'] = $model->getPagination();
		}else{
			$data = tbDepositRecords::model()->findAll( $criteria );
		}

		$days = $this->allowUndoDays();//一星期以内的允许申请撤消
		$return['list'] = array();
		$t = time();
		foreach ( $data as $val ){
			$canUndo = $val->state == '0' && ( $t < strtotime( $val->createTime )+86400*$days );
			$list = $val->attributes;
			$list['canUndo'] = $canUndo;
			$list['member']  = $val->member->companyname;
			$return['list'][] = $list;
		}


		$criteria->select = 'sum( `amount` ) AS `amount` ';
		$criteria->compare( 'state',0 );
		$totalModel = tbDepositRecords::model()->find( $criteria );
		if( $totalModel ){
			$return['total'] =  $totalModel->amount;
		}else{
			$return['total'] = 0;
		}

		if( $type !== 'exportExcel' ){
			return $return;
		}
		$this->exportExcelRecords( $return );
	}

	/**
	*	导出excel --收款记录
	*/
	private function exportExcelRecords( $data ){
		$filename = '收款记录';
		$saveData[] =  array( '客户：' ,$data['memberName'],' 收款时间段：',$data['t1'].'_'.$data['t2'],' 总收款金额：' ,$data['total'],'isTitle'=>true);
		$saveData[] =  array();
		$saveData[] =  array();
		$saveData[] =  array( '客户','结算类型','结算单号/月份','收款金额(元)','收款时间','操作人','状态' ,'isTitle'=>true);

		foreach( $data['list'] as $val ){
			$saveData[] = array( $val['member'],
								($val['type'] == '1')?'月结':'结算单',
								$val['settlementId'],
								$val['amount'],
								$val['createTime'],$val['username'],
								($val['state'] == '1')?'已撤销':'正常' );
		}

		Yii::$enableIncludePath = false;
		Yii::import('libs.vendors.phpexcel.*');

		$ExcelFile = new ExcelFile( $filename );
		$ExcelFile->createMergeExl( $saveData );
		exit;
	}

	public function allowUndoDays(){
		return 7;
	}

	public function getUndoInfo( $id ){
		if( empty( $id ) || !is_numeric( $id )) return ;

		$d = $this->allowUndoDays();
		$t = time();
		$t = $t - 86400*$d ;
		$time = date("Y-m-d H:i:s",$t ) ;

		$model = tbDepositRecords::model()->findByPk( $id,'createTime>=:t',array(':t'=>$time) );
		if( !$model ) return ;

		$member = tbProfileDetail::model()->findByPk( $model->memberId );
		$model->memberId = $member->companyname;
		$data = $model->attributes;

		//检查是否有申请撤消
		$data['undoModel'] = tbDepositRecordsUndo::model()->exists( 'recordsId =:recordsId and state in(0,1)',array( ':recordsId'=>$data['recordsId'] ) );

		return $data;
	}

	public function undoList( array $condition ){
		$criteria = new CDbCriteria;

		if( isset( $condition['state'] ) && $condition['state'] == '0'  ){
			$criteria->order = ' t.createTime ASC ';
		}else{
			$criteria->order = ' t.createTime desc ';
		}

		foreach( $condition as $key=>$val ){
			if( is_null($val) || $val === '' ) continue;

			switch( $key ){
				case 't1':
					$criteria->addCondition("t.createTime>='$val'");
					break;
				case 't2':
					$createTime2 = date("Y-m-d H:i:s",strtotime( $val )+86400 ) ; //包含选择的当天
					$criteria->addCondition("t.createTime<'$createTime2'");
					break;
				case 'memberId':
					if( is_numeric( $val ) && $val>0 ){
						$rtable = tbDepositRecords::model()->tableName();
						$criteria->addCondition(" exists( select null from $rtable o where o.`recordsId`=t.`recordsId` and o.`memberId` = '$val') ");
					}
					break;
				default:
					$criteria->compare( 't.'.$key,$val );
					break;
			}
		}




		$pageSize = (int)tbConfig::model()->get( 'page_size' );
		$model = new CActiveDataProvider( 'tbDepositRecordsUndo', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));

		$return['list'] = array_map ( function( $i ){
				$info = $i->attributes;

				$records = $i->records;
				$info['settlementId'] = $records->settlementId;
				$info['amount'] = $records->amount;
				$info['type'] = $records->type;

				$member = $records->member;
				$info['companyname'] = $member->companyname;
				$info['stateTitle'] = $i->stateTitle();
				return $info;
				}, $model->getData()) ;
		$return['pages'] = $model->getPagination();
		return $return;
	}




	public function applyList( array $condition ){
		$criteria = new CDbCriteria;

		if( isset( $condition['state'] ) && $condition['state'] == '0'  ){
			$criteria->order = ' t.createTime ASC ';
		}else{
			$criteria->order = ' t.createTime desc ';
		}

		foreach( $condition as $key=>$val ){
			if( is_null($val) || $val === '' ) continue;

			switch( $key ){
				case 't1':
					$criteria->addCondition("t.createTime>='$val'");
					break;
				case 't2':
					$createTime2 = date("Y-m-d H:i:s",strtotime( $val )+86400 ) ; //包含选择的当天
					$criteria->addCondition("t.createTime<'$createTime2'");
					break;
				case 'memberId':
					$rtable = tbDepositRecords::model()->tableName();
					$criteria->addCondition(" exists( select null from $rtable o where o.`recordsId`=t.`recordsId` and o.`memberId` = '$val') ");
					break;
				default:
					$criteria->compare( 't.'.$key,$val );
					break;
			}
		}

		$pageSize = (int)tbConfig::model()->get( 'page_size' );
		$model = new CActiveDataProvider( 'tbDepositSettleApply', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));

		$return['list'] = array_map ( function( $i ){
				$info = $i->attributes;
				$member = $i->member;
				$info['companyname'] = $member->companyname;
				$info['stateTitle'] = $i->stateTitle();
				return $info;
				}, $model->getData()) ;
		$return['pages'] = $model->getPagination();
		return $return;
	}


	/**
	*	收款撤消审核
	*/
	public function check( $model ){
		if( empty( $model ) ) return ;

		$this->scenario = 'check';
		$this->cause = Yii::app()->request->getPost('cause');
		$this->state = Yii::app()->request->getPost('state');
		if( !$this->validate() ) {
			return false ;
		}

		//如果同意撤消
		if( $this->state == 'pass' ){
			$model->state = tbDepositRecordsUndo::STATE_PASS;
		}else{
			$model->state = tbDepositRecordsUndo::STATE_NOTPASS;
		}

		$model->checkCause = $this->cause;
		$model->checkTime = new CDbExpression('NOW()');
		$model->checkUsername = Yii::app()->user->getState('username');
		$model->checkUserId  = Yii::app()->user->id;

		$transaction = Yii::app()->db->beginTransaction();
		if( !$model->save() ) {
			$transaction->rollback();
			$this->addErrors( $model->getErrors() );
			return false;
		}

		if( $this->state == 'pass' ){
			$records = $model->records;
			$records->state = tbDepositRecords::STATE_UNDO;
			if( !$records->save() ) {
				$transaction->rollback();
				$this->addErrors( $records->getErrors() );
				return false;
			}

			if( $records->type == tbDepositRecords::TYPE_SETTLEMENT ){
				$settModel = $records->settlement;
			}else{
				$settModel = $this->getMonthSettleModel( $records->memberId,$records->settlementId );
			}

			/* $settModel->receipt = bcsub( $settModel->receipt,$records->amount,2 );
			$settModel->isDone = 0;
			if( !$settModel->save() ) {
				$transaction->rollback();
				$this->addErrors( $settModel->getErrors() );
				return false;
			} */

			if( $records->type != tbDepositRecords::TYPE_SETTLEMENT ){
				//同步一到的信用还款记录
				$creditDetail = new tbMemberCreditDetail();
				$creditDetail->memberId =  $records->memberId;
				$creditDetail->amount = $records->amount;
				$creditDetail->orderId = 0;
				$creditDetail->isCheck = 1;
				$creditDetail->mark = '撤消还款：'.$records->recordsId;

				if( !$creditDetail->save() ) {
					$transaction->rollback();
					$this->addErrors( $creditDetail->getErrors() );
					return false;
				}
			}
		}

		$transaction->commit();
		return true;
	}


	/**
	* 申请结算详情
	*/
	public function getApplyInfo( $id,$state = null ){
		if( !is_numeric( $id ) || $id< 1 ) return ;

		$condition = '';
		$params = array();
		if( !is_null($state) && in_array( $state,array(0,1,2) ) ){
			$condition = 'state = :state';
			$params[':state'] = $state;
		}

		$model = tbDepositSettleApply::model()->findByPk( $id , $condition,$params );

		if( !$model ) return ;

		$data = $model->attributes;
		if( $model->type == '1' ){
			$settlement = $this->getMonthSettleModel( $model->memberId,$model->settlementId );
			$data['realPayment'] = $settlement->payments;
		}else{
			$settlement = $model->settlement;
			if( empty( $settlement  ) ) return;
			$data['realPayment'] = bcadd( $settlement->productPayments,$settlement->freight,2 );
		}
		$data['receipt'] = $settlement->receipt;
		$data['notReceive'] =  bcsub( $data['realPayment'],$settlement->receipt,2 );

		if( $model->state == '0' ){
			$model->amount = $data['notReceive'];
		}

		$this->_model = $model;
		$member = $model->member;
		$data['companyname'] = $member->companyname;
		$data['stateTitle'] = $model->stateTitle();
		return $data;
	}

	/**
	* 申请结算--审核
	*/
	public function checkApply(){
		if( empty( $this->_model ) ) return ;

		$this->scenario = 'check';
		$this->cause = Yii::app()->request->getPost('cause');
		$this->state = Yii::app()->request->getPost('state');
		if( !$this->validate() ) {
			return false ;
		}

		//如果同意撤消
		if( $this->state == 'pass' ){
			$this->_model->state = tbDepositSettleApply::STATE_PASS;
		}else{
			$this->_model->state = tbDepositSettleApply::STATE_NOTPASS;
		}

		$this->_model->checkCause = $this->cause;
		$this->_model->checkTime = new CDbExpression('NOW()');
		$this->_model->checkUsername = Yii::app()->user->getState('username');
		$this->_model->checkUserId  = Yii::app()->user->id;

		$transaction = Yii::app()->db->beginTransaction();
		if( !$this->_model->save() ) {
			$transaction->rollback();
			$this->addErrors( $this->_model->getErrors() );
			return false;
		}

		if( $this->state == 'pass' ){
			$settlement = $this->_model->settlement;
			$settlement->isDone = 1;
			if( !$settlement->save() ) {
				$transaction->rollback();
				$this->addErrors( $settlement->getErrors() );
				return false;
			}
		}

		$transaction->commit();
		return true;
	}

	public function getApplys( $settlementId,$memberId ){
		if( !is_numeric( $settlementId ) || $settlementId<1  || !is_numeric( $memberId ) || $memberId<1 ) return ;

		$criteria = new CDbCriteria;
		$criteria->order = ' t.createTime ASC ';
		$criteria->compare( 'settlementId',$settlementId );
		$criteria->compare( 'memberId',$memberId );

		$model = tbDepositSettleApply::model()->findAll( $criteria );

		$list = array_map(function ($i){
				$info = $i->attributes;
				$info['stateTitle'] = $i->stateTitle();
				return $info;}, $model );
		return $list;
	}

	/**
	* 取得收款模型
	*
	*/
	public function getPaymentModel(){
		$id = Yii::app()->request->getQuery('settlementId');
		if( is_null( $id ) ){
			$memberId = Yii::app()->request->getQuery('memberId');
			$month = Yii::app()->request->getQuery('month');
			$model = $this->getMonthSettleModel( $memberId,$month );
			if( !$model ) return ;

			$this->_model = $model;
			$this->memberId = $model->memberId;
			$this->realPayment = $model->payments;
			$this->type = tbDepositRecords::TYPE_MONTHLY;
			return true;
		}else{
			return $this->getSettleModel( $id );
		}

	}

	private function getMonthSettleModel( $memberId,$month ){
		if( !is_numeric( $memberId ) || $memberId<1 || empty( $month ) ) return ;

		$c = new CDbCriteria;
		$c->compare('memberId',$memberId);

		if( strstr($month,"-") ){
			$month .= '-01';
		}else{
			$month .= '01';
		}

		$month = date( 'Y-m-d',strtotime( $month ) );
		$c->compare('month',$month);
		$model = tbOrderSettlementMonth::model()->find( $c );
		return $model;
	}

	private function getSettleModel( $id ){
		if( !is_numeric( $id ) || $id<1 ) return ;

		$model = tbOrderSettlement::model()->findByPk( $id );
		if( !$model ) return ;

		$this->_model = $model;

		$orderModel = $model->order;
		$this->memberId = $orderModel->memberId;
		$this->realPayment = bcadd( $model->productPayments,$model->freight,2 );
		$this->type = tbDepositRecords::TYPE_SETTLEMENT;

		return true;
	}

	public function getOPModel(){
		return $this->_model;
	}

	//取得某月的结算单
	public function getSettleList( $month ){
		if( empty( $this->memberId ) || empty( $month ) ) return array();

		$criteria = new CDbCriteria;
		$criteria->order = ' t.createTime ASC ';

		$t2 =  date("Y-m-d", strtotime("+1 months", strtotime( $month )));
		$criteria->addCondition("t.createTime>='$month'");
		$criteria->addCondition("t.createTime<'$t2'");

		$criteria->addCondition(" exists( select null from {{order}} o where o.`orderId`=t.`orderId` and o.`memberId` = '".$this->memberId."' and o.`payModel` = '1'  and o.`state`!= 7 ) ");

		$models = tbOrderSettlement::model()->findAll( $criteria );
		$list = array_map(function ($i){
			$realPayment = bcadd( $i->productPayments,$i->freight,2 );
			$notReceive = bcsub( $realPayment,$i->receipt,2 );
			return array('settlementId'=>$i->settlementId,
							  'orderId'=>$i->orderId,
							  'realPayment'=>$realPayment,
							  'receipt'=>$i->receipt,
							  'notReceive'=>$notReceive,
							  'createTime'=>$i->createTime
			);}, $models );
		return $list ;
	}

	/**
	* 对账单信息
	*/
	public function billData(){
		$data['memberId']  =  Yii::app()->request->getQuery('memberId');
		$d = date( 'Y-m-d' );
		$data['t1'] = Yii::app()->request->getQuery('t1',$d );
		$data['t2'] = Yii::app()->request->getQuery('t2', $d );
		$data['memberName'] = Yii::app()->request->getQuery('memberName');

		if( empty( $data['memberName'] ) ) return $data ;

		$member = tbProfileDetail::model()->findByAttributes( array( 'companyname'=> $data['memberName'] ) );
		if( empty( $member ) ){
			$this->addError( 'memberId','你请求的客户不存在' );
			 return $data ;
		}

		$data['memberId']  =   $member->memberId;
		$data['memberName'] = $member->companyname;
		if( empty( $data['t1'] ) ){
			$this->addError( 't1','开始时间不能为空' );
			 return $data ;
		}

/* 		if( empty( $data['t2'] ) ){
			$this->addError( 't2','结束时间不能为空' );
			 return $data ;
		} */

		$t1 = date( 'Y-m-d',strtotime( $data['t1']) );
		$t2 = date("Y-m-d H:i:s",strtotime( $data['t2'] )+86400 ) ; //包含选择的当天


		$where = "O.memberId = '". $data['memberId'] ."' and t.`createTime`>='$t1' AND t.`createTime`<'$t2' and t.state<>7 ";

		$sql = "SELECT count(O.orderId) as count, SUM(O.`realPayment`) AS `payments`,SUM(t.`receipt`) AS `receipt`
			FROM  {{order}} O  LEFT JOIN  {{order_settlement}} t ON ( O.orderId = t.orderId ) WHERE $where
			ORDER BY O.`createTime` DESC ";

		$command = Yii::app()->db->createCommand( $sql );
		$totalData = $command->queryRow();

		$data = array_merge( $data,$totalData );


		//判断一下是否月结客户
	/* 	$month = date( 'Y-m-01',strtotime( $t1 ) );
		$month2 = date( 'Y-m-01',strtotime( $data['t2'] ));

		if( $month == $month2 ){
			$c = new CDbCriteria;
			$c->compare( 'memberId',$data['memberId'] );
			$c->compare('month',$month);
			$payModel = tbOrderSettlementMonth::model()->find( $c );

			if( $payModel ){
				$data['receipt'] = ( $data['payments'] > $payModel->receipt )? $payModel->receipt :  $data['payments'];
			}
		}else{
			// 跨月的暂时不计算
			$c->addCondition( "month >= '$month' and month <= '$month2'" );
			$payModel = tbOrderSettlementMonth::model()->findAll( $c );
			$pay = array();
			foreach( $payModel as $val ){
				$pay[$val->month] = $val->attributes;
			}
		}
 */





		$data['notReceive']  = bcsub( $data['payments'],$data['receipt'],2 );

		$criteria = new CDbCriteria;
		$criteria->select = 'orderId,realPayment,freight,state';
		$criteria->order = ' t.createTime ASC ';
		$criteria->compare( 'memberId',$data['memberId'] );
		$criteria->addCondition("t.createTime>='$t1'");
		$criteria->addCondition("t.createTime<'$t2'");


		$models = tbOrder::model()->findAll( $criteria );
		$data['orders'] = $data['cancleOrders'] =array();
		$data['cancleNum'] = 0;
		foreach( $models as $val ){
			$info = array(	'orderId'=>$val->orderId,
							'realPayment'=>$val->realPayment,
							'freight'=>$val->freight,
							'settlementId'=>'',
							'receipt'=>'',
							'isDone'=>'否',
							'settTime'=>''
						);


			$val->getAttributes( array( 'orderId','realPayment','freight','state' ) );

			$sett = tbOrderSettlement::model()->findByAttributes( array('orderId'=>$val->orderId) );
			if ( $sett ){
				//productPayments
				$info['settlementId'] = $sett->settlementId;
				$info['receipt'] = $sett->receipt;
				$info['settTime'] = $sett->createTime;
				if( $sett->isDone ){
					$info['isDone'] = '是';
				}
			}

			$info['products'] = array_map( function( $pval ){
						// 'serialNumber',
						$pinfo = $pval->getAttributes( array('singleNumber','color','price','num','isSample' ) );
						if( $pval->isSample ){
							$pinfo['price'] = $pinfo['subPrice'] = 0.00;
							$pinfo['isSample'] = '是';
						}else{
							$pinfo['subPrice'] = bcmul(  $pval->price ,$pval->num,2 );
							$pinfo['isSample'] = '否';
						}
						return $pinfo;

					}, $val->products );
			if( $val['state'] == '7' ){
				$data['cancleOrders'][] = $info;
				$data['cancleNum']++;
			}else{
				$data['orders'][] = $info;
			}
		}

		//订单退货信息
		$refund = array();

		$criteria = new CDbCriteria;
		$criteria->order = ' t.createTime ASC ';
		$criteria->compare( 't.state',array('1','2','3') );

		$criteria->addCondition(" exists( select null from {{order}} o where o.`orderId`=t.`orderId` and o.`memberId` = '".$data['memberId']."' and o.`createTime`>='$t1' and o.`createTime`<'$t2' )");
		$refundModel = tbOrderRefund::model()->findAll(  $criteria );
		$data['refund'] =  array_map( function( $val ){
			$info = $val->getAttributes( array( 'refundId','orderId','realPayment','createTime','cause' ));
			$info['products'] = array_map( function( $pval ){
						$pinfo = $pval->getAttributes( array('singleNumber','color','price','num' ) );
						$pinfo['subPrice'] = bcmul(  $pval->price ,$pval->num,2 );
						return $pinfo;
					}, $val->products );
			return $info;
		},$refundModel );

		//导出excel
		$type  =  Yii::app()->request->getQuery('type');
		if( $type === 'exportExcel' ){
			$this->exportExcel( $data );
		}
		return $data;
	}

	private function exportExcel( $data ){
		$filename = $data['memberName'].'_'.$data['t1'].'_'.$data['t2'];

		$saveData[] =  array( '客户：' ,$data['memberName'],'下单时间段：',$data['t1'].'_'.$data['t2'],'isTitle'=>true);
		$saveData[] =  array( '总成交金额：' ,$data['payments'], '总已收款：' ,$data['receipt'], '总未收款：' ,$data['notReceive'],'isTitle'=>true);
		$saveData[] =  array( '总成交单数：' ,$data['count'], '总取消单数：' ,$data['cancleNum'],'isTitle'=>true);

		$saveData[] =  array();
		$saveData[] =  array();

		$saveData[] =  array( '订单编号','结算单号','结算单生成时间','总金额','运费','已收款','是否结算完成' ,'产品编号','颜色','单价','数量','赠板','小计','isTitle'=>true);

		foreach( $data['orders'] as $val ){
			$saveData[] = array( $val['orderId'],$val['settlementId'],$val['settTime'],$val['realPayment'],$val['freight'],$val['receipt'],$val['isDone'],'mergeDetails'=> $val['products'] );
		}

		if(!empty(  $data['cancleOrders'] )){
			$saveData[] =  array();
			$saveData[] =  array();
			$saveData[] =  array();

			$saveData[] =  array( '已取消的订单','isTitle'=>true);
			$saveData[] =  array( '订单编号','结算单号','结算单生成时间','总金额','运费','已收款','是否结算完成' ,'产品编号','颜色','单价','数量','赠板','小计','isTitle'=>true);
			foreach( $data['cancleOrders'] as $val ){
				$saveData[] = array( $val['orderId'],$val['settlementId'],$val['settTime'],$val['realPayment'],$val['freight'],$val['receipt'],$val['isDone'],'mergeDetails'=> $val['products'] );
			}
		}

		if(!empty(  $data['refund'] )){
			$saveData[] =  array();
			$saveData[] =  array();
			$saveData[] =  array();

			$saveData[] =  array( '退货信息','isTitle'=>true);
			$saveData[] =  array( '退货单号','订单编号','退货总金额','申请退货时间','理由' ,'产品编号','颜色','单价','数量','小计','isTitle'=>true);
			foreach( $data['refund'] as $val ){
				$saveData[] = array( $val['refundId'],$val['orderId'],$val['realPayment'],$val['createTime'],$val['cause'],'mergeDetails'=> $val['products'] );
			}
		}

		Yii::$enableIncludePath = false;
		Yii::import('libs.vendors.phpexcel.*');

		$ExcelFile = new ExcelFile( $filename );
		$ExcelFile->createMergeExl( $saveData );
		exit;
	}
}
<?php
/**
 * 订单表
 * 订单当前状态：0:待审核,1:备货中,2:备货完成,3:待发货,4:待收货,6:已完成,7:关闭
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$orderId
 * @property integer	$originatorType		下单人类型：0客户下单，1业务员下单
 * @property integer	$memberId			客户ID
 * @property integer	$userId 			业务员ID
 * @property integer	$state				状态
 * @property integer	$orderType			订单类型: 0:现货订单 1:预订订单 2：留货订单 3：尾货订单
 * @property integer	$isDel				删除
 * @property integer	$deliveryMethod		提货方式
 * @property integer	$commentState		评论状态：0未评论，1已评论
 * @property integer	$logistics			物流公司编号
 * @property integer	$realPayment		实付款，订单总额
 * @property integer	$payState			支付状态：0未支付，1已付定金，2已支付，3.已收款
 * @property integer	$isSettled			是否已开具结算单
 * @property integer	$isRecognition		是否已财务确认
 * @property integer	$payModel			支付方式
 * @property integer	$freight			运费
 * @property string		$memo				订单留言
 * @property timestamp	$createTime			订单提交时间
 * @property timestamp	$payTime 			提交支付时间
 * @property timestamp	$dealTime 			交易完成时间
 * @property string		$source				订单来源 enum('web', 'wx', 'ios', 'android')
 * @property string		$name				收货人姓名
 * @property string		$tel				收货人电话
 * @property string		$address			收货地址
 *
 */

 class tbOrder extends CActiveRecord {

	const TYPE_NORMAL = 0;   //现货订单
	const TYPE_BOOKING = 1; //订货订单
	const TYPE_KEEP = 2;    //留货订单
	const TYPE_TAIL = 3;    //尾货订单

	//是否包含订金
	public $hasDeposit = false;


	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{order}}";
	}

	public function relations(){
		return array(
			//注意查询条件
			'products'=>array(self::HAS_MANY,'tbOrderProduct','orderId','condition'=>'products.state = 0'),
			'paymemt'=>array(self::HAS_MANY,'tbOrderPayment','orderId'),
			'user'=>array(self::BELONGS_TO,'tbProfile','', 'on' => 't.userId=user.memberId','select'=>'username'),
			'batches'=>array(self::HAS_MANY,'tbOrderBatches','orderId'),
			'purchase'=>array(self::HAS_MANY, 'tbOrderPurchase2', '', 'on'=>'t.orderId=purchase.orderId'),
			'paymode'=>array(self::BELONGS_TO,'tbPayMent', 'payModel=paymentId'),
			'deposit'=>array(self::HAS_ONE,'tbOrderDeposit','orderId'),
		);
	}

	public function rules() {
		return array(
			array('memberId,orderType,address,name,tel,deliveryMethod,source,warehouseId,packingWarehouseId','required'),
			array('commentState,originatorType','in','range'=>array(0,1)),
			array('source','in','range'=>array('web', 'wx', 'ios', 'android','unknow')),
			array('orderType','in','range'=>array(0,1,2,3)),
			array('freight', "numerical",'min'=>'0','max'=>'10000' ),
			array('realPayment', "numerical",'min'=>'0','max'=>'99999999' ),
			array('memberId,userId,state,isDel,payState,payModel,logistics,deliveryMethod,warehouseId,packingWarehouseId', "numerical","integerOnly"=>true),
			array('memo','length','max'=>'100'),
			array('memo,address,name,tel', "safe"),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'orderId' => '订单ID',
			'memberId' => '客户ID',
			'userId' => '业务员ID',
			'state' => '订单状态',
			'orderType' => '订单类型',
			'payState'=>'支付状态',
			'memo'=>'订单留言',
			'address'=>'收货地址',
			'freight'=>'运费',
			'name'=>'收货人姓名',
			'tel'=>'收货人电话',
			'isPayment'=>'是否已付款',
			'payModel'=>'支付方式',
			'realPayment'=>'实付款',
			'deliveryMethod'=>'提货方式',
			'originatorType'=>'下单人类型',
			'source'=>'订单来源',
			'warehouseId'=>'发货仓库',
			'packingWarehouseId'=>'分拣仓库',
		);
	}

	 public function isLocked() {
		 $total  = tbOrderProduct::model()->countByAttributes(['orderId'=>$this->orderId]);
		 $locled = tbStorageLock::model()->countByAttributes(['orderId'=>$this->orderId]);
		 return $total === $locled;
	}

	/**
	 * 生成一个订单ID
	 * @return integer
	 */
	public function  setOrderId(){
		$model = $this->find( array(
		  'select'=>'MAX(orderId) as orderId',
		));

		if( $model &&  substr($model->orderId,0,6 ) >= date('ymd') ) return ;

		$this->orderId = date('ymd') . str_pad(1, 4, '0', STR_PAD_LEFT);
		//return ZOrderHelper::getOrderId();
		// $t = time();
		// $t = substr($t,5);
		// return date('Ymd') .$t. str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT);
	}


	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->setOrderId();
			$this->createTime = new CDbExpression('NOW()');

			if($this->deliveryMethod == ''){
				$this->deliveryMethod = 0;
			}

			//很重要，勿删。
			if($this->freight == ''){
				$this->freight = 0;
			}
		}
		return true;
	}

	public function countByKeep( $state = 0) {
		$orderKeep = tbOrderKeep::model()->tableName();
		$sql       = "select count(*) from {$this->tableName()} A where not exists(select 1 from {$orderKeep} B where A.orderId=B.orderId or B.state=0) and A.state={$state}";
		$cmd = $this->getDbConnection()->createCommand( $sql );
		$result = $cmd->queryColumn();
		return intval($result[0]);
	}

	public function findAllByKeep( $state = 0) {
		$orderKeep = tbOrderKeep::model()->tableName();
		$page      = Yii::app()->request->getQuery('page');
		$page_size = tbConfig::model()->get('page_size');
		$offset    = $page * $page_size;
		$sql       = "select orderId from {$this->tableName()} A where not exists(select 1 from {$orderKeep} B where A.orderId=B.orderId) and A.state = {$state} order by A.createTime limit {$offset},{$page_size}";
		$cmd = $this->getDbConnection()->createCommand( $sql );
		$ids = $cmd->queryColumn();
		if( !$ids ) {
			return array();
		}
		return $this->findAllByPk( $ids );
	}

	 public function getUsername() {
		if ( empty ($this->userId )) return ;
		$userName = tbProfile::model()->getMemberUserName( $this->userId );
		return $userName;
	 }

	  public function getCompanyname() {
		if ( empty ($this->memberId )) return ;
		$model = tbProfileDetail::model()->findByPk( $this->memberId );

		$c = '';
		if( $model ){
			$c = empty($model->shortname )? $model->companyname :$model->shortname ;
		}

		if( empty ( $c ) ){
			$c = tbProfile::model()->getMemberUserName( $this->memberId );
		}
		return $c;
	 }

	 public function getProductsTotal() {
		 $total = tbOrderProduct::model()->count("orderId=:id and state = 0", array(':id'=>$this->orderId));
		 return intval($total);
	 }

	 public function getProducts() {
		 $result = tbOrderProduct::model()->findAllByAttributes(array('orderId'=>$this->orderId,'state'=>'0'));
		 return $result;
	 }

	 public function getOrderPayMode() {
	 	$payment = tbPayMent::model()->findByPk($this->payModel);
	 	if($payment) {
	 		return $payment;
	 	}
	 	$payment = new tbPayMent();
	 	$payment->paymentTitle = '未支付';
	 	return $payment;
	 }


	 /**
	 * 保存后的操作
	 */
	protected function afterSave(){
		if( $this->realPayment <= 0 ){
			$this->payState = 2;
		}

		if($this->isNewRecord){
			if( $this->state =='1' ){
				//订单生效短信通知，还有一个发送程序在审核通过程序
				OrderSms::effective( $this );
			}

			if( $this->orderType == tbOrder::TYPE_KEEP ){
				//留货
				$addModel = new tbOrderKeep();
				$addModel->orderId = $this->orderId;
				$keeyday = tbConfig::model()->get( 'order_keep_time' );
				$addModel->expireTime = strtotime( $keeyday.' days');
				if( !$addModel->save() ) {
					$this->addErrors( $addModel->getErrors() );
					return false;
				}
			}
		}else{
			//发货或取消订单后，释放锁定量。
			if( in_array($this->state,array('4','6','7')) ){
				$this->onAfterSave = array('tbStorageLock','freeAll');
				$obj = new stdclass;
				$obj->orderId = $this->orderId;
				$this->onAfterSave( new CEvent( $obj )  );
			}
		}
	}

	protected function afterFind(){
		if(  $this->hasRelated('user') ){
			if(is_null( $this->user )){
				$this->user =  new stdClass();
				$this->user->username = Yii::t('base','system saleman');
			}
		}
	}

	/**
	* 取消订单, 未结算前允许取消，已结算走退换货流程
	* @param string $reason 取消理由
	*/
	public function closeOrder( $whoClose,$closeReason,$opId){
		if( empty($this->orderId) || empty($closeReason)|| empty($opId) ){
			 return false;
		}

		//新增取消记录
		$close = new tbOrderClose();
		$close->orderId = $this->orderId;
		$close->opId = $opId;
		$close->opType = $whoClose;
		$close->reason = $closeReason;
		if(!$close->save()){
			$this->addErrors( $close->errors );
			return false;
		}

		$oldState = $this->state;

		//更改订单状态
		$this->state = 7 ;
		if(!$this->save()){
			return false;
		}

		if( $this->orderType ==  self::TYPE_BOOKING ){
			//取消待采购订单
			$flag = tbOrderPurchase2::model()->cancleOrder( $this->orderId );
		}else if( $this->orderType == self::TYPE_TAIL ){
			//取消尾货订单，降价促销的不处理，整批消息的要把已售完的状态恢复一下。
			 $tailIds = array();
			 foreach (  $this->products as $val ){
				if ( $val->saleType == 'whole' ) {
					$tailIds[$val->tailId] = $val->tailId;
				}
			 }

			 if( !empty( $tailIds ) ){
				tbTail::model()->updateByPk( $tailIds,array('isSoldOut'=>'0'),' isSoldOut = 1 ' );
			 }
		}

		//如果已备货完成，释放相关orderId的锁定，若未完成，在具体环节关闭。不能统一关闭。
		if( $oldState >= '2' ){
			//释放仓库锁定
			$lock = new tbWarehouseLock();
			$lock->deleteAllByAttributes( array( 'orderId'=>$this->orderId ) );
		}

		//月结
		if( $this->payModel == '1' ){
			$creditDetail = new tbMemberCreditDetail();
			if( !$creditDetail->cancleCredit( $this ) ){
				$this->addErrors( $creditDetail->getErrors() );
				return false;
			}
		}
		return true;
	}

	public function getUserInfo() {
		$userInfo = tbMember::model()->with('profiledetail')->findByPk($this->memberId);
		if($userInfo) {
			return $userInfo;
		}
		return false;
	}
}
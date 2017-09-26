<?php

/**
 * 订单管理
 */
class Order {
	public $userType;
	public $memberId;

	public function __construct() {
		$this->userType =  Yii::app()->user->getState('usertype');
		$this->memberId = Yii::app()->user->id;
	}

	/**
	* 上传凭证
	* @param integer $paymemtId
	* @param string $voucher
	*/
	public function uploadVoucher( $id,$voucher ){
		if(empty($id)||empty($voucher)){
			return ;
		}

		$criteria=new CDbCriteria;
		$criteria->compare('t.paymentId',$id);
		if( $this->userType == tbMember::UTYPE_SALEMAN ){
			$criteria->addCondition( 't2.userId = '.$this->memberId);
			$criteria->join = 'inner join {{order}} t2 on ( t.orderId=t2.orderId )'; //连接表
		}else if( $this->userType == tbMember::UTYPE_MEMBER ){
			$criteria->addCondition( 't2.memberId = '.$this->memberId);
			$criteria->join = 'inner join {{order}} t2 on ( t.orderId=t2.orderId )'; //连接表
		}

		$model = tbOrderPayment::model()->find( $criteria );
		if( $model ){
			$model->voucher = $voucher;
			if($model->save()){
				return true;
			}else{
				return $model->getErrors();
			}
		}
		return false;

	}


	/**
	* 删除订单
	* @param integer $orderId 订单ID
	* @param integer $userId 	 业务员ID
	*/
	public static function del( $orderId ,$userId = '' ){
		if( empty( $orderId ) ) {
			return false ;
		}

		$opId  = Yii::app()->user->id;
		$attributes = array( 'isDel'=>1 );
		$condition = ' state in( 6,7 ) '; //允许删除的订单状态
		$count = tbOrder::model()->updateByPk( $orderId,$attributes,$condition );
		if( $count ){
			return true;
		}
		return false;
	}






	/**
	* 取消订单理由
	*/
	public static function closeReason(){
		return array('我不想买了','信息填写错误，重新拍','暂时缺货','其它原因');
	}

	/**
	* 取消订单,取消订单流程需重写。。
	* @param integer $orderId  订单ID
	* @param integer $whoClose  订单关闭人 0客户，1业务员，2 后台管理员取消  3 系统取消
	* @param string $closeReason  订单关闭理由
	* @param integer $opId  操作者登录ID,app端传值过来.
	*/
	public static function cancleOrder( $orderId ,$whoClose, $closeReason,$opId='' ) {
		$opId = ( $opId=='' )?Yii::app()->user->id:$opId;

		if( empty( $orderId ) || empty( $opId ) || empty( $closeReason ) || !in_array( $whoClose , array( '0','1','2','3' ) )  ) {
			return false ;
		}

		$condition = " state <= '3' "; //未发货前允许取消，已发货走退换货流程
		$params = array();
		if( $whoClose == '0' ){
			$condition  .= ' and memberId = :memberId ';
			$params[':memberId'] =  $opId ;
		}

		$model = tbOrder::model()->findByPk( $orderId,$condition,$params );
		if( !$model ){
			return false;
		}

		if( $whoClose == '1' ){
			$isserve = tbMember::checkServe( $model->memberId,$opId );
			if( !$isserve ){
				return false;
			}
		}

		$transaction =  Yii::app()->db->beginTransaction();
		try	{
			$m = new tbOrderApplyclose;

			//业务员申请直接取消，客户申请判断订单状态，若为未审核的直接取消,否转入申请表，待业务员审核
			if( $whoClose == '0' && $model->state != 0  ){
				//判断是否已经申请过取消
				if( $m->hasApply( $orderId ) ){
					return array(array(Yii::t('order','You have applied for cancellation, please wait for the clerk to review!')));
				}

				$m->state = 0;
				tbOrderMessage::addMessage( $orderId,'applyCancle' );
				tbWarehouseMessage::holdon( $orderId );
			}else{
				if( !$model->closeOrder( $whoClose,$closeReason,$opId ) ){
					return $model->errors;
				}

				$m->state = 1;
			}

			$m->orderId = $orderId;
			$m->reason = $closeReason;
			if(!$m->save()){
				return $m->errors;
			}

		   $transaction->commit();
		   return true;
		} catch(Exception $e) {
		   $transaction->rollBack();
		   return false;
		}
	}

	/**
	 * 查找订单
	 * @param  array $condition 查找条件
	 * @param  string $order	排序
	 * @param  integer $pageSize 每页显示条数
	 */
	public function search( $condition = array() , $order = '',$pageSize=2 ) {
		$criteria=new CDbCriteria;
		$criteria->select = 't.orderId,t.state,t.orderType,t.memberId,t.payState,t.isSettled,t.createTime,t.realPayment,t.freight,t.payModel,t.commentState,t.hasRefund';
		if(!empty($condition)){
			foreach ($condition as $key=>$val){
				if( is_string($val) ) $val = trim($val);

				if( is_null($val) || $val === '' ){
					continue ;
				}

				switch( $key ){
					case 'createTime1':
						$criteria->addCondition("t.createTime>='$val'");
						break;
					case 'createTime2':
						$createTime2 = date("Y-m-d H:i:s",strtotime( $val )+86400 ) ; //包含选择的当天
						$criteria->addCondition("t.createTime<'$createTime2'");
						break;
					case 'orderId':
						$criteria->compare('t.'.$key,$val,true);
						break;
					case 'is_string':
						$criteria->addCondition( $val );//直接传搜索条件
						break;
					case 'singleNumber':
						$criteria->addCondition( "exists ( select null from {{order_product}} op where op.orderId = t.orderId and op.singleNumber like '$val%' and op.state = 0  ) " );
						break;
					default:
						$criteria->compare('t.'.$key,$val);
						break;
				}
			}
		}

		if(  $this->userType == tbMember::UTYPE_SALEMAN ){
			$userId[] =  $this->memberId;
			if( tbConfig::model()->get( 'default_saleman_id' ) == $this->memberId ){
				$userId[] = 0;
			}
			$userId = implode(',',$userId);
			$criteria->join .= ' left join {{member}}  m on (m.memberId = t.memberId and m.userId in ('.$userId.') )';
			$criteria->addCondition("m.memberId is not null ");
		}

		$criteria->with = array('user');

		$criteria->order = $order;//"t.createTime DESC,t.orderId DESC";//默认为时间倒序
		$model = new CActiveDataProvider('tbOrder', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));

		$data = $model->getData();
		$result['list'] = $result['units'] = array();
		$result['pages'] = $model->getPagination();

		if(empty($data)) return $result;


		$keeps = array();
		$productIds = array();
		foreach ( $data as $key => $val ){
			if($val->orderType == '2'){
				$keeps[$val->orderId] = $val->orderId;
			}

			$val->createTime = substr( $val->createTime,0,10);
			$result['list'][$key] = $val->getAttributes(array('orderId','state','orderType','memberId','payState','createTime','realPayment','freight','payModel','commentState','isSettled','hasRefund'));
			$result['list'][$key]['oType'] = $val->orderType;
			$result['list'][$key]['orderType'] = $this->orderType( $val->orderType );
			$result['list'][$key]['username'] = '';
			$result['list'][$key]['stateTitle'] =$this->stateTitle( $val->state );
			$result['list'][$key]['paymemt'] = array();

			$result['list'][$key]['products'] = array();
			foreach( $val->products as $pval ){
				$productIds[$pval->productId] = $pval->productId;
				$result['list'][$key]['products'][] = $pval->attributes;
			}

			if( $val->user ){
				$result['list'][$key]['username'] = $val->user->username;
			}

			foreach( $val->paymemt as $payval ){
				$result['list'][$key]['paymemt'][] = $payval->attributes;
			}

			if( $this->userType!='member' ){
				$result['list'][$key]['member'] = $this->getMemberDetial( $val->memberId );
			}
		}

		$keepOrders =  $this->getKeepOrders( $keeps );
		if(!empty($keepOrders)){
			foreach ($result['list'] as &$val){
				if( array_key_exists( $val['orderId'],$keepOrders) ){
					$val['keep'] = $keepOrders[$val['orderId']];
				}
			}

		}

		//取得产品单位
		$result['units'] = tbProduct::model()->getUnitConversion( $productIds );
		return $result;
	}

	private function getKeepOrders( $orderids ){
		if( empty( $orderids ) ) return ;
		$c = new CDbCriteria;
		$c->compare('orderId',$orderids);
		$c->compare('buyState','0');
		$keepOrder = tbOrderKeep::model()->findAll( $c );

		$stateTitle = array('0'=>'留货审核中','1'=>'留货审核通过','2'=>'留货审核不通过');
		$keepOrders = array();
		foreach ( $keepOrder as $val){
			$val->expireTime = date('Y/m/d',$val->expireTime);
			$keepOrders[$val->orderId] = $val->attributes;
			$keepOrders[$val->orderId]['stateTitle'] = $stateTitle[$val->state];
		}
		return $keepOrders;
	}

	/**
	* 取得客户详细信息
	*/
	public function getMemberDetial( $memberId ){
		$model = tbProfileDetail::model()->findByPk( $memberId );
		if( !$model ){
			$model = new tbProfileDetail();
		}
		return $model->getAttributes(array('tel','corporate','companyname','address','shortname'));
	}

	/**
	* 提货方式
	*/
	public function deliveryMethod( $i='' ){
		$arr = array('1'=>'自提','2'=>'物流配送','3'=>'公司配送');

		if($i == '' ) return $arr;
		return array_key_exists( $i,$arr)?$arr[$i]:'';
	}

	/**
	* 订单类型标题
	* @param integer $i 订单类型
	*/
	public function orderType( $i ){
		$arr = array('0'=>'现货订单','1'=>'预定订单','2'=>'留货订单','3'=>'尾货订单');
		return array_key_exists( $i,$arr )?$arr[$i]:'';
	}

	/**
	* 状态
	*/
	public function stateTitle( $i='' ){
		$arr = array('0'=>'待审核','1'=>'备货中','2'=>'备货完成','3'=>'待发货','4'=>'待收货','6'=>'交易完成','7'=>'交易关闭');
		return array_key_exists( $i,$arr)?$arr[$i]:'';
	}

	/**
	 * 商品价格显示格式
	 * 保留两位小数
	 * @param float $price
	 * @return string
	 */
	final public static function priceFormat($price,$space=',') {
		return number_format($price, 2, '.', $space);
	}

	/**
	 * 商品数量显示格式
	 * 保留1位小数
	 * @param float $quantity
	 * @return string
	 */
	final public static function quantityFormat($quantity) {
		return sprintf("%.1f", $quantity);
	}

	/**
	* 求余,得到 $operand 被 $modulus 除后的余数字符串,不考虑负数,用于单位换算，如卷/码。
	* @param float $operand
	* @param integer $modulus
	*/
	final public static function unitMod( $operand, $modulus ){
		if ( $modulus > '0' && $operand >= $modulus ){
			$operand = explode( ".",$operand );
			$operand[0] = $operand[0]%$modulus;
			$operand = implode( ".",$operand );
		}
		return Order::quantityFormat( $operand );
	}

	/**
	 * 手机端订单列表，以断点定位查找
	 * @param  string $nextid	断点标识，必须以orderId排序才能以orderId为断点标识，往上查找，所以搜索条件为小于此orderId
	 * @param  array $condition 查找条件
	 * @param  integer $pageSize 每页显示条数
	 */
	public function mList( $type,$nextid = 0,$condition = array(),$pageSize = 8 ){

		if( !empty( $type ) ){
			$tab = $this->tabs( $type );
			$condition =array_merge(  $tab['condition'],$condition );
		}

		if( $this->userType =='member' ){
			$condition['memberId'] = $this->memberId ;
		}

		$criteria=new CDbCriteria;
		$criteria->select = 't.orderId,t.state,t.orderType,t.memberId,t.payState,t.isSettled,t.createTime,t.realPayment,t.freight,t.payModel,t.commentState,t.hasRefund';
		if(!empty($condition)){
			foreach ($condition as $key=>$val){
				if( is_string($val)) $val = trim($val);
				if( is_null($val) || ( $val != '0' && $val == '' ) ){
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
					case 'orderId':
						$criteria->compare('t.'.$key,$val,true);
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

		//断点标识，必须以orderId排序才能以orderId为断点标识，往上查找，所以搜索条件为小于此orderId
		if( !empty( $nextid ) ){
			$criteria->addCondition(" t.orderId < '$nextid' ");
		}

		if(  $this->userType == tbMember::UTYPE_SALEMAN ){
			$userId[] =  $this->memberId;
			if( tbConfig::model()->get( 'default_saleman_id' ) == $this->memberId ){
				$userId[] = 0;
			}
			$userId = implode(',',$userId);
			$criteria->join .= ' left join {{member}}  m on (m.memberId = t.memberId and m.userId in ('.$userId.') )';
			$criteria->addCondition("m.memberId is not null ");
		}

		$criteria->with = array('user');
		$criteria->limit = $pageSize;    //取1条数据，如果小于0，则不作处理
		$criteria->order = "t.orderId DESC";

		$result['list'] = $result['units'] = array();
		$result['nextid'] = $nextid ;
		$result['hasNext'] = false ;

		$data = tbOrder::model()->findAll( $criteria );
		if( empty($data) ) return $result;

		$keeps = array();
		$productIds = array();
		$com = array();
		foreach ( $data as $key => $val ){
			$result['nextid'] = $val->orderId;
			$val['state'] = (int)$val['state'];
			$val->createTime = substr( $val->createTime,0,10);


			if($val->orderType == '2'){
				$keeps[$val->orderId] = $val->orderId;
				$stateTitle = '';
			}else{
				if( empty( $val['payModel'] ) && $val['state'] == 0 && $val['payState']=='0'   ){
					$stateTitle= '待付款';
				}else{
					$stateTitle = $this->stateTitle( $val['state'] );
				}
			}

			$orderInfo = $val->getAttributes(array('orderId','state','orderType','memberId','payState','createTime','realPayment','freight','payModel','commentState','hasRefund'));
			$orderInfo['oType'] = $val->orderType;
			$orderInfo['orderType'] = $this->orderType( $val->orderType );
			$orderInfo['username'] = '';
			$orderInfo['stateTitle'] = $stateTitle;
			$orderInfo['paymemt'] = array();
			foreach( $val->products as $pval ){
				$productIds[$pval->productId] = $pval->productId;
				$orderInfo['products'][] = $pval->attributes;
			}

			if( $val->user ){
				$orderInfo['username'] = $val->user->username;
			}

			foreach( $val->paymemt as $payval ){
				$orderInfo['paymemt'][] = $payval->attributes;
			}


			if( $this->userType == tbMember::UTYPE_SALEMAN ) {
				if( !array_key_exists( $val->memberId ,$com ) ){
					$com[$val->memberId] = $this->getMemberDetial( $val->memberId );
				}
				$orderInfo['member'] = $com[$val->memberId] ;
				$orderInfo['companyname'] = $orderInfo['member']['companyname'];
			}

			$result['list'][] = $orderInfo;

		}

		if ( count( $result['list'] ) == $pageSize ){
			$result['hasNext'] = true ;
		}

		$keepOrders =  $this->getKeepOrders( $keeps );
		if(!empty($keepOrders)){
			foreach ($result['list'] as &$val){
				if( array_key_exists( $val['orderId'],$keepOrders) ){
					$val['keep'] = $keepOrders[$val['orderId']];
				}
			}
		}

		//取得产品单位
		$result['units'] = tbProduct::model()->getUnitConversion( $productIds );
		$this->setButtons( $result['list'] );

		return $result;

	}

	/**
	* 前台页面显示按纽
	*/
	public function setButtons( &$list ){
		$ids1 = $ids2 = array();
		foreach( $list as $val ){
			if( $val['state']<= '3' ){
				$ids1[] = $val['orderId'];
				if( $val['isSettled'] == '0' ){
					$ids2[] = $val['orderId'];
				}
			}
		}

		$applyClose =  tbOrderApplyclose::model()->hasApplyClose( $ids1 );
		$applyChange = tbOrderApplychange::model()->hasApplyChange( $ids2 );

		foreach ( $list as &$val ){
			$val['buttons'] = $this->setButton( $val,$applyClose ,$applyChange );
		}
	}

	/**
	* 前台页面显示按纽
	*/
	private function setButton( $val,$applyClose ,$applyChange ){
		$buttons = array();

		switch( $val['state'] ){
			case '0':
				if( $this->userType =='saleman' ) {
					if( array_key_exists('keep',$val) ){
						if( $val['keep']['state']!= '2' ) {
							$buttons[] = 'confirmbuy'; //确定购买按纽
							$buttons[] = 'delay'; //申请延期按纽
						}
						$buttons[] = 'cancel'; //取消订单按纽
						return $buttons;
					}else{
						$buttons[] = 'applyprice';
						$buttons[] = 'check'; //审核按纽
					}
				}
				break;
			case '1':
			case '2':
				//未开具结算单的
				if( $val['isSettled'] == '0' && $this->userType =='saleman' ) {
					$buttons[] = 'applyprice';
					$buttons[] = 'settlement'; //生成结算单按纽
				}
				break;
			case '4':
				$buttons[] = 'received';//确认收货按纽
				$buttons[] = 'express';	//查看物流按纽
				return $buttons;
			case '6':
				if( $val['commentState'] == '0' && $this->userType !='saleman' ){
					$buttons[] = 'comment';	//客户反馈按纽
				}
				$buttons[] = 'express';	//查看物流按纽
				if( $val['hasRefund'] != '2' ){
					$buttons[] = 'refund';	//退货按纽
				}
				return $buttons;
				break;
			case '7':
				$buttons[] = 'alreadyClose';	//订单已关闭，此按纽不需要加链接
				return $buttons;
				break;
		}

		$hasClose = in_array($val['orderId'],$applyClose)?true:false;
		$hasChange = in_array($val['orderId'],$applyChange)?true:false;

		if(  $val['state']<= '3' && !$hasClose  ){
			$buttons[] = 'cancel'; //取消订单按纽
		}

		if( $val['isSettled'] == '0' && $val['oType']!= tbOrder::TYPE_TAIL && $val['state'] < '2' && !$hasClose && !$hasChange ){
			$falg = tbDistribution::model()->exists( 'orderId=:orderId',array(':orderId'=> $val['orderId'] ) );
			if( !$falg ){
				$buttons[] = 'change';	//修改订单按纽
			}
		}

		//未支付
		if( $val['payState'] < 2 ){
			//预订订单，当未审核时，判断是否需要支付订金，当备货完成时，判断是否需要支付尾款。
			if( $val['oType'] == tborder::TYPE_BOOKING ){
				if( in_array( $val['state'], array('0','1') ) ){
					$depositModel = tbOrderDeposit::model()->findByPk( $val['orderId'] );
					if( $depositModel && $depositModel->amount>0 && $depositModel->payState!='1'  ){
						if( $this->userType =='saleman' ) {
							$buttons[] = 'changedeposit'; //修改订金按纽
						}
						$buttons[] = 'needpay';	//立即付款按纽
					}
				}else if( $val['payModel']!='1' && $val['state'] >= 2 ) {
					$buttons[] = 'needpay';	//立即付款按纽
				}
			}else{
				if( $val['payModel']!='1' ){
					$buttons[] = 'needpay';	//立即付款按纽
				}
			}
		}
		return $buttons;
	}

	/**
	* 订单列表tab 留货订单列表另外写
	*
	*/
	public function tabs( $type='' ){
		$tabs = array(
			'0'=>array('title'=>'所有订单','condition'=>array( 'isDel'=>'0' )),//,'orderType'=>array( 0,1,3 )
			'1'=>array('title'=>'待审核','condition'=>array( 'state'=>'0','orderType'=>array( 0,1,3 ),'is_string'=>' t.payModel != 0 ' )),
			'2'=>array('title'=>'备货中','condition'=>array( 'state'=>'1' )),
			'3'=>array('title'=>'备货完成','condition'=>array( 'state'=>'2' )),
			'4'=>array('title'=>'待发货','condition'=>array( 'state'=>'3' )),
			'5'=>array('title'=>'待确认收货','condition'=>array( 'state'=>'4' )),
			'6'=>array('title'=>'待结算','condition'=>array( 'isSettled'=>'0','state'=>array(1,2),'is_string'=>' t.orderType != 2 ' )),
			'7'=>array('title'=>'留货订单','condition'=>array( 'orderType'=>'2','isDel'=>'0','state'=>'0')),

			//'state'=>'0','orderType'=>array( 0,1,3 ) )
			//'6'=>array('title'=>'待结算','condition'=>array( 'payState'=>array(0,1) ))'payModel'=>'0','state'=>array(0,1,2)
			//'6'=>array('title'=>'待结算','condition'=>array( 'payModel'=>'0','state'=>array(0,1,2),'is_string'=>' t.orderType != 2 '
		);

		if( array_key_exists( $type,$tabs) ){
			return $tabs[$type];
		}else if( $type == ''){
			return $tabs;
		}
	}

	/**
	* 取得系统设置中配置的留货天数
	*/
	public static function getKeeyday(){
		return tbConfig::model()->get( 'order_keep_time' );;
	}

}
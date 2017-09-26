<?php
/**
 * 订单退货
 * @author liang
 * @version 0.1
 * @package CFormModel
 *
 */

class OrderRefund extends CFormModel {
	/**
	* 操作日志类别
	*/
	const OP_LOG_TYPE = 'refund';


	public $cause;

	public $num;

	private $_userType;
	private $_memberId;
	private $_orderModel;
	private $_model;

	public $singleNumber;
	public $positionId;
	public $postQuantity;
	public $batch;

	public function __construct( $memberId,$userType ) {
		$this->_userType =  $userType;
		$this->_memberId =  $memberId;
	}

	/**
	* Declares the validation rules.
	* The rules state that account and password are required,
	* and password needs to be authenticated.
	*/
	public function rules()	{
		return array(
			array('num,cause','required','on'=>'add'),
			array('cause','length','min'=>'6',"max"=>'200','on'=>'add'),
			array('num', "numerical",'min'=>'0.1',"integerOnly"=>false,'on'=>'add'),

			array('singleNumber,positionId,postQuantity,batch','required','on'=>'import'),
			array('positionId', "numerical",'min'=>'1',"integerOnly"=>true,'on'=>'import'),
			array('postQuantity', "numerical",'min'=>'0',"integerOnly"=>false,'on'=>'import'),
			array('cause,singleNumber,postQuantity', 'safe'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签
	*/
	public function attributeLabels() {
		return array(
			'num' => '退货数量',
			'cause'=> '退货理由',
			'singleNumber' => '产品编码',
			'positionId'=> '仓位信息',
			'postQuantity' => '入库数量',
			'batch'=> '批次',
		);
	}

	/**
	* 取得可退货的订单信息
	* @param integer $id 订单ID
	*/
	public function getOrder( $id ){
		if( empty( $this->_memberId ) ) return ;

		$condition = '1';
		$params = array();

		if( $this->_userType  != tbMember::UTYPE_SALEMAN  ){
			$condition .= ' and memberId =:memberId ';
			$params[':memberId'] = $this->_memberId;
		}

		$model = tbOrder::model()->findByPk( $id,$condition,$params );
		if( !$model ) return ;

		if( $this->_userType == tbMember::UTYPE_SALEMAN ){
			$isserve = tbMember::checkServe( $model->memberId,$this->_memberId  );
			if( !$isserve ){
				return ;
			}
		}

		$this->_orderModel = $model;
		return  $model;
	}


	/**
	* 生成退货单
	* @param array $dataArr 生成退货单提交的数据
	*/
	public function save( $dataArr ){
		$this->scenario = 'add';

		if( empty( $dataArr ) || !is_array( $dataArr ) || empty( $this->_orderModel ) ){
			$this->addError('num','退货产品不能为空');
			return false;
		}

		foreach ( $dataArr as $val ){
			$this->num = $val;
			if(!$this->validate()){
				return false;
			}
		}

		//新建退货单--要审核
		$saveData = $refundPays = array();

		foreach( $this->_orderModel->products as $val ){
			if( !array_key_exists( $val->orderProductId,$dataArr ) ) continue;

			if( $dataArr[$val->orderProductId] > $val->num ){
				$this->addError('num',$val->singleNumber.'的退货数量不能大于购买数量');
				return false;
			}

			//查找已申请退货的数量
			$num = $this->hasRefund( $val->orderProductId );
			if( bcadd( $dataArr[$val->orderProductId],$num,1 ) > $val->num ){
				$this->addError('num',$val->singleNumber.'已经申请过退货，总退货数量不能大于购买数量');
				return false;
			}

			$d = $val->getAttributes( array('orderId','orderProductId','productId','tailId','price','singleNumber','color') );
			if( $val->isSample =='1' ){
				$d['price'] = 0;
			}

			$d['num'] = $dataArr[$val->orderProductId];
			$saveData[] = $d;
			$refundPays[] = bcmul( $d['num'],$d['price'] ,2 );
		}

		if( empty( $saveData ) ){
			$this->addError('num','退货产品不能为空');
			return false;
		}

		$refund					= new tbOrderRefund();
		$refund->orderId		= $this->_orderModel->orderId;
		$refund->applyType		= ( $this->_userType == tbMember::UTYPE_SALEMAN )?1:0;
		$refund->realPayment	= array_sum( $refundPays );
		$refund->cause			= $this->cause;
		$refund->state			= 0;

		//使用事务处理
		$transaction = Yii::app()->db->beginTransaction();

		if( !$refund->save() ){
			$this->addErrors( $refund->getErrors() );
			return false;
		}

		$refundProduct			= new tbOrderRefundProduct();
		$refundProduct->refundId = $refund->refundId;
		foreach( $saveData as $val ){
			$_model = clone $refundProduct;
			$_model->attributes = $val;
			if( !$_model->save() ){
				$transaction->rollback(); //如果操作失败, 数据回滚
				$this->addErrors( $_model->getErrors() );
				return false;
			}
		}

		$remark = ( $this->_userType == tbMember::UTYPE_SALEMAN )?'业务员提交申请':'客户提交申请';
		if( !$this->refundOpLog( $refund->refundId,'insert',$remark,0 )){
			return false;
		}

		$transaction->commit();
		return true;
	}


	private function refundOpLog( $refundId,$code,$remark,$isManage ){
		//生成操作日志
		$oplog = new tbOpLog();
		$oplog->objType = self::OP_LOG_TYPE;
		$oplog->userId = $this->_memberId;
		$oplog->objId =  $refundId;
		$oplog->code = $code;
		$oplog->remark = $remark;
		$oplog->isManage = ($isManage)?1:0;
		if( !$oplog->save() ){
			$this->addErrors( $oplog->getErrors() );
			return false;
		}
		return true;
	}

	/**
	* 取得列表
	* @param array $condition 查询列表条件
	*/
	public function search( $condition = array(),$pageSize = 10 ){
		$criteria = new CDbCriteria;
		if(!empty($condition)){
			foreach ($condition as $key=>$val){
				if( is_null( $val ) || $val === '' ){
					continue ;
				}
				if( $key =='createTime1' ){
					$criteria->addCondition("t.createTime>='$val'");
				}else if( $key =='createTime2' ){
					$createTime2 = date("Y-m-d H:i:s",strtotime( $val )+86400 ) ; //包含选择的当天
					$criteria->addCondition("t.createTime<'$createTime2'");
				}else if( $key =='orderId' ){
					$criteria->compare('t.'.$key,$val,true);
				}else{
					$criteria->compare('t.'.$key,$val);
				}
			}
		}

		if(  $this->_userType == tbMember::UTYPE_SALEMAN ){
			$userId[] =  $this->_memberId ;
			if( tbConfig::model()->get( 'default_saleman_id' ) == $this->_memberId ){
				$userId[] = 0;
			}
			$userId = implode(',',$userId);
			$criteria->join .= 'left join {{order}} o on ( o.orderId = t.orderId ) left join {{member}}  m on (m.memberId = o.memberId and m.userId in ('.$userId.') )';
			$criteria->addCondition("m.memberId is not null ");
		}else if( $this->_userType == tbMember::UTYPE_MEMBER ){
			$criteria->addCondition( ' exists( select null from  {{order}} o where o.orderId = t.orderId and o.memberId = '.$this->_memberId.')');
		}

		$criteria->order = ' t.createTime desc ';
		$model = new CActiveDataProvider( 'tbOrderRefund', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));

		$return['list'] = array();
		$return['pages'] = $model->getPagination();
		$data = $model->getData();
		if( empty( $data ) ){
			return $return;
		}

		$order = new Order();
		$userIds = $productids = array();
		foreach ( $data as $val ){
			$list = array();
			$userIds[] = $val->order->userId;

			if( $this->_userType == tbMember::UTYPE_MEMBER ){
				$canPrint = false;
			}else{
				$canPrint = in_array( $val->state,array(1,2,3) )?true:false;
			}
			$list = array_merge($val->attributes,array(
													'orderCreateTime'=>$val->order->createTime,
													'userId'=>$val->order->userId,
													'memberId'=>$val->order->memberId,
													'canPrint'=>$canPrint
								));

			$list['products'] = array();
			foreach ( $val->products as $pval ){
				if( $pval->state == '0' ){
					$orderproduct = $pval->orderproduct;
					$list['products'][] = array_merge($pval->attributes,array(
													'title'=>$orderproduct->title,
													'mainPic'=>$orderproduct->mainPic,
													'serialNumber'=>$orderproduct->serialNumber,
											));
					$productids[] = $pval->productId;
				}
			}

			$list['member'] = $order->getMemberDetial( $val->order->memberId );
			$return['list'][] = $list;
		}

		$userIds = implode(',',array_unique( $userIds ));
		$memberPro = tbProfile::model()->findAll(
					array('select'=>'memberId,username',
						  'condition'=>'memberId in ('.$userIds.')',

					));
		foreach ( $memberPro as $val ){
			$salesmans[$val->memberId] = $val->username;
		}

		$units = tbProduct::model()->getUnitConversion( $productids );

		foreach ( $return['list'] as &$val){
			$val['salesman'] = (isset($salesmans[$val['userId']]))?$salesmans[$val['userId']]:'';

			foreach ( $val['products'] as &$pval ){
				$pval['unitName'] = (isset($units[$pval['productId']]['unit']))?$units[$pval['productId']]['unit']:'';
			}
		}
		return $return;
	}


	/**
	* 取得可退货的信息--查看退款单
	* @param integer $id 退款单ID
	* @param boolean $isAdmin 是否后台查看
	*/
	public function getOne( $id,$state = null, $isAdmin = false ){
		if( empty( $this->_memberId ) ) return ;

		$condition = '';
		if( !is_null( $state ) ){
			$condition = 'state = '.$state;
		}

		$model = tbOrderRefund::model()->findByPk( $id,$condition );
		if( !$model ) return ;

		$orderModel = $model->order;

		if( !$isAdmin ){
			if( $this->_userType == tbMember::UTYPE_SALEMAN ){
				$isserve = tbMember::checkServe( $orderModel->memberId,$this->_memberId  );
				if( !$isserve ){
					return ;
				}
			}else{
				if( $this->_memberId != $orderModel->memberId ){
					return ;
				}
			}
		}

		$this->_model = $model;

		$info = array_merge($model->attributes,array(
													'orderCreateTime'=>$orderModel->createTime,
													'userId'=>$orderModel->userId,
													'memberId'=>$orderModel->memberId,
													'address'=>$orderModel->address,
													'memo'=>$orderModel->memo,
								));

		$info['products'] = $productids = array();
		foreach ( $model->products as $pval ){
			if( $pval->state == '0' ){
				$p = $pval->getAttributes( array('productId','tailId','num','price','singleNumber','color') );

				$orderproduct = $pval->orderproduct;

				$p['isSample'] = ( $orderproduct->isSample )?'是':'否';
				$p['buynum'] = $orderproduct->num;
				$p['subprice'] = bcmul( $pval->num,$pval->price,2 );
				$info['products'][] = $p;
				$productids[] = $pval->productId;
			}
		}

		$orderClass = new Order();
		$info['orderType'] =  $orderClass->orderType( $orderModel->orderType );
		$info['deliveryMethod'] =  $orderClass->deliveryMethod( $orderModel->deliveryMethod );
		$info['payModel'] = '';
		if( $orderModel->payModel ){
			$payment = tbPayMent::model()->findByPk( $orderModel->payModel );
			if( $payment ){
				$info['payModel'] = $payment->paymentTitle;
			}
		}

		$info['member'] = $orderClass->getMemberDetial( $orderModel->memberId );
		$info['member']['salesman'] = tbUser::model()->getUserName( $orderModel->userId );

		$info['oplog'] = tbOpLog::model()->getOP( self::OP_LOG_TYPE,$model->refundId );

		return $info;
	}

	public function stateTitles(){
		// 客户提出申请－－业务员审核－－仓库收货－－生成退货结算单－－财务确认－－退货完成
		//  业务员提出申请－－仓库收货－－生成退货结算单－－财务确认－－退货完成
		$arr = array( '0'=>'待审核', '1'=>'待仓库收货', '2'=>'待财务确认', '3'=>'退货完成', '10'=>'已取消退货');
		return $arr;
	}

	/**
	* 审核退款单
	*/
	public function check( $isManage = false ){
		$state = Yii::app()->request->getPost('state');
		$cause = Yii::app()->request->getPost('cause');

		if( !in_array( $state, array('pass','nopass') )){
			$this->addError( 'state','审核结果只能是通过和不通过' );
			return false;
		}

		if( empty( $cause ) ){
			$this->addError( 'cause','请填写审核理由' );
			return false;
		}

		if( $state == 'pass' ){
			$this->_model->state = tbOrderRefund::STATE_WARRANT;
			$remark = '审核通过;';
		}else{
			$this->_model->state = tbOrderRefund::STATE_CANCLE;
			$remark = '审核不通过;';
		}

		//使用事务处理
		$transaction = Yii::app()->db->beginTransaction();

		if( !$this->_model->save() ){
			$transaction->rollback(); //如果操作失败, 数据回滚
			$this->addErrors( $this->_model->getErrors() );
			return false;
		}

		$remark .= $cause;
		if( !$this->refundOpLog( $this->_model->refundId,$state,$remark,$isManage ) ){
			return false;
		}

		if( $state == 'pass' ){
			$orderModel = $this->_model->order;
			$products = $orderModel->products;

			$allRefund = true; //全部退完
			foreach( $products as $val ){
				//查找已审核通过的退货的数量
				$num = $this->hasRefund( $val->orderProductId,true );
				if( $num < $val->num ){
					$allRefund = false;
					break;
				}
			}

			if( $allRefund ){
				$orderModel->hasRefund = 2;
				if( !$orderModel->save() ){
					$transaction->rollback(); //如果操作失败, 数据回滚
					$this->addErrors( $orderModel->getErrors() );
					return false;
				}
			}
		}
		$transaction->commit();
		return true;
	}


	/**
	* 计算总申请退货总数
	* @param integer $orderProductId
	* @param boolean $hasCheck 是否已经审核
	*/
	public function hasRefund( $orderProductId,$hasCheck = 'false' ){
		$c = new CDbCriteria;
		$c->select = ' sum( `num` ) as num ';
		$c->compare( 't.orderProductId', $orderProductId );
		$c->compare( 't.state', 0 );
		$r = tbOrderRefund::model()->tableName();
		if( $hasCheck ){
			$c->addCondition(" exists (select null from $r r where r.refundId = t.refundId and r.state not in( 0,10 ) )");
		}else{
			$c->addCondition(" exists (select null from $r r where r.refundId = t.refundId and r.state !=10 )");
		}

		$model = tbOrderRefundProduct::model()->find( $c );
		$num = ( $model->num >0 )?$model->num:0;
		return $num ;
	}


	/**
	* 已审核的退货信息
	*/
	public static function refunds( $orderId ){
		$c = new CDbCriteria;
		$c->compare( 't.orderId', $orderId );
		$c->compare( 't.state', 0 );
		$r = tbOrderRefund::model()->tableName();
		$c->addCondition(" exists (select null from $r r where r.refundId = t.refundId and r.state not in(0,1) )");
		$model = tbOrderRefundProduct::model()->findAll( $c );
		$result = array();
		foreach ( $model as $val ){
			$result[$val->orderProductId] = $val->attributes;
		}
		return $result ;
	}


	/**
	* 退货产品入库
	*
	*/
	public function import(){
		$this->scenario = 'import';

		$imports = Yii::app()->request->getPost('product');
		if( !is_array($imports) || empty ( $imports )){
			$this->addError( 'product','入库的产品不能为空' );
			return false;
		}

		$needImport = array();
		foreach ( $this->_model->products as $pval ){
			$needImport[$pval->singleNumber] = array( 'num'=>$pval->num,'color'=>$pval->color );
		}

		//计算单品的总入库数量，不能大于退货数量
		$nums = array();
		foreach ( $imports as &$_pval ){

			unset( $_pval['total'] );
			$this->attributes = $_pval;
			if(!$this->validate()){
				return false;
			}

			$single =  $_pval['singleNumber'];

			if( !array_key_exists( $single,$needImport ) ){
				$this->addError( 'singleNumber','退货产品中不存在'.$_pval['singleNumber'] );
				return false;
			}

			$_pval['color'] = $needImport[$single]['color'];
			$nums[$single][] = $_pval['postQuantity'];

			//判断仓位信息是否正确
			$position = tbWarehousePosition::model()->findByPk(  $_pval['positionId'],'state = 0' );
			if( !$position ){
				$this->addError( 'position',$_pval['singleNumber'].'选择的仓位信息不存在' );
				return false;
			}
		}

		foreach ( $needImport as $key=>&$val ){
			if( !isset( $nums[$key] ) ){
				$this->addError( 'product',$key.'退货入库数量不能为空' );
				return false;
			}

			$n = array_sum( $nums[$key] );
			if( $n > $val['num'] ){
				$this->addError( 'product',$key.'入库数量不能大于退货数量' );
				return false;
			}

			//实际退货数量以入库为准
			$val['num'] = $n ;
		}

		$refundPays = array();

		$transaction = Yii::app()->db->beginTransaction();

		// 生成入库单
		$OrderImport = new OrderImport();
		$source = tbWarehouseWarrant::FORM_REFUND;
		$OrderImport->warrant = array('postId'=>$this->_model->refundId,
						'warehouseId'=> 0 ,
						'source'=>$source);

		$details  = array();
		foreach ( $imports as $pval ){

			$record = new tbWarehouseWarrantDetail();
			$record->orderId = '0';
			$record->num = $pval['postQuantity'];
			$record->singleNumber = $pval['singleNumber'];
			$record->color = $pval['color'];
			$record->batch = $pval['batch'];
			$record->positionId = $pval['positionId'];
			array_push( $details, $record );
		}

		$OrderImport->details = $details;
		if( !$OrderImport->save() ) {
			$transaction->rollback(); //如果操作失败, 数据回滚

			$error = $OrderImport->getErrors();
			$this->addErrors( $error );
			return false;
		}

			foreach ( $this->_model->products as $pval ){
			if( $pval->num != $needImport[$pval->singleNumber]['num'] ){
				$pval->num = $needImport[$pval->singleNumber]['num'];
				if( !$pval->save() ) {
					$transaction->rollback(); //如果操作失败, 数据回滚
					$error = $pval->getErrors();
					$this->addErrors( $error );
					return false;
				}
			}

			$refundPays[] = bcmul( $pval->num ,$pval->price ,2 );
		}


		$this->_model->state = tbOrderRefund::STATE_CONFIRM;
		$this->_model->warrantId = $OrderImport->getWarrantId();
		$this->_model->realPayment	= array_sum( $refundPays );

		if( !$this->_model->save() ){
			$transaction->rollback(); //如果操作失败, 数据回滚
			$this->addErrors( $this->_model->getErrors() );
			return false;
		}


		if( !$this->refundOpLog( $this->_model->refundId,'import','仓库确认收货',true ) ){
			return false;
		}

		$transaction->commit();
		return true;
	}

	/**
	* 退货产品入库--确认--未分配的订单
	* @param integer $orderId 订单ID
	*
	*/
	public function importNo( $orderId  ){
		$refunds = tbOrderRefund::model()->findAll( 'orderId = :orderId and state = 1 ',array(':orderId'=>$orderId) );
		foreach ( $refunds as $val ){
			$val->state = 2;
			if( !$val->save() ){
				$transaction->rollback(); //如果操作失败, 数据回滚
				$this->addErrors( $val->getErrors() );
				return false;
			}
			if( !$this->refundOpLog( $val->refundId,'import','仓库确认',true ) ){
				return false;
			}
		}
		return true;
	}

	/**
	* 退款单--财务确认
	*/
	public function confirm( $isManage = false ){
		$confirmpay = Yii::app()->request->getPost('confirmpay');
		if( $confirmpay != '1' ){
			return false;
		}

		//使用事务处理
		$transaction = Yii::app()->db->beginTransaction();

		$this->_model->state = tbOrderRefund::STATE_OK;
		if( !$this->_model->save() ){
			$transaction->rollback(); //如果操作失败, 数据回滚
			$this->addErrors( $this->_model->getErrors() );
			return false;
		}


		//月结客户账单细增加1条退款记录
		$order = tbOrder::model()->findByPk( $this->_model->orderId );
		if( $order->payModel == '1' ){
			$creditDetail = new tbMemberCreditDetail();
			$creditDetail->isCheck = '1';
			$creditDetail->amount = -$this->_model->realPayment;
			$creditDetail->memberId =  $order->memberId;
			$creditDetail->orderId = $this->_model->orderId;
			$creditDetail->mark = '订单退货';
			if( !$creditDetail->save() ){
				$transaction->rollback(); //如果操作失败, 数据回滚
				$this->addErrors( $creditDetail->getErrors() );
				return false;
			}
		}

		if( !$this->refundOpLog( $this->_model->refundId,'confirmpay','财务确认',true ) ){
			return false;
		}

		$transaction->commit();
		return true;
	}

}
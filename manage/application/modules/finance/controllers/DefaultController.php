<?php
/**
 * 财务收款管理
 * @access 财务收款管理
 * @author liang
 * @package Controller
 * @version 0.1
 *
 */
class DefaultController extends Controller {

	/**
	 * @access 财务收款
	 */
	public function actionIndex() {
		$model = new ReceivablesForm();
		$data = $model->getMonthInfo();
		$data['memberName'] =  Yii::app()->request->getQuery('memberName');
		$this->render( 'index' ,$data );
	}

	/**
	 * @access 财务收款
	 */
	public function actionIndex2() {
		$model = new ReceivablesForm();
		$data = $model->getInfo();
		$data['memberName'] =  Yii::app()->request->getQuery('memberName');
		$this->render( 'index2' ,$data );
	}

	/**
	 * @access 收款记录查询
	 */
	public function actionList() {
		$model = new ReceivablesForm();
		$data = $model->getDepositRecords();

		$err =  $model->getErrors();
		if( !empty( $err ) ){
			$this->dealError( $err );
		}
		$data['excelUrl'] = $this->createUrl( 'list' ,array('memberId'=>$data['memberId'],'memberName'=>$data['memberName'],'t1'=>$data['t1'],'t2'=>$data['t2'],'type'=>'exportExcel'));
		$this->render( 'list' ,$data );
	}

	/**
	 * @access 待审核结算申请
	 */
	public function actionAuditapply() {
		$model = new ReceivablesForm();
		$data = $model->applyList( array('state'=>0) );
		$this->render( 'auditapply' ,$data );
	}

	/**
	 * @access 已审核结算申请
	 */
	public function actionApplylist() {
		$conditon['memberId'] = Yii::app()->request->getQuery('memberId');
		if( !is_numeric( $conditon['memberId'] ) || $conditon['memberId'] <= '0' ){
			$conditon['memberId'] = $memberName = '';
		}else{
			$memberName =  Yii::app()->request->getQuery('memberName');
		}
		$conditon['t1'] = Yii::app()->request->getQuery('t1');
		$conditon['t2'] = Yii::app()->request->getQuery('t2');
		$conditon['state'] = array(1,2);

		$model = new ReceivablesForm();
		$data = $model->applyList( $conditon );
		$data['memberName'] = $memberName;

		$this->render( 'applylist' ,array_merge( $data,$conditon) );
	}

	/**
	 * @access 结算申请审核
	 */
	public function actionCheckapply() {
		$id = Yii::app()->request->getQuery('id');
		$form = new ReceivablesForm();
		$data = $form->getApplyInfo( $id ,0 );
		if( !$data ){
			$this->redirect( $this->createUrl('auditapply') );
		}

		if( Yii::app()->request->isPostRequest ) {
			if( $form->checkApply() ) {
				if(!$url = urldecode(Yii::app()->request->getQuery('from'))){
					$url = $this->createUrl('auditapply');
				}
				$this->dealSuccess( $url );
			}else{
				$this->dealError( $form->getErrors() );
			}
		}
		$this->render( 'checkapply',$data );
	}

	/**
	 * @access  查看结算申请审核
	 */
	public function actionViewapply() {
		$id = Yii::app()->request->getQuery('id');
		$form = new ReceivablesForm();
		$data = $form->getApplyInfo( $id );
		if( !$data ){
			$this->redirect( $this->createUrl('applylist') );
		}
		$this->render( 'viewapply',$data );
	}

	/**
	 * @access 对账单
	 */
	public function actionAccountbill(){
		$model = new ReceivablesForm();
		$data = $model->billData();
		$error = $model->getErrors();
		if( !empty( $error ) ){
			$this->dealError( $error );
		}

		$data['excelUrl'] = $this->createUrl( 'accountbill' ,array('memberId'=>$data['memberId'],'memberName'=>$data['memberName'],'t1'=>$data['t1'],'t2'=>$data['t2'],'type'=>'exportExcel'));
		$this->render( 'accountbill' ,$data );
	}


	/**
	 * @access 新增收款记录
	 */
	public function actionAdd() {
		$form = new ReceivablesForm();
		$flag = $form->getPaymentModel();
		if( !$flag ){
			$this->redirect( $this->createUrl('index') );
		}

		if( Yii::app()->request->isPostRequest ) {
			$action = Yii::app()->request->getPost('action');
			if( in_array ( $action,array('add','apply')) ){
				if( $form->$action () ) {
					$url = Yii::app()->request->urlReferrer;
					$this->dealSuccess( $url );
				}else{
					$this->dealError( $form->getErrors() );
				}
			}
		}

		if( $form->type == '1' ){
			$this->add2( $form );
		}else{
			$this->add1( $form );
		}
	}

	private function add2( $form ){
		$model = $form->getOPModel();
		$detail = $form->getSettleList( $model->month );
		$model->month = date('Ym',strtotime($model->month) );
		$member = tbProfileDetail::model()->companyname( $model->memberId );

		$receipts = $form->receiptList( $model->month );
		$receipts['realPayment'] = $form->realPayment;
		$receipts['notReceive'] = bcsub( $form->realPayment,$receipts['totalReceipt'],2 );
		$receipts['isApply'] = $form->isApply( $model->month );


		$this->render('add2',array('month' => $model->month,'member' => $member ,'detail' => $detail,'receipts' => $receipts));
	}


	private function add1( $form ){
		$model = $form->getOPModel();
		$orderModel = $model->order;

		$order = new Order();
		$orderModel->orderType = $order->orderType( $orderModel->orderType );
		$member = $order->getMemberDetial( $orderModel->memberId );

		$member['salesman'] = tbUser::model()->getUserName( $orderModel->userId );
		$orderModel->deliveryMethod = $order->deliveryMethod( $orderModel->deliveryMethod );
		$payments = tbPayMent::model()->getPayMents();

		$orderModel->payModel = array_key_exists($orderModel->payModel,$payments)?$payments[$orderModel->payModel]['paymentTitle']:'未付款';

		if( $orderModel->warehouseId && $warehouse = tbWarehouseInfo::model()->findByPk( $orderModel->warehouseId ) ){
			$orderModel->warehouseId = $warehouse->title;
		}

		//结算单出单人
		if( $model->type == '1' ){
			$originator = tbUser::model()->getUserName( $model->originatorId );
		}else{
			$originator = tbProfile::model()->getMemberUserName( $model->originatorId );
			$originator .= '(业务员)';
		}

		$detail = array_map( function( $i ){ return $i->attributes;},$model->detail );

		$products = array();
		foreach ( $orderModel->products as $_product ){
			$products[$_product->orderProductId] = $_product->getAttributes( array('productId','singleNumber','mainPic','color','price','tailId','title') );
		}
		foreach ( $detail as &$val ){
			$val = array_merge( $val, $products[$val['orderProductId']] );
			if( $val['isSample']=='1' ){
				$val['subprice'] = 0;
				$val['isSample']= '是';
			}else{
				$val['subprice'] = bcmul( $val['num'],$val['price'],2 );
				$val['isSample']= '否';
			}
		}

		$receipts = $form->receiptList( $model->settlementId );
		$receipts['realPayment'] = $form->realPayment;
		$receipts['notReceive'] = bcsub( $form->realPayment,$receipts['totalReceipt'],2 );
		$receipts['isApply'] = $form->isApply( $model->settlementId );

		$this->render('add',array('model' => $model,'orderModel' => $orderModel,'member' => $member ,'originator' => $originator,'detail' => $detail,'receipts' => $receipts));
	}
}
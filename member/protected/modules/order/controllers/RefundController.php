<?php
/**
* 退货管理
*/
class RefundController extends Controller {

	public function init() {
		parent::init();
		$this->pageTitle .= ' 退货管理';
	}

	/**
	* 退货单列表
	* @access 退货单列表
	*/
	public function actionIndex() {
		$condition['state'] = Yii::app()->request->getQuery('state');
		if( $condition['state'] !== '' && is_numeric( $condition['state'] ) ){
			$condition['state'] = (int)$condition['state'];
		}else{
			$condition['state'] = null;
		}

		$condition['createTime1'] = Yii::app()->request->getQuery('createTime1');
		$condition['createTime2'] = Yii::app()->request->getQuery('createTime2');
		$condition['orderId'] = trim( Yii::app()->request->getQuery('orderId') );
		$condition['refundId'] = trim(Yii::app()->request->getQuery('refundId'));
		$OrderRefund = new OrderRefund( Yii::app()->user->id,Yii::app()->user->getState('usertype') );
		$data = $OrderRefund->search( $condition );
		$data['stateTitles'] =  $OrderRefund->stateTitles();

		$this->render('index',array_merge( $data,$condition ));
	}



	/**
	* 查看退货单
	* @access  查看退货单
	* @param integer id
	*/
	public function actionView(){
		$id = Yii::app()->request->getQuery('id');

		$OrderRefund = new OrderRefund( Yii::app()->user->id,Yii::app()->user->getState('usertype') );
		$data = $OrderRefund->getOne( $id );

		if( !$data ){
			$url = $this->createUrl('index');
			$this->redirect($url);
		}

		$this->render('view',$data );
	}

	/**
	* 审核退货单
	* @access  审核退货单
	* @param integer id
	*/
/* 	public function actionCheck(){
		$id = Yii::app()->request->getQuery('id');

		$OrderRefund = new OrderRefund( Yii::app()->user->id,Yii::app()->user->getState('usertype') );
		$data = $OrderRefund->getOne( $id,0 );

		if( !$data ){
			$url = $this->createUrl('index');
			$this->redirect($url);
		}

		if( Yii::app()->request->isPostRequest ){
			if( $OrderRefund->check() ) {
				$this->dealSuccess( $this->createUrl( 'index' ) );
			} else {
				$errors = $OrderRefund->getErrors();
				$this->dealError( $errors );
			}
		}

		$this->render('check',$data );
	} */

	/**
	* @access 申请退货单
	* @param string orderId 订单号
	*/
	public function actionAdd(){
		$id = Yii::app()->request->getQuery('id');

		$OrderRefund = new OrderRefund( Yii::app()->user->id,Yii::app()->user->getState('usertype') );
		$model = $OrderRefund->getOrder( $id );

		if( !$model ){
			$url = $this->createUrl('index');
			$this->redirect($url);
		}


		if( Yii::app()->request->isPostRequest ){
			$dataArr = Yii::app()->request->getPost('products');
			$OrderRefund->cause = Yii::app()->request->getPost('cause');
			if( $OrderRefund->save( $dataArr ) ) {
				$this->dealSuccess( $this->createUrl( '/order/default/index' ) );
			} else {
				$errors = $OrderRefund->getErrors();
				$this->dealError( $errors );
			}
		}

		$order = new Order();
		$orderType = $order->orderType( $model->orderType );
		$member = $order->getMemberDetial( $model->memberId );
		$model->deliveryMethod = $order->deliveryMethod( $model->deliveryMethod );

		$payModel = tbPayMent::model()->findByPk( $model->payModel );
		if( $payModel ){
			$model->payModel = $payModel->paymentTitle;
		}else{
			$model->payModel = '';
		}

		if( $model->warehouseId && $warehouse = tbWarehouseInfo::model()->findByPk( $model->warehouseId ) ){
			$model->warehouseId = $warehouse->title;
		}else{
			$model->warehouseId = '';
		}

		$products = array();
		foreach( $model->products as $pval){
			$hasRefund = $OrderRefund->hasRefund( $pval->orderProductId );
			$canRefund = bcsub ( $pval->num,$hasRefund ,1 );

			$total = ( $pval->isSample =='1' )?'0':bcmul( $pval['price'],$canRefund,2 );


			$products[] = array(
							'orderProductId'=>$pval->orderProductId,
							'color'=>$pval->color,
							'singleNumber'=>$pval->singleNumber,
							'isSample'=>$pval->isSample,
							'price'=>$pval->price,
							'num'=>$pval->num,
							'canRefund'=>$canRefund,
							'total'=>$total,
							);

		}

		$this->render('add',array('model' => $model,'member' => $member,'orderType' => $orderType,'products' => $products,'cause' => $OrderRefund->cause));
	}
	
	/**
	* @access 打印退货单
	*/
	public function actionPrint(){
		$id = Yii::app()->request->getQuery('id');
		$state = PrintPush::printRefund( $id,$msg );
		$json = new AjaxData( $state , $msg );
		echo $json->toJson();
		Yii::app()->end();
	}
}

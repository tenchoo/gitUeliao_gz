<?php
/**
* 结算单管理
* @access 结算单管理
* @author liang
* @package Controller
* @version 0.1
*/
class SettlementController extends Controller {


	/**
	* 结算单管理
	* @access 结算单管理
	*/
	public function actionIndex() {
		$condition['createTime1'] = Yii::app()->request->getQuery('createTime1');
		$condition['createTime2'] = Yii::app()->request->getQuery('createTime2');
		$condition['orderId'] = Yii::app()->request->getQuery('orderId');
		$OrderSettlement = new OrderSettlement();
		$data = $OrderSettlement->search( $condition );
		$this->render('index',array_merge( $data,$condition ));
	}

	/**
	* @access 生成结算单
	*/
	public function actionAdd(){
		$id = Yii::app()->request->getQuery('id');
		$model = tbOrder::model()->with('products','user')->findByPk( $id ,'t.state >0 and t.state !=7 and t.isSettled = 0');
		if( !$model ){
			throw new CHttpException(404,"the require obj has not exists.");
		}
		if( Yii::app()->request->isPostRequest ){
			$dataArr = Yii::app()->request->getPost('data');

			$model->deliveryMethod = $dataArr['deliveryMethod'];
			$model->payModel = $dataArr['payModel'];
			$model->freight = $dataArr['freight'];
			$model->memo = $dataArr['memo'];
			$model->address = $dataArr['address'];

			$OrderSettlement = new OrderSettlement();
			if( $OrderSettlement->save( $dataArr,$model ) ) {
				$url = Yii::app()->session['tourl'];
				if( empty($url) ){
					$url = $this->createUrl( 'index' ) ;
				}
				$this->dealSuccess( $url );
			} else {
				$errors = $OrderSettlement->getErrors();
				$this->dealError( $errors );
			}

		}else{
			Yii::app()->session['tourl'] = Yii::app()->request->urlReferrer;
		}

		$order = new Order();
		$model->orderType = $order->orderType( $model->orderType );
		$member = $order->getMemberDetial( $model->memberId );
		$member['salesman'] = isset( $model->user->username )?$model->user->username:'';
		$deliveryMethod = $order->deliveryMethod();
		$payModel = tbPayMent::model()->getPayMents( '0' );

		//判断当前用户是否月结用户
		$creditInfo = tbMemberCredit::creditInfo( $model->memberId );
		if( empty( $creditInfo ) && $model->payModel != '1' ){
			unset( $payModel['1'] );
		}

		if( $model->warehouseId && $warehouse = tbWarehouseInfo::model()->findByPk( $model->warehouseId ) ){
			$model->warehouseId = $warehouse->title;
		}

		$this->render('add',array('model' => $model,'member' => $member ,'deliveryMethod' => $deliveryMethod,'payModel' => $payModel));
	}

	/**
	* @access 打印结算单
	*/
	public function actionPrint(){
		$id = Yii::app()->request->getQuery('id');
		$state = PrintPush::printSettlement( $id,$msg,true );
		if( Yii::app()->request->getIsAjaxRequest() ) {
			$json = new AjaxData( $state , $msg );
			echo $json->toJson();
		}else{
			echo $msg; 
		}
		Yii::app()->end();
	}

	/**
	* 查看结算单
	* @access  查看结算单
	* @param integer id
	*/
	public function actionView(){
		$id = Yii::app()->request->getQuery('id');
		$model = tbOrderSettlement::model()->findByPk( $id );
		if( !$model ){
			throw new CHttpException(404,"the require obj has not exists.");
		}

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

		$this->render('view',array('model' => $model,'orderModel' => $orderModel,'member' => $member ,'originator' => $originator,'detail' => $detail));
	}

}

<?php
/**
 * 订单发货管理
 * @access 发货单管理
 * @author liang
 * @package Controller
 * @version 0.1
 *
 */
class DeliveryController extends Controller {

	/**
	 * 待发货订单,从订单表读数据
	 * @access 待发货订单
	 */
	public function actionIndex() {
		$condition['orderId'] = Yii::app()->request->getQuery('orderId');
		$condition['singleNumber'] = Yii::app()->request->getQuery('singleNumber');
		$condition['is_string'] = 't.state = 3 and ( t.isRecognition = 1 or t.payModel = 4 )';

		$Order = new Order();
		$pageSize = tbConfig::model()->get( 'page_size' );
		$data = $Order->search( $condition,'t.createTime DESC' ,$pageSize );

		$this->render('index',array('list' => $data['list'], 'pages' => $data['pages'], 'condition' => $condition));
	}


	/**
	* 发货
	* @access 发货
	* @param integer id 订单ID，针对订单发货
	*/
	public function actionDelivery(){
		$id = Yii::app()->request->getQuery('id');
		$model = tbOrder::model()->findByPk( $id ,'t.state = 3 and ( t.isRecognition = 1 or t.payModel = 4)');
		if( !$model ){
			$this->redirect( $this->createUrl( 'index' ) );
		}

		$scenario = '';
		if( $model->deliveryMethod == '2' ){
			//物流配送需要填写物流信息
			$scenario = 'logistics';
		}else if( $model->deliveryMethod == '1' ){
			$ladingCode = tbConfig::model()->get( 'order_ladingCode' );
			if( $ladingCode ){
				//需要提货码
				$creditInfo = tbMemberCredit::creditInfo( $model->memberId );
				if(  $model->payModel == '1' || !empty( $creditInfo ) ){
					$scenario = 'ladingCode';
				}
			}
		}

		if( Yii::app()->request->isPostRequest ){
			$dataArr = Yii::app()->request->getPost('data');
			$delivery = new Delivery( $scenario );
			$delivery->orderId = $model->orderId;
			if( $delivery->save( $dataArr,$model ) ) {
				$this->dealSuccess( $this->createUrl( 'index' ) );
			} else {
				$errors = $delivery->getErrors();
				$this->dealError( $errors );
			}
		}

		if( $model->warehouseId && $warehouse = tbWarehouseInfo::model()->findByPk( $model->warehouseId ) ){
			$model->warehouseId = $warehouse->title;
		}

		$productIds = array_map( function($i){return $i->productId;},$model->products);

		$logisticsList = tbLogistics::model()->getList();

		$classorder = new Order();
		$deliveryMethod = $classorder->deliveryMethod( $model->deliveryMethod );

		$units = tbProduct::model()->getUnitConversion( $productIds );
		$this->render('delivery',array( 'model' => $model ,'logisticsList' => $logisticsList,'deliveryMethod' => $deliveryMethod,'units' => $units,'scenario' => $scenario));
	}

	/**
	* 发送提货码
	* @access 发送提货码
	* @param integer id 订单ID，针对订单发货
	*/
	public function actionDeliverycode(){
		$orderId = Yii::app()->request->getQuery('id');
		$msg = '发送成功';
		$state = OrderSms::deliveryCode( $orderId,$msg );
		$json=new AjaxData( true,$msg );
		echo $json->toJson();
		Yii::app()->end();
	}


	/**
	 * 发货单管理,从发货单表读数据
	 * @access 发货单管理
	 */
	public function actionList() {
		$condition['deliveryId'] = Yii::app()->request->getQuery('deliveryId');
		$condition['orderId'] = Yii::app()->request->getQuery('orderId');
		$condition['singleNumber'] = Yii::app()->request->getQuery('singleNumber');

		$Delivery = new Delivery();
		$data = $Delivery ->search( $condition );
		$this->render('list',array('list' => $data['list'], 'pages' => $data['pages'], 'condition' => $condition));
	}


	/**
	* 查看发货单
	* @access 查看发货单
	* @param integer id 发货单ID
	*/
	public function actionView(){
		$id = Yii::app()->request->getQuery('id');
		$delivery = new tbDelivery();
		$model = $delivery->with('operator','order','order.products')->findByPk( $id );
		if( !$model ){
			$this->redirect( $this->createUrl( 'list' ) );
		}

		$data = call_user_func( array( new Delivery(),'setList'),$model ) ;
		$data['member']  = call_user_func( array( new Order(),'getMemberDetial'),$model->order->memberId ) ;
		$data['memo']  = $model->order->memo;

		$data['logistics'] =  $delivery->getLogisticsInfo( $model->logistics,$model->logisticsNo,$model->address );
		$this->render('view',$data);
	}
	
	
}
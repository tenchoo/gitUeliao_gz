<?php
/**
 * 待分配订单
 * @access 待分配订单
 * @author liang
 * @package Controller
 * @version 0.1
 *
 */
class DistributionController extends Controller {

	/**
	 * @access 待分配分拣订单
	 */
	public function actionIndex() {
		$this->showlist( '0' );
	}

	/**

	 * @access 已分配分拣订单
	 */
	public function actionList() {
		$this->showlist( '1' );
	}

	private function showlist( $state ){
		$condition = array( 'state'=>$state);
		$condition['orderId'] = Yii::app()->request->getQuery('orderId');
		$condition['singleNumber'] = Yii::app()->request->getQuery('singleNumber');

		$model = new DistributionForm();
		$data = $model->search( $condition );
		$this->render('index',array_merge($data,$condition));
	}

	/**
	 * @access 订单分配
	 * @throws CHttpException
	 */
	public function actionDistribution(){
		$id = Yii::app()->request->getQuery('id');
		$model = tbOrderDistribution::model()->with('order','order.user')->findByPk( $id,'t.state=0 ');
		if( !$model || empty($model->order) ){
			throw new CHttpException(404,"the require obj has not exists.");
		}

		if( $model->order->state == '7' ){
			$flag = tbOrderDistribution::model()->cancleOrder(  $model->order->orderId );
			$this->redirect( $this->createUrl( 'index' ) );
			return;
		}

		$applycolse = tbOrderApplyclose::model()->hasApply( $model->orderId );

		//退货信息
		//$refunds = OrderRefund::refunds(  $model->orderId );
		$refunds = array();

		$isClose = false;
		$unitRate = array();
		//取得单位和单位换算率
		$productIds =  array_map( function ( $i ){ return $i->productId;},$model->order->products);
		$pInfo =  tbProduct::model()->getUnitConversion( $productIds );
		foreach ( $model->order->products as $val){
			$unitRate[$val->orderProductId] = array_key_exists($val->productId,$pInfo)? $pInfo[$val->productId]['unitConversion']:'';
			/* if(  array_key_exists( $val->orderProductId,$refunds) ){
				$refunds[$val->orderProductId]['residualNum'] = bcsub($val->num,$refunds[$val->orderProductId]['num'],1);
				if( $refunds[$val->orderProductId]['residualNum'] > 0 ){
					$isClose = false;
				}
			}else{
				$isClose = false;
			} */
		}

		if( Yii::app()->request->isPostRequest && !$applycolse ){
			$DistributionForm = new DistributionForm();
			if( $isClose ){
				if( $DistributionForm->closeDistribution( $model ) ) {
					$this->dealSuccess( $this->createUrl( 'index' ) );
				} else {
					$errors = $DistributionForm->getErrors();
					$this->dealError( $errors );
				}
			}else{

				$DistributionForm->deliveryWarehouseId  = Yii::app()->request->getPost('warehouseId');
				$DistributionForm->packinger  = Yii::app()->request->getPost('packinger');
				$unitRate = Yii::app()->request->getPost('unitRate');
				$dataArr = Yii::app()->request->getPost('data');
				if( $DistributionForm->save( $dataArr,$model,$unitRate) ) {
					$this->dealSuccess( $this->createUrl( 'index' ) );
				} else {
					$errors = $DistributionForm->getErrors();
					$this->dealError( $errors );
				}
			}
		}else{
			$dataArr = array();
		}

		$salesman = isset( $model->order->user->username )?$model->order->user->username:'';
		$order = new Order();
		$model->order->orderType = $order->orderType( $model->order->orderType );
		$model->order->deliveryMethod = $order->deliveryMethod( $model->order->deliveryMethod );
		$warehouse = tbWarehouseInfo::model()->getAll();


		$this->render('distribution',
					array(
						'model' => $model->order,
						'salesman'=>$salesman,
						'warehouse'=>$warehouse,
						'dataArr'=>$dataArr,
						'unitRate'=>$unitRate,
						'productUnits'=>Yii::app()->session['productUnits'],
						'applycolse'=>$applycolse,
						'refunds'=>$refunds,
						'isClose'=>$isClose,
					));
	}

	/**
	* 查看分配分拣
	* @access 查看分配分拣
	* @param integer id 分配分拣单ID
	*/
	public function actionView(){
		$id = Yii::app()->request->getQuery('id');
		$model = tbDistribution::model()->with('operator','order','order.user')->find( 't.orderId = :id ',array(':id'=>$id));
		if( !$model || empty($model->order) ){
			throw new CHttpException(404,"the require obj has not exists.");
		}

		$data['operator'] = !empty($model->operator)?$model->operator->username:'';
		$data['distributionTime'] = $model->createTime;
		$data['salesman'] = isset( $model->order->user->username )?$model->order->user->username:'';
		$data['orderTime'] = $model->order->createTime;
		$data['orderId'] = $model->orderId;
		$data['address'] = $model->order->address;
		$data['name'] = $model->order->name;
		$data['tel'] = $model->order->tel;
		$data['memo'] = $model->order->memo;

		$order = new Order();
		$data['orderType'] = $order->orderType( $model->order->orderType );
		$data['deliveryMethod'] = $order->deliveryMethod( $model->order->deliveryMethod );

		$warehouse = tbWarehouseInfo::model()->getAll();

		$data['deliveryWarehouse'] = isset($warehouse[$model->deliveryWarehouseId])?$warehouse[$model->deliveryWarehouseId]:'';
		foreach( $model->order->products as $val ){
			$products[$val->orderProductId] = $val->getAttributes(array('singleNumber','color','num'));
		}

		$packinger = array();
		foreach( $model->detail as $val ){
			$w = array_key_exists($val->warehouseId,$warehouse)?$warehouse[$val->warehouseId]:'';

			if( !array_key_exists( $val->packingerId,$packinger )){
				$packinger[$val->packingerId] = tbUser::model()->getUsername( $val->packingerId );
			}

			$detail = array( 'warehouse'=>$w,
							 'productBatch'=>$val->productBatch,
							 'distributionNum'=>$val->distributionNum,
							 'positionTitle'=>$val->positionTitle,
							 'packinger'=>$packinger[$val->packingerId],
					);
			$products[$val->orderProductId]['detail'][] = $detail;
		}

		$data['products'] = $products;
		$this->render('view',$data);
	}

	/**
	* @access 分拣员信息
	* @param integer warehouseId 所属仓库ID
	* @param integer username 分拣员名字查询
	*/
	public function actionPackingerlist(){
		$id = Yii::app()->request->getQuery('id');


	}
}
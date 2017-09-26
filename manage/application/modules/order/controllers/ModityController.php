<?php
/**
* 取消订单/修改订单管理
* @access 修改订单管理
*/
class ModityController extends Controller {

	/**
	* 取消订单列表页
	* @access 取消订单列表页
	*/
	public function actionIndex() {
		$condition['orderId'] = trim(Yii::app()->request->getQuery('orderId'));
		$condition['createTime1'] = Yii::app()->request->getQuery('createTime1');
		$condition['createTime2'] = Yii::app()->request->getQuery('createTime2');

		$data = OrderClose::search( $condition );
		$this->render( 'index',array_merge($data,$condition) );
	}

	/**
	* 修改订单列表页
	* @access 修改订单列表页
	*/
	public function actionChangelist() {
		$condition['orderId'] = trim(Yii::app()->request->getQuery('orderId'));
		$condition['createTime1'] = Yii::app()->request->getQuery('createTime1');
		$condition['createTime2'] = Yii::app()->request->getQuery('createTime2');

		$data = OrderChange::search( $condition );
		$this->render( 'changelist',array_merge($data,$condition) );
	}


	/**
	* 取消订单的审核
	 * @access 取消订单的审核
	*/
	public function actionCheckclose(){
		$id = Yii::app()->request->getQuery('id');

		$criteria = new CDbCriteria;
		$criteria->compare('t.id',$id);
		$criteria->compare('t.state','0');
		$criteria->join = 'inner join {{order}}  a on (t.orderId = a.orderId )';
		$criteria->order = 'createTime desc';
		$criteria->addCondition("t.state !='7'");

		$applyClose = tbOrderApplyclose::model()->find( $criteria );
		if( !$applyClose ){
			throw new CHttpException(404,"the require obj has not exists.");
		}

		$model = tbOrder::model()->with('products','batches','user')->findByPk( $applyClose->orderId );

		if( Yii::app()->request->isPostRequest ){
			$OrderClose = new OrderClose();
			$OrderClose->attributes =  Yii::app()->request->getPost('data');
			if( $OrderClose->check( $applyClose,$model ) ) {
				$this->dealSuccess( $this->createUrl( 'index' ) );
			} else {
				$this->dealError( $OrderClose->errors );
			}
		}

		$order = new Order();
		$model->orderType = $order->orderType( $model->orderType );
		$member = $order->getMemberDetial( $model->memberId );
		$member['salesman'] = isset( $model->user->username )?$model->user->username:'';
		$model->deliveryMethod = $order->deliveryMethod( $model->deliveryMethod );
		$payments = tbPayMent::model()->getPayMents();
		$model->payModel = isset($payments[$model->payModel])?$payments[$model->payModel]['paymentTitle']:"";

		$this->render('checkclose',array('model' => $model,'member' => $member ));
	}

	/**
	* 取消订单详情页
	 * @access 取消订单详情页
	* @param string orderId 订单号
	*/
	public function actionView(){
		$id = Yii::app()->request->getQuery('id');
		$applyClose = tbOrderApplyclose::model()->find( array(
							'select'=>'state,remark,orderId',
							'condition'=>'id = '.$id,
							'order'=>'createTime desc',
						));

		if( !$applyClose ){
			throw new CHttpException(404,"the require obj has not exists.");
		}

		$applyinfo = $applyClose->getAttributes(array('state','remark'));
		$model = tbOrder::model()->with('products','batches','user')->findByPk( $applyClose->orderId  );

		if( !$model ){
			throw new CHttpException(404,"the require obj has not exists.");
		}

		$order = new Order();
		$model->orderType = $order->orderType( $model->orderType );
		$model->deliveryMethod = $order->deliveryMethod( $model->deliveryMethod );
		$payments = tbPayMent::model()->getPayMents();
		$model->payModel = isset($payments[$model->payModel])?$payments[$model->payModel]['paymentTitle']:"";

		$member = $order->getMemberDetial( $model->memberId );
		$member['salesman'] = ( $model->user )?$model->user->username:'';

		$this->render('view',array('model' => $model,'member' => $member ,'applyinfo'=>$applyinfo));
	}


	/**
	* 修改订单的审核
	 * @access 修改订单的审核
	*/
	public function actionCheckchange(){
		$id = Yii::app()->request->getQuery('id');

		$criteria = new CDbCriteria;
		$criteria->compare('t.orderId',$id);
		$criteria->compare('t.state','0');
		$criteria->join = 'inner join {{order}}  a on (t.orderId = a.orderId )';
		$criteria->addCondition("t.state !='7'");
		$applyChange = tbOrderApplychange::model()->with('detail')->find( $criteria );
		if( !$applyChange ){
			throw new CHttpException(404,"the require obj has not exists.");
		}

		$model = tbOrder::model()->with('products','batches','user')->findByPk( $id );

		if( Yii::app()->request->isPostRequest ){
			$OrderChange = new OrderChange();
			$dataArr = Yii::app()->request->getPost('data');
			if( $OrderChange->check( $dataArr,$applyChange,$model ) ) {
				$this->dealSuccess( $this->createUrl( 'changelist' ) );
			} else {
				$this->dealError( $OrderChange->errors );
			}
		}else{
			$dataArr = array('address'=>'','meno'=>'','checkInfo'=>'','freight'=>$model->freight,'address'=>$model->address,'memo'=>$model->memo,'products'=>array());

			foreach ( $applyChange->detail as $val){
				$dataArr['products'][$val->orderProductId]['changeNum'] = $val->applyNum;
				$dataArr['products'][$val->orderProductId]['remark'] = $val->remark;
			}
		}

		$order = new Order();
		$model->orderType = $order->orderType( $model->orderType );
		$member = $order->getMemberDetial( $model->memberId );
		$member['salesman'] = isset( $model->user->username )?$model->user->username:'';
		$model->deliveryMethod = $order->deliveryMethod( $model->deliveryMethod );
		$payments = tbPayMent::model()->getPayMents();
		$model->payModel = isset($payments[$model->payModel])?$payments[$model->payModel]['paymentTitle']:"";

		foreach ( $applyChange->detail as $val ){
			$applyinfo[$val->orderProductId] = $val->attributes;
		}

		if(!isset($dataArr['state'])){
			$dataArr['state'] = '';
		}
		$this->render('checkchange',array('model' => $model,'member' => $member,'data' => $dataArr,'applyinfo' => $applyinfo ));
	}


	/**
	* 修改订单详情页
	 * @access 修改订单详情页
	* @param string orderId 订单号
	*/
	public function actionCview(){
		$id = Yii::app()->request->getQuery('id');		
		$model = tbOrder::model()->with('products','batches','user')->findByPk( $id );

		if( !$model ){
			throw new CHttpException(404,"the require obj has not exists.");
		}

		//查找订单修改记录
		$changeModel = tbOrderApplychange::model()->with('detail')->findByAttributes( array('orderId'=>$id) );

		if( !$changeModel ){
			throw new CHttpException(404,"the require obj has not exists.");
		}

		$applyinfo = $changeModel->getAttributes(array('state','checkInfo'));
		foreach ( $changeModel->detail as $val ){
			$applyinfo['detail'][$val->orderProductId] = $val->attributes;
		}

		$order = new Order();
		$model->orderType = $order->orderType( $model->orderType );
		$model->deliveryMethod = $order->deliveryMethod( $model->deliveryMethod );
		$payments = tbPayMent::model()->getPayMents();
		$model->payModel = isset($payments[$model->payModel])?$payments[$model->payModel]['paymentTitle']:"";

		$member = $order->getMemberDetial( $model->memberId );
		$member['salesman'] = ( $model->user )?$model->user->username:'';

		$this->render('cview',array('model' => $model,'member' => $member ,'applyinfo'=>$applyinfo));
	}
}
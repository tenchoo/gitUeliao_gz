<?php
/**
* 取消订单/修改订单管理
*
*/
class ModityController extends Controller {

	public $userType;

	public function init(){
		parent::init();
		$this->userType =  Yii::app()->user->getState('usertype');
	}

	/**
	* 取消订单列表页
	* @access 取消订单列表页
	*/
	public function actionIndex() {
		$condition['orderId'] = trim(Yii::app()->request->getQuery('orderId'));
		$condition['createTime1'] = Yii::app()->request->getQuery('createTime1');
		$condition['createTime2'] = Yii::app()->request->getQuery('createTime2');


		if( $this->userType != tbMember::UTYPE_SALEMAN ){
			$condition['memberId'] = Yii::app()->user->id;
		}

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
	* 修改订单,订单只允许修改一次。
	*/
	public function actionChange(){
		$id = Yii::app()->request->getQuery('id');

		$exists = tbOrderApplyclose::model()->hasApply( $id );
		if( $exists ){
			$this->dealSuccess( $this->createUrl( 'view',array('id'=>$id ) ) );
		}

		$exists = tbOrderApplychange::model()->exists( 'orderId = :id',array(':id'=>$id) );
		if( $exists ){
			$this->dealSuccess( $this->createUrl( 'cview',array('id'=>$id ) ) );
		}

		$condition = 't.orderType !=:type ';
		$param = array(':type'=>tbOrder::TYPE_KEEP);
		if( $this->userType != 'saleman' ){
			$condition .= ' and t.memberId= :memberId';
			$param[':memberId'] = Yii::app()->user->id;
		}

		$model = tbOrder::model()->with('products','batches')->findByPk( $id ,$condition ,$param);
		//尾货订单不允许修改
		if( !$model || $model->orderType == tbOrder::TYPE_TAIL  ){
			$url = $this->createUrl('changelist');
			$this->redirect($url);
			//throw new CHttpException(404,"the require obj has not exists.");
		}

		$this->isServe( $model->memberId );

		$applyinfo = null;
		if( Yii::app()->request->isPostRequest ){
			$OrderChange = new OrderChange();
			$data =  Yii::app()->request->getPost('data');
			if( $OrderChange->change( $data,$model ) ) {
				$url = Yii::app()->session['tourl'];
				if( empty($url) ){
					$url = $this->createUrl( 'changelist' ) ;
				}
				Yii::app()->session['tourl'] = null;
				$this->dealSuccess( $url );
			} else {
				$this->dealError( $OrderChange->errors );
			}
		}else{
			Yii::app()->session['tourl'] = Yii::app()->request->urlReferrer;
		}

		$order = new Order();
		$model->orderType = $order->orderType( $model->orderType );
		$model->deliveryMethod = $order->deliveryMethod( $model->deliveryMethod );
		$payments = tbPayMent::model()->getPayMents();
		$model->payModel = isset($payments[$model->payModel])?$payments[$model->payModel]['paymentTitle']:"";

		$member = $order->getMemberDetial( $model->memberId );

		$this->render('change',array('model' => $model,'member' => $member ,'applyinfo'=>$applyinfo));
	}


	/**
	* 取消订单的审核
	*/
	public function actionCheckclose(){
		$this->checkSaleman();

		$id = Yii::app()->request->getQuery('id');

		$criteria = new CDbCriteria;
		$criteria->compare('t.id',$id);
		$criteria->compare('t.state','0');
		$criteria->join = 'inner join {{order}}  a on (t.orderId = a.orderId )';
		$criteria->order = 'createTime desc';
		$criteria->addCondition("a.state !='7'");
		$applyClose = tbOrderApplyclose::model()->find( $criteria );
		if( !$applyClose ){
			$url = $this->createUrl('index');
			$this->redirect($url);
			//throw new CHttpException(404,"the require obj has not exists.");
		}

		$model = tbOrder::model()->with('products','batches')->findByPk( $applyClose->orderId );

		$this->isServe( $model->memberId );

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
		$model->deliveryMethod = $order->deliveryMethod( $model->deliveryMethod );
		$payments = tbPayMent::model()->getPayMents();
		$model->payModel = isset($payments[$model->payModel])?$payments[$model->payModel]['paymentTitle']:"";

		$this->render('checkclose',array('model' => $model,'member' => $member ));
	}

	private function checkSaleman(){
		if($this->userType!='saleman'){
			throw new CHttpException(403,"You do not have permission .");
		}
	}

	private function isServe( $memberId ){
		if( $this->userType == tbMember::UTYPE_SALEMAN ){
			$isserve = tbMember::checkServe( $memberId,Yii::app()->user->id );
			if( !$isserve ){
				throw new CHttpException(403,"You do not have permission to view this page.");
			}
		}
	}

	/**
	* 取消订单详情页
	* @param string orderId 订单号
	*/
	public function actionView(){
		$id = Yii::app()->request->getQuery('id');

		$condition = ($this->userType == 'saleman')?'':' t.memberId = '.Yii::app()->user->id;
		$model = tbOrder::model()->with('products','batches')->findByPk( $id ,$condition );

		if( !$model ){
			$url = $this->createUrl('index');
			$this->redirect($url);
			// throw new CHttpException(404,"the require obj has not exists.");
		}

		$this->isServe( $model->memberId );

		//查找订单申请审核记录
		$applyinfo = null;
		if( $model->state!=7 ){
			$applyClose = tbOrderApplyclose::model()->find( array(
							'select'=>'state,remark',
							'condition'=>'orderId = '.$model->orderId,
							'order'=>'createTime desc',
						));

			if( !$applyClose ){
				$url = $this->createUrl('changelist');
				$this->redirect($url);
				//throw new CHttpException(404,"the require obj has not exists.");
			}

			$applyinfo = $applyClose->getAttributes(array('state','remark'));
		}

		$order = new Order();
		$model->orderType = $order->orderType( $model->orderType );
		$model->deliveryMethod = $order->deliveryMethod( $model->deliveryMethod );
		$payments = tbPayMent::model()->getPayMents();
		$model->payModel = isset($payments[$model->payModel])?$payments[$model->payModel]['paymentTitle']:"";

		$member = $order->getMemberDetial( $model->memberId );

		$this->render('view',array('model' => $model,'member' => $member ,'applyinfo'=>$applyinfo));
	}


	/**
	* 修改订单的审核
	*/
	public function actionCheckchange(){
		$this->checkSaleman();

		$id = Yii::app()->request->getQuery('id');

		$criteria = new CDbCriteria;
		$criteria->compare('t.orderId',$id);
		$criteria->compare('t.state','0');
		$criteria->join = 'inner join {{order}}  a on (t.orderId = a.orderId )';
		$criteria->addCondition("a.state !='7'");
		$applyChange = tbOrderApplychange::model()->with('detail')->find( $criteria );
		if( !$applyChange ){
			$url = $this->createUrl('changelist');
			$this->redirect($url);
			// throw new CHttpException(404,"the require obj has not exists.");
		}

		$model = tbOrder::model()->with('products','batches')->findByPk( $id );

		$this->isServe( $model->memberId );

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
	* @param string orderId 订单号
	*/
	public function actionCview(){
		$id = Yii::app()->request->getQuery('id');
		$condition = ($this->userType == 'saleman')?'':' t.memberId = '.Yii::app()->user->id;
		$model = tbOrder::model()->with('products','batches')->findByPk( $id ,$condition );

		if( !$model ){
			$url = $this->createUrl('changelist');
			$this->redirect($url);
			//throw new CHttpException(404,"the require obj has not exists.");
		}

		$this->isServe( $model->memberId );

		//查找订单修改记录
		$changeModel = tbOrderApplychange::model()->with('detail')->findByAttributes( array('orderId'=>$id) );

		if( !$changeModel ){
			$url = $this->createUrl('changelist');
			$this->redirect($url);
			//throw new CHttpException(404,"the require obj has not exists.");
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

		$this->render('cview',array('model' => $model,'member' => $member ,'applyinfo'=>$applyinfo));
	}
}
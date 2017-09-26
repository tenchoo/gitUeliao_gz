<?php
/**
* 价格申请管理
* @access 价格申请管理
* @author liang
* @package Controller
* @version 0.1
*/
class ApplypriceController extends Controller {


	/**
	* 价格申请管理
	* @access 价格申请管理
	*/
	public function actionIndex() {
		$condition['createTime1'] = Yii::app()->request->getQuery('createTime1');
		$condition['createTime2'] = Yii::app()->request->getQuery('createTime2');
		$condition['orderId'] = Yii::app()->request->getQuery('orderId');
		$data = tbOrderApplyprice::model()->search( $condition );
		$this->render('index',array_merge( $data,$condition ));
	}

	/**
	* @access 价格审核
	*/
	public function actionCheck(){
		$id = Yii::app()->request->getQuery('id');
		$apply = tbOrderApplyprice::model()->findByPk( $id,'state = 0' );
		if( !$apply ){
			throw new CHttpException(404,"the require obj has not exists.");
		}

		$model = tbOrder::model()->with('products','user')->findByPk( $apply->orderId );
		if( !$model ){
			throw new CHttpException(404,"the require obj has not exists.");
		}
		if( Yii::app()->request->isPostRequest ){
			$dataArr = Yii::app()->request->getPost('data');
			$OrderApplyPrice = new OrderApplyPrice();
			$OrderApplyPrice->state = Yii::app()->request->getPost('state');
			if( $OrderApplyPrice->check( $dataArr,$model,$apply ) ) {
				$this->dealSuccess( $this->createUrl( 'index' ) );
			} else {
				$errors = $OrderApplyPrice->getErrors();
				$this->dealError( $errors );
			}
		}
		$order = new Order();
		$model->orderType = $order->orderType( $model->orderType );
		$model->deliveryMethod = $order->deliveryMethod( $model->deliveryMethod );
		$member = $order->getMemberDetial( $model->memberId );
		$member['salesman'] = isset( $model->user->username )?$model->user->username:'';
		if( $model->payModel ){
			$payModel = tbPayMent::model()->findByPk( $model->payModel );
		}
		$model->payModel = (isset($payModel)&&$payModel)?$payModel->paymentTitle:'';
		$this->render('check',array('model' => $model,'member' => $member,'applyPrice'=>unserialize($apply->prices) ));
	}


	/**
	* @access 查看价格申请
	*/
	public function actionView(){
		$id = Yii::app()->request->getQuery('id');
		$apply = tbOrderApplyprice::model()->findByPk( $id );
		if( !$apply ){
			throw new CHttpException(404,"the require obj has not exists.");
		}

		$model = tbOrder::model()->with('products','user')->findByPk( $apply->orderId );
		if( !$model ){
			throw new CHttpException(404,"the require obj has not exists.");
		}

		$order = new Order();
		$model->orderType = $order->orderType( $model->orderType );
		$model->deliveryMethod = $order->deliveryMethod( $model->deliveryMethod );
		$member = $order->getMemberDetial( $model->memberId );
		$member['salesman'] = isset( $model->user->username )?$model->user->username:'';
		if( $model->payModel ){
			$payModel = tbPayMent::model()->findByPk( $model->payModel );
		}
		$model->payModel = (isset($payModel)&&$payModel)?$payModel->paymentTitle:'';

		$applyInfo = $apply->attributes;

		//申请人
		if( $apply->applyType == '1' ){
			$originator = tbUser::model()->getUserName( $apply->originatorId );
		}else{
			$originator = tbProfile::model()->getMemberUserName( $apply->originatorId );
			$originator .= '(业务员)';
		}

		//0未审，1审核通过，2审核不通过,3删除
		$arr = array('0'=>'待审核','1'=>'审核通过','2'=>'审核不通过','3'=>'已删除');
		$applyInfo['state'] = array_key_exists( $apply->state,$arr)?$arr[$apply->state]:$apply->state;
		$applyInfo['checkUser'] = tbUser::model()->getUserName( $apply->checkUserId );
		$applyInfo['originator'] = $originator;
		$this->render('view',array('model' => $model,'member' => $member,'applyInfo' =>$applyInfo ,'applyPrice'=>unserialize($apply->prices) ));
	}


	/**
	* @access 价格申请
	* @ param string orderId 订单号
	*/
	public function actionApply(){
		$id = Yii::app()->request->getQuery('id');
		$condition = '' ;
		$model = tbOrder::model()->with('products','user')->findByPk( $id ,$condition);
		if( !$model ){
			throw new CHttpException(404,"the require obj has not exists.");
		}
		if( Yii::app()->request->isPostRequest ){
			$dataArr = Yii::app()->request->getPost('data');
			$OrderApplyPrice = new OrderApplyPrice();

			if( $OrderApplyPrice->save( $dataArr,$model->orderId ,'1' ) ) {
				$url = Yii::app()->session['tourl'];
				if( empty($url) ){
					$url = $this->createUrl( 'index' ) ;
				}
				$this->dealSuccess( $url );
			} else {
				$errors = $OrderApplyPrice->getErrors();
				$this->dealError( $errors );
			}
		}else{
			Yii::app()->session['tourl'] = Yii::app()->request->urlReferrer;
		}
		$order = new Order();
		$model->orderType = $order->orderType( $model->orderType );
		$model->deliveryMethod = $order->deliveryMethod( $model->deliveryMethod );
		$member = $order->getMemberDetial( $model->memberId );
		$member['salesman'] = isset( $model->user->username )?$model->user->username:'';
		if( $model->payModel ){
			$payModel = tbPayMent::model()->findByPk( $model->payModel );
		}
		$model->payModel = (isset($payModel)&&$payModel)?$payModel->paymentTitle:'';
		$this->render('apply',array('model' => $model,'member' => $member ));
	}
}

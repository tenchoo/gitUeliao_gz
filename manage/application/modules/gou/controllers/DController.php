<?php
/*
* @access 送货员前台
*/
class DController extends CController {

	public $layout = 'false';



	//欢迎页面无需检察权限
    public function beforeAction($action) {
        return true;
    }

	/**
	* @access 送货员列表
	*/
	public function actionIndex() {
		$id = Yii::app()->request->getQuery('id');
		$c = Yii::app()->request->getQuery('c');

		$model = new tbDeliveryman();

		$m = $model->idMd5( $id );
		if( !is_numeric( $id  ) || $c !== $m  ){
			exit('您无权访问此页面，请联系管理员');
		}

		$memberName = tbDeliveryman::model()->getdeliverymanName( $id );
		if( empty( $memberName ) ){
			exit('您无权访问此页面，请联系管理员');
		}

		if( Yii::app()->request->isPostRequest ) {
			$this->edit( $id );
		}

		$condition['areaId'] = Yii::app()->request->getQuery('areaId');
		$condition['state'] = Yii::app()->request->getQuery('state',0);
		$condition['keyword'] = trim(Yii::app()->request->getQuery('keyword'));
		$condition['type'] = Yii::app()->request->getQuery('type');
		$condition['isDel'] = 0;

		$condition['deliverymanId'] = $id;

		$model = new tbDeliveryOrder();
		$model->isPhone = true;
		$data = $model->serach( $condition );

	//	$areas =


		$data['memberName'] = $memberName;
		$data['url'] = $this->createUrl( 'index', array( 'id'=>$id,'c'=>$c ) );

		$this->render( 'index',array_merge( $data,$condition ) );
	}

	/**
	* post结果处理 success
	* @param $url 跳转到的地址
	*/
	public function dealSuccess($url=null){
		if( Yii::app()->request->getIsAjaxRequest() ) {
			$json=new AjaxData(true,null,$url);
			echo $json->toJson();
			Yii::app()->end(200);
		} else {
			if($url)
				$this->redirect($url);
		}
	}

	/**
	* post结果处理 Error
	* @param $errors 错误信息
	*/
	public function dealError($errors){
		if( Yii::app()->request->getIsAjaxRequest() ) {
			$message = current($errors);
			if( is_array($message) ) $message =current($message);
			$json=new AjaxData(false,$message,$errors);
			echo $json->toJson();
			Yii::app()->end(200);
		}else{
			//$this->setError( $errors );
		}
	}

	/**
	* @access 编辑送货单
	*/
	public function edit( $opId ){
		$id = Yii::app()->request->getPost('editId');
		$model = tbDeliveryOrder::model()->findByPk( $id );
		if ( !$model ) {
			exit;
		}

		$state = Yii::app()->request->getPost('state');

		if( !is_null( $state ) ){
			$model->state = $state;
			$states = $model->getStates();
			if( !array_key_exists( $model->state ,$states ) ){
				exit;
			}

			if( $model->state == '1' ){
				$model->deliveryTime = new CDbExpression('NOW()');
			}
		}else{
			$deliveryAddress = Yii::app()->request->getPost('deliveryAddress');
			if( empty( $deliveryAddress  ) ) exit;

			$model->deliveryAddress = $deliveryAddress;
		}

		if( $model->save() ){
			if( !is_null( $state ) ){
				$states['1'] = '已配送';
				//增加一条修改记录
				$op = new tbDeliveryOrderOp();
				$op->deliveryOrderId =  $model->id ;
				$op->state = $model->state ;
				$remark = Yii::app()->request->getPost('remark');
				$op->remark = $states[$model->state];
				if( !empty($remark)){
					$op->remark .= ','.$remark;
				}
				$op->deliverymanId = $opId;
				$op->save();
			}
			$this->redirect(  Yii::app()->request->urlReferrer  );
			exit;
		}
	}

}
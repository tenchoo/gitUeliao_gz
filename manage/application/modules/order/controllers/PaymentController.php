<?php
/**
* 支付管理
* @access 支付管理
* @author liang
* @package Controller
* @version 0.1
*/
class PaymentController extends Controller {

	/**
	 * 支付方法
	 * @access 支付方法
	 */
	public function actionIndex() {
		$model = tbPayMent::model()->findAll();

		$data = array();
		$type = array();
		foreach ( $model as $val ){

			if( $val->type == '0'){
				$type[$val->paymentId] = $val->paymentTitle;
			}else{
				$data[] = $val->attributes;
			}
		}
		$this->render( 'index',array('data'=>$data ,'type'=>$type) );
	}

	/**
	* 添加/编辑支付类型
	* @access 添加/编辑支付类型
	*/
	public function actionAddedit(){
		$paymentId = Yii::app()->request->getQuery('paymentId');
		if( $paymentId ){
			$model = tbPayMent::model()->findByPk( $paymentId );
			if( !$model ){
				throw new CHttpException(404,"the require obj has not exists.");
			}
		}else{
			$model = new tbPayMent();
			$model->type = 0;
			$model->available = 1;
		}


		$pay = Yii::app()->request->getPost('pay');
		if( $pay ){
			if( $this->saveModel( $pay,$model ) ) {
				$this->dealSuccess( $this->createUrl( 'index' ) );
			} else {
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}
		}

		$types = $model->getPayMents('0');
		$type = array();
		foreach ( $types as $val ){
			$type[$val['paymentId']] =$val['paymentTitle'];
		}

		$this->render( 'addedit',array('data'=>$model->attributes,'type'=>$type ) );
	}

	private function saveModel( $pay,&$model ){
		if( $model->type > 1 ){
			if( empty( $pay['paymentSet']['payment_user'] ) ){
				$model->addError( 'type','账户名称必须填写' );
				return false;
			}

			if( empty( $pay['paymentSet']['payment_id'] ) ){
				$model->addError( 'type','账户ID必须填写' );
				return false;
			}
		}

		$pay['termType'] = isset($pay['termType'])?$this->setTermType( $pay['termType'] ):0;
		$pay['paymentSet'] = serialize($pay['paymentSet']);
		$model->attributes = $pay;
		return $model->save();
	}

	/**
	 * 添加/编辑支付方法
	 * @access 添加/编辑支付方法
	 */
	public function actionEdittype() {
		$paymentId = Yii::app()->request->getQuery('paymentId');
		if( $paymentId ){
			$model = tbPayMent::model()->findByPk( $paymentId );
			if( !$model ){
				throw new CHttpException(404,"the require obj has not exists.");
			}
		}else{
			$model = new tbPayMent();
			$model->type = 0;
			$model->available = 1;
		}

		if( Yii::app()->request->isPostRequest ){
			$model->paymentTitle = Yii::app()->request->getPost('paymentTitle');
			$termType = Yii::app()->request->getPost('termType');
			$model->termType = $this->setTermType( $termType );
			if( $model->save() ) {
				$this->dealSuccess( $this->createUrl( 'type' ) );
			} else {
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}

		}
		$this->render( 'edittype',array('paymentTitle'=>$model->paymentTitle,'termType'=>$model->termType ) );
	}

	private function setTermType( $termType ){
		if( !isset($termType ['0']) ) return 0;

		if( count( $termType ) == 2 ) return 3;

		return $termType ['0'];
	}


	/**
	 * 删除支付方法
	 * @access 删除支付方法
	 */
	public function actionDeltype() {
		$paymentId = (int) Yii::app()->request->getQuery('id');
		if( $paymentId>12 ){
			tbPayMent::model()->deleteByPk( $paymentId  );
		}
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}


	/**
	 * 支付类型
	 * @access 支付类型
	 */
	public function actionType() {
		$data = tbPayMent::model()->getPayMents('0');
		$this->render( 'type',array('data'=>$data ) );
	}


}

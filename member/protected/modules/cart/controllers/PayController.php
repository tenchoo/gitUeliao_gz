<?php
class PayController extends Controller {

 	/**
 	 * 模板布局文件
 	 * @var string
 	 */
	public $layout='libs.commons.views.layouts.cart';

	public function init(){
		parent::init();
		Yii::import('libs.vendors.payment.*');
	}

	/**
	 * 支付页面
	 */
	public function actionIndex() {
		$orderids = Yii::app()->request->getQuery( 'orderids' );

		$PayForm = new OrderPayForm();
		$model = $PayForm->getPayInfo( $orderids );
		if(empty( $model )){
			$url = $this->createUrl('/order/default/index');
			$this->redirect($url);
		}
		list($payModels,$logistics) = $PayForm->pcPayment();

		$dataArr = Yii::app()->request->getPost('Pay');
		if( $dataArr ){
			$payid = null;
			if( array_key_exists('bank' ,$dataArr) ){
				if( is_numeric($dataArr['bank']) ){
					if( $dataArr['bank'] <5 ){
						$PayForm->payModel = $dataArr['bank'];
						unset($dataArr['bank']);
					}else{
						$PayForm->payModel = 5;
						$PayForm->payMethod = $dataArr['bank'];
						$payid = $dataArr['bank'];
						$bank = '';
					}
				}else {
					$PayForm->payModel = 5;
					$PayForm->payMethod = 7;
					$payid = 7;
					$bank = $dataArr['bank'];
				}
			}


			if( !is_null( $payid ) ){
				//生成支付信息
				$paymentModel = $PayForm->setPaymentOrder( 0 );

				//进入支付
				$pay = new PaymentStorage();
				$params = $pay->Restructure($paymentModel,$payid,$bank);
				$pay->pay($params,$payid);
			}else{
				if( $PayForm->paymemtMethod( $dataArr,$model ) ){
					$this->dealSuccess( $this->createUrl('success') );
				}else{
					$this->dealError( $PayForm->getErrors() );
				}
			}
		}

		$this->render( 'index',array('model'=>$model,'totalPrice'=>$PayForm->totalPrice,'payModels'=>$payModels,'logistics'=>$logistics,'payTitle'=>$PayForm->payTitle) );
	}

	/**
	* 支付成功页面
	*/
	public function actionSuccess(){
		$pay = strstr( Yii::app()->request->urlReferrer, '/pay/');
		if( empty( $pay ) ){
			$pay = strstr( Yii::app()->request->urlReferrer, '/alipay/');
		}
		$title = empty($pay)?'下单':'付款';
		$this->render( 'success',array('title'=>$title) );
	}

}
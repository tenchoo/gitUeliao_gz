<?php
/**
 * 购物车
 * @author liang
 * @version 0.1
 * @package Controller
 */
class CartController extends Controller {

	public function init() {
		parent::init();

		if( empty( $this->memberId ) ) {
			$this->message = Yii::t('user','You do not log in or log out');
			$this->showJson();
		}
	}

	/**
	 * 购物车列表
	 */
	public function actionIndex(){
		$model =  new Cart($this->memberId,$this->userType);
		$this->data = $model->cartList();
		$this->showJson(true, null, $this->data);
// 		var_dump($this->data);exit;
// 		$this->state = true;
// 		$this->showJson();
	}


	/**
	* 新增
	*/
	public function actionCreate(){
		$productId = Yii::app()->request->getPost( 'productId' );
		$cart = Yii::app()->request->getPost( 'cart' );

		$model = new tbCart();
		$model->memberId = $this->memberId;
		if( $model->addCart( $productId, $cart ) ){
			$this->state = true;
		}else{
			$this->message = current( current( $model->getErrors() ) );
		}

		$this->showJson();
	}

	/**
	* 更改购物车数量
	* @param integer $id 购物车ID
	*/
	public function actionUpdate( $id ){
		$num = Yii::app()->request->getPut( 'num' );

		$model = new tbCart();
		$model->memberId = $this->memberId;
		if( $model->qty( $id,$num ) ){
			$this->state =  true;
		}else{
			$this->message = Yii::t('order','Purchase quantity must be an integer greater than 0 or with a decimal point');
		}

		$this->showJson();
	}



	/**
	* 删除购物车
	* @param integer $id 购物车ID
	*/
	public function actionDelete( $id ){

		$criteria = new CDbCriteria;
		$criteria->compare('cartId', $id);
		$criteria->compare('memberId', $this->memberId  );

		if( tbCart::model()->deleteAll( $criteria ) ){
			$this->state = true;
		}

		$this->showJson();
	}


	/**
	* 清除购物车失效清单
	*/
	public function actionDeletefailure(){
		if(!Yii::app()->request->getIsDeleteRequest()){
			$this->notFound();
		}
		$id = Yii::app()->cache->get('failureCart_'.$this->memberId);
		$model =  new Cart($this->memberId,$this->userType);
		if( $model->delete( $id ) ){
			$this->state = true;
		}
		$this->showJson();
	}

}
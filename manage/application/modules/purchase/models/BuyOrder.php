<?php
/**
* 已被弃用
*/
class BuyOrder extends CModel {

	private $_orderBuy;
	private $_products = array();

	public function __construct( $orderId=null ) {
		if( is_null( $orderId ) ) {
			$this->_orderBuy = new tbOrderbuy();
		}
		else {
			$this->_loadData( $orderId );
		}
	}

	public function attributeNames() {
		return array();
	}

	public function __get($name) {
		return $this->_orderBuy->getAttribute($name);
	}

	public function setAttributes($values,$safe=true) {
		return $this->_orderBuy->setAttributes($values,$safe);
	}

	public function pushProduct( $data ) {
		$data['relate']   = implode(':', $data['relate']);
		$data['dealTime'] = strtotime($data['dealTime']);

		$product = new tbOrderbuyProduct();
		$product->setAttributes( $data );
		array_push( $this->_products, $product );
	}

	/**
	 * 设置/获取订单所包含的产品
	 * @param null|array $data 为null代表获取产品列表,为数组表示设置包含的产品
	 * @return array|bool
	 */
	public function products( $data=null ) {
		if( is_null($data) ) {
			return $this->_products;
		}

		if( !is_null($data) && !is_array($data) ) {
			return false;
		}

		foreach( $data as $item ) {
			$this->pushProduct( $item );
		}
		return true;
	}

	public function fetchProducts() {
		return $this->_products;
	}

	/**
	 * 存储订单信息入库
	 * @return bool
	 * @throws CDbException
	 */
	public function save() {
		$transaction = $this->_orderBuy->getDbConnection()->beginTransaction();
		if( !$this->_orderBuy->save() ) {
			$this->addError('save', Yii::t('order','Unable to save buy order'));
			$error = $this->_orderBuy->getErrors();
			$error = array_shift( $error );
			Yii::log( $error, CLogger::LEVEL_ERROR, __CLASS__.'::'.__FUNCTION__);
			$transaction->rollback();
			return false;
		}

		foreach( $this->_products as $orderProduct ) {
			$orderProduct->orderId = $this->_orderBuy->orderId;
			if( !$orderProduct->save() ) {
				$this->addError('save', Yii::t('order','Unable to save buy order product'));
				$transaction->rollback();
				return false;
			}
		}
		tbOrderPurchase::model()->deleteAll( "userId=:uid", array(':uid'=>Yii::app()->getUser()->id) );
		$transaction->commit();
		return true;
	}

	private function _loadData( $orderId ) {
		$this->_orderBuy = tbOrderbuy::model()->with('products')->findByPk( $orderId );
		if( is_null($this->_orderBuy) ) {
			throw new CHttpException(404, Yii::t('order','Not found record'));
		}

		$this->_products = $this->_orderBuy->products;

		foreach( $this->_products as & $item ) {
			$item->relate = tbOrderbuyRelate::model()->findAllByAttributes(array('orderProductId'=>$item->id));
		}
	}
}

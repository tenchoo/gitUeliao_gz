<?php
/**
 * 低安全库存列表
 * @access 低安全库存管理
 * @author yagas-office
 *
 */
class LowerController extends Controller {

	/**
	 * @access 低安全库存列表
	 */
	public function actionIndex() {
		$criteria = new CDbCriteria();
		$key = Yii::app()->request->getQuery('s');

		if( !empty($key) ) {
			//根据单品编码进行搜索
			$criteria->condition = "singleNumber=:serial and state=:state";
			$criteria->params = array( ':serial'=>$key, ':state'=>tbRequestlower::STATE_NORMAL );
		}
		else {
			$criteria->condition = "state=:state";
			$criteria->params = array(':state'=>tbRequestlower::STATE_NORMAL);
		}

		$total = tbRequestlower::model()->count( $criteria );
		$pages = new CPagination( $total );
		$pages->setPageSize( (int)tbConfig::model()->get('page_size') );
		$pages->applyLimit($criteria);

		$orderList = tbRequestlower::model()->findAll( $criteria );

		$this->render( 'index', array('orderList'=>$orderList,'pages'=>$pages) );
	}

	public function actionList() {
		$criteria = new CDbCriteria();
		$key = Yii::app()->request->getQuery('s');

		if( !empty($key) ) {
			//根据单品编码进行搜索
			$criteria->condition = "singleNumber=:serial and state!=:state";
			$criteria->params = array( ':serial'=>$key, ':state'=>tbRequestlower::STATE_DELETE );
		}
		else {
			$criteria->condition = "state!=:state";
			$criteria->params = array(':state'=>tbRequestlower::STATE_DELETE);
		}

		$total = tbRequestlower::model()->count( $criteria );
		$pages = new CPagination( $total );
		$pages->setPageSize( (int)tbConfig::model()->get('page_size') );
		$pages->applyLimit($criteria);

		$orderList = tbRequestlower::model()->findAll( $criteria );

		$this->render( 'list', array('orderList'=>$orderList,'pages'=>$pages) );
	}

	/**
	 * 添加入待采购列表
	 * @access 加入采购列表
	 */
	public function actionPurchase() {
		$id = Yii::app()->request->getQuery('id');
		$errors = array( array( Yii::t('warehouse','No data') ) );

		if( !empty( $id ) && is_numeric( $id ) ){
			$tr = Yii::app()->getDb()->beginTransaction();
			$result = $this->pushTopurchase( $id,$errors );
			if( $result ){
				$tr->commit();
				$this->dealSuccess( Yii::app()->request->urlReferrer );
				return;
			}else{
				$tr->rollback();
			}
		}

		$this->dealError( $errors );
		$this->redirect( $this->createUrl('index') );
	}

	/**
	 * 更新订单状态
	 * @param integer $id
	 */
	private function pushTopurchase( $id ,&$errors ) {
		$lower = tbRequestlower::model()->findByPk( $id ,"state=:state", array(':state'=>tbRequestlower::STATE_NORMAL) );
		if( !$lower ) {
			return false;
		}

		$lower->state = tbRequestlower::STATE_PROCCESSING;
		if( $lower->save() ) {
			$purchase = new tbOrderPurchase2();
			$purchase->state = $purchase::STATE_NORMAL;
			$purchase->source = $purchase::FROM_LOWER;
			$purchase->deliveryTime = '0000-00-00';
			$purchase->color = $lower->color;
			$purchase->comment = '低安全库存请购';

			$product = tbProductStock::model()->findByAttributes( array( 'singleNumber'=>$lower->singleNumber ) );
			if( $product ){
				$purchase->productId = $product->productId;
			}
			$data = array(
				'orderId' =>  $lower->lowerId,
				'orderProId' => 0,
				'productCode' => $lower->singleNumber,
				'quantity' => $lower->buyTotal,
			);
			$purchase->setAttributes( $data );
			if( $purchase->save() ) {
				return true;
			}else{
				$errors = $purchase->errors;
				return false;
			}
		}else{
			$errors = $lower->errors;
		}

		return false;
	}
}
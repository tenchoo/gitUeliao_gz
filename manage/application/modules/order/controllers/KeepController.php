<?php
/**
 * 留货单管理
 * @author yagas
 * @access 留货订单管理
 *
 */
class KeepController extends Controller {

	/**
	 * 待审核留货单列表
	 *
	 * @access 待审核留货订单
	 */
	public function actionIndex() {
		$total = tbOrderKeep::model ()->countByAttributes ( array (
				'state' => tbOrderKeep::STATE_NORMAL,
				'buyState' =>tbOrderKeep::BUYSTATE_NO,
		) );
		$pages = new CPagination ( $total );
		$pages->setPageSize ( tbConfig::model ()->get ( 'page_size' ) );
		$orders = tbOrderKeep::model ()->with ( 'orderInfo' )->findAllByAttributes ( array (
				'state' => tbOrderKeep::STATE_NORMAL,
				'buyState' =>tbOrderKeep::BUYSTATE_NO,
		) );
		$this->render ( 'index', array (
				'total' => $total,
				'pages' => $pages,
				'orders' => $orders,
				'condition' => array (
						'createTime1' => '',
						'createTime2' => '',
						'orderId' => ''
				)
		) );
	}

	/**
	 * 留货订单延期列表
	 *
	 * @access 留货订单延期列表
	 */
	public function actionDelay() {
		$orderId = Yii::app ()->request->getQuery ( 'orderId' );

		$c = new CDbCriteria;
		if( $orderId ){
			$c->compare('orderId',$orderId);
		}

		$c->order = 'field (state, 0 ) desc ,createTime desc';

		$pageSize = tbConfig::model()->get( 'page_size' );
		$model = new CActiveDataProvider( 'tbOrderKeepDelay', array(
			'criteria'=>$c,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));

		$data = $model->getData();
		$pages = $model->getPagination();

		$orderids = array_map( function ($i){return $i->orderId;},$data);

		//查找留货单信息
		$Keep = tbOrderKeep::model ()->with ( 'orderInfo' )->findAllByAttributes ( array (
				'orderId' => $orderids,
		) );

		$orders = array();
		foreach ( $Keep as $val ){
			$orders[$val->orderId] = $val;
		}


		$stateTitle = array('0'=>'未审核','1'=>'审核通过','2'=>'审核不能过');
		foreach ( $data as &$val ){
			$val = $val->attributes;
			$val['stateTitle'] = $stateTitle[$val['state']];
			$val['order'] = $orders[$val['orderId']];
		}






		$this->render ( 'delay', array (
				'pages' => $pages,
				'data' => $data,
				'condition' => array (
						'createTime1' => '',
						'createTime2' => '',
						'orderId' => $orderId
				)
		) );
	}

	/**
	 * 留货订单延期审核
	 *
	 * @access 留货订单延期审核
	 */
	public function actionCheckdelay() {
		$id = Yii::app ()->request->getQuery ( 'id' );
		if ( empty($id) || !is_numeric ( $id ) ) {
			throw new CHttpException ( 404, "Not found delay apply of order:{$id}" );
		}

		$DelayApply = tbOrderKeepDelay::model()->find('orderId = :orderId and state = :state ',array(':orderId'=>$id,':state'=>0));
		if ( !$DelayApply ) {
			throw new CHttpException ( 404, "Not found delay apply of order:{$id}" );
		}

		$Keep = tbOrderKeep::model ()->with ( 'orderInfo' )->find ( 't.orderId = :id and t.state != :state',
																	array ( ':id' => $id ,':state' => tbOrderKeep::STATE_REFUSE ) );
		if ( !$Keep ) {
			throw new CHttpException ( 404, "Not found delay apply of order:{$id}" );
		}


		$delay = tbConfig::model()->get('order_keep_delay');
		$delayTime = $Keep->expireTime + $delay*86400;

		if (Yii::app ()->request->getIsPostRequest ()) {
			$DelayApply->scenario = 'check';
			$DelayApply->state = Yii::app ()->request->getPost ( 'state' );
			$DelayApply->reason = Yii::app ()->request->getPost ( 'reason' );
			$DelayApply->userId = Yii::app ()->user->id;
			$DelayApply->checkTime = new CDbExpression('NOW()');
			if ( $DelayApply->save ()) {
				if( $DelayApply->state == '1' ){
					$Keep->expireTime = $delayTime;
					$Keep->save ();
				}
				$this->dealSuccess( $this->createUrl( 'delay' ) );
			} else {
				$this->dealError( $DelayApply->getErrors () );
			}
		}

		$products = tbOrderProduct::model ()->findAllByAttributes ( array (	'orderId' => $id ) );

		$this->render ( 'checkdelay', array (
				'order' => $Keep->orderInfo,
				'products' => $products,
				'userInfo' => $Keep->orderInfo->getUserInfo(),
				'expireTime'=>date('Y-m-d H:i:s',$Keep->expireTime),
				'delayTime'=>date('Y-m-d H:i:s',$delayTime),
		) );
	}


	/**
	 * 已审核留货单
	 *
	 * @access 已审核留货单
	 */
	public function actionCheck() {
		$total = tbOrderKeep::model ()->countByAttributes ( array (
				'state' => tbOrderKeep::STATE_CHECKED
		) );
		$pages = new CPagination ( $total );
		$pages->setPageSize ( tbConfig::model ()->get ( 'page_size' ) );
		$orders = tbOrderKeep::model ()->with ( 'orderInfo' )->findAll ( 't.state != '.tbOrderKeep::STATE_NORMAL );
		$this->render ( 'check', array (
				'total' => $total,
				'pages' => $pages,
				'orders' => $orders,
				'condition' => array (
						'createTime1' => '',
						'createTime2' => '',
						'orderId' => ''
				)
		) );
	}

	/**
	 * 查看留货单
	 * @access 查看留货单明细
	 */
	public function actionView(){
		$id = Yii::app ()->request->getQuery ( 'id' );
		if ($id && is_numeric ( $id )) {
			$order = tbOrder::model ()->with('products')->findByPk ( $id );
			if (! $order) {
				throw new CHttpException ( 404, "Not found order:{$id}" );
			}

			$orderClass = new Order();
			$member = $orderClass->getMemberDetial( $order->memberId );
			$check = tbOrderKeep::model()->findByAttributes ( array('orderId'=>$id));
			$this->render ( 'view', array (
					'order' => $order,'member' => $member,
					'products' => $order->products,
					'check'=>$check,
			) );
			Yii::app()->end(200);
		}
		throw new CHttpException(404,"Not found page");
	}

	/**
	 * 审核留货订单
	 * @access 已审核留货订单
	 */
	public function actionExamine() {
		$id = Yii::app ()->request->getQuery ( 'id' );

		$order = null;
		if ( $id && is_numeric ( $id ) ) {
			$order = tbOrderKeep::model()->findByAttributes ( array(
							'orderId'=>$id,
							'state'=>tbOrderKeep::STATE_NORMAL,
							'buyState' =>tbOrderKeep::BUYSTATE_NO,
							));
		}

		if (! $order) {
			throw new CHttpException ( 404, "Not found order:{$id}" );
		}

		if (Yii::app ()->request->getIsPostRequest ()) {
			$order->userId = Yii::app ()->user->id;
			$order->state = Yii::app ()->request->getPost ( 'state' );
			$order->cause = Yii::app ()->request->getPost ( 'cause' );
			if ( $order->save ()) {
				$this->dealSuccess( $this->createUrl( 'index' ) );
			} else {
				$this->dealError( $order->getErrors () );
			}
		}

		$products = tbOrderProduct::model ()->findAllByAttributes ( array (
					'orderId' => $id
			) );
		$this->render ( 'examine', array (
				'order' => $order->orderInfo,
				'products' => $products,
				'userInfo' => $order->orderInfo->getUserInfo(),
				'expireTime'=>date('Y-m-d H:i:s',$order->expireTime),
		) );
	}
}
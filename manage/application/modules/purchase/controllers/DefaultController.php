<?php
/**
 * 采购单管理
 * @access 采购单管理
 * @author liang
 * @package Controller
 * @version 0.2
 *
 */
class DefaultController extends Controller {

	//创建采购单时存放工厂信息
	private $_supplierInfo;

	/**
	 * 待采购订单
	 *
	 * @access 待采购订单
	 */
	public function actionIndex() {
		$s = trim( Yii::app()->request->getQuery('s') );
		$o = trim( Yii::app()->request->getQuery('o') );

		$criteria  = new CDbCriteria();
		if(!empty($s) || !empty($o)) {
			$criteria->condition = "state=".tbOrderPurchase2::STATE_NORMAL;
			if( $s ) {
				$criteria->addColumnCondition( array('productCode'=>$s) );
			}
			if( $o ) {
				$criteria->addColumnCondition( array('orderId'=>$o) );
			}
		}
		else {
			$criteria->condition = "state=:state and isAssign=:assign";
			$criteria->params = array ( ':state' => tbOrderPurchase2::STATE_NORMAL, ':assign'=>tbOrderPurchase2::STATE_UNASSIGN );
		}

		$pager = new CPagination ( tbOrderPurchase2::model ()->count ( $criteria ) );
		$pager->setPageSize( (int) tbConfig::model()->get( 'page_size' ) );
		$pager->applyLimit ( $criteria );

		$orderList = $result = tbOrderPurchase2::model()->findAll( $criteria );
		$chooseCount =  tbOrderPurchase2::model()->countByAttributes(
							array(
								'state'=>1,
								'userId'=>Yii::app()->user->id
							));
						

		$this->render ( 'index', array (
				'list' => $orderList,
				'pages' => $pager,
				'chooseCount' => $chooseCount,
				's' => $s,
				'o' => $o,

				) );
	}

	/**
	 * 新增采购订单
	 *
	 * @access 新增采购订单
	 * @throws CHttpException
	 */
	public function actionAdd() {
		if (Yii::app ()->request->getIsAjaxRequest ()) {
			if (Yii::app ()->request->getQuery ( 'event' ) == 'remove') {
				$this->removeBuyDraft ();
			} else {
				$this->createBuyDraft ();
			}
		}

		if ( Yii::app()->request->getIsPostRequest() ) {
			$this->createPurchasingOrder();

			/* $form = Yii::app ()->request->getPost( 'form' );
			$products = Yii::app ()->request->getPost( 'product' );
			$form ['memberId'] = Yii::app()->getUser()->id;

			if (!is_array( $products ) || !$products) {
				$this->setError( array(Yii::t('base','Not found product choose')) );
			}

			if (! $this->hasError ()) {

				$order = new BuyOrder();
				$order->setAttributes( $form );
				$order->products( $products );

				if ($order->save()) {
					Yii::app ()->getUser()->setFlash( 'success', $this->createUrl( 'default/index' ) );
				} else {
					$this->setError( $order->getErrors() );
				}
			} */
		}

		$criteria        = new CDbCriteria();
		$criteria->order = "productCode ASC";
		$dataList = tbOrderPurchase2::model()->findAllByAttributes(
			array('state'=>tbOrderPurchase2::STATE_CHOOSE, 'userId'=>Yii::app()->user->id),
			$criteria
		);

		$orderList = $this->orderListFormat( $dataList );

		$optional = array(
			'orderList'          => $orderList,
			'hasMutipleSupplier' => $this->checkSupplier( array_keys($orderList) ),
			'supplierInfo'       => $this->_supplierInfo
		);
		if( $optional['hasMutipleSupplier'] === false ) {
			Yii::app()->getUser()->setFlash('warning', Yii::t('order','has multiple supplier'));
		}

		$this->render( 'order', $optional );
	}


	/**
	 * 采购订单管理
	 *
	 * @access 采购订单管理
	 */
	public function actionList() {
		$state = Yii::app()->request->getQuery('state');
		$purchaseId = Yii::app()->request->getQuery('purchaseId');
		$factory = Yii::app()->request->getQuery('factory');
		$purchaseDate = Yii::app()->request->getQuery('purchaseDate');

		$model = new tbOrderPurchasing();
		$stateTitles = $model->stateTitles();
		$criteria = new CDbCriteria ();

		if( array_key_exists( $state,$stateTitles ) ){
			$criteria->compare('state',$state );
		}

		if( !empty( $purchaseId ) ){
			$criteria->compare('purchaseId',$purchaseId );
		}

		if( !empty( $factory ) ){
			$criteria->compare('supplierName',$factory,true );
		}

		if( !empty( $purchaseDate ) ){
			$t = strtotime( $purchaseDate );
			$criteria->addCondition(" t.createTime >= '$t'");
			$t = $t+86400;
			$criteria->addCondition("t.createTime<'$t'");
		}

		$criteria->addCondition( "state!=".tbOrderPurchasing::STATE_DELETE );
		$criteria->order = "field(t.state,0) DESC , createTime DESC";

		$pages = new CPagination ( $model->count ( $criteria ) );
		$pages->setPageSize ( tbConfig::model ()->get ( 'page_size' ) );
		$pages->applyLimit ( $criteria );

		$list = $model->findAll( $criteria );

		$this->render ( 'list', array (
				'dataList' => $list,
				'pages' => $pages,
				'stateTitles' => $stateTitles,
				'state' => $state,
				'purchaseId' => $purchaseId,'factory' => $factory,'purchaseDate' => $purchaseDate,
		) );
	}

	/**
	 * 业务员创建发货单
	 *
	 * @access 创建发货单
	 * @throws CHttpException
	 */
	public function actionPost() {
		$id = Yii::app ()->request->getQuery ( 'id' );
		$purchasing = tbOrderPurchasing::model()->findByPk($id,'state =:s',array(':s'=>tbOrderPurchasing::STATE_NORMAL));
		if( is_null($purchasing) ) {
			$this->redirect ( $this->createUrl('list') );
		}

		if (Yii::app ()->request->getIsPostRequest ()) {
			  //创建发货单记录
			$post = new tbOrderPost2();
			if( $post->createPost( $purchasing, 1 ) ){
				$this->dealSuccess ( $this->createUrl('list') );
			}else{
				$this->dealError ( $post->getErrors() );
			}
		}

		$products = tbOrderPurchasingProduct::model()->findAllByAttributes(['purchaseId'=>$id]);
		$this->render ( 'post', array ( 'order' => $purchasing , 'products'=>$products) );
	}


   /**
     * @access 确定发货完成
     */
    public function actionFinish() {
		$id = Yii::app()->request->getQuery('id');
        $purchase = tbOrderPurchasing::model()->findByPk($id,'state =:s',array(':s'=>tbOrderPurchasing::STATE_NORMAL));
        if( is_null( $purchase ) ) {
            $this->redirect( $this->createUrl('index') );
        }

		//判断是否有发过货，有发货不允许取消
		$falg = tbOrderPost2::model()->exists('purchaseId=:purchaseId',array(':purchaseId'=>$purchase->purchaseId));
		if( !$falg ){
			$this->dealError ( array('还未发过货,不能提交发货完成') );
		}else{
			$purchase->stateToDone();
			$this->dealSuccess( Yii::app()->request->urlReferrer );
		}
    }

	/**
	 * 查看采购单详情
	 * @access 查看采购单明细
	 */
	public function actionView() {
		$id = Yii::app ()->request->getQuery ( 'id' );
		$purchasing = tbOrderPurchasing::model()->findByPk($id);
		if( is_null($purchasing) ) {
			throw new CHttpException( 404, Yii::t('base','Not found record'));
		}

		if($purchasing->state == tbOrderPurchasing::STATE_CLOSE ){
			$close = tbPurchasingClose::model()->findByPk( $id );
			$close = $close->attributes;
			$close['operation'] = tbUser::model()->getUsername( $close['opId'] );
		}else{
			$close = null;
		}
		$this->render ( 'view', array ( 'order' => $purchasing,'close' => $close ) );
	}

	/**
	 * 取消采购单
	 * @access 取消采购单
	 */
	public function actionCancle(){
		$id = Yii::app ()->request->getQuery ( 'id' );
		$model = tbOrderPurchasing::model()->findByPk($id,'state =:s',array(':s'=>tbOrderPurchasing::STATE_NORMAL));
		if( is_null($model) ) {
			throw new CHttpException( 404, Yii::t('base','Not found record'));
		}

		if( Yii::app()->request->getIsPostRequest() ) {
			//生成取消采购单记录并更新采购单状态为已取消
            $reason = Yii::app()->request->getPost('reason');
			$Purchasing = new Purchasing();

			if( $Purchasing->cancle( $model ,$reason ) ){
				$this->dealSuccess ( $this->createUrl('list') );
			}else{
				$this->dealError ( $Purchasing->getErrors() );
			}
        }

		$this->render ( 'cancle', array ( 'order' => $model ) );

	}

	/**
	 * 订单搜索
	 * @access hidden
	 * @deprecated
	 * 声明过期，后续搜索请使用 actionIndex 方法
	 */
// 	public function actionSearch() {
// 		$s = Yii::app()->request->getQuery('s');
// 		$o = Yii::app()->request->getQuery('o');

// 		$criteria  = new CDbCriteria();
// 		$criteria->condition = "state=".tbOrderPurchase2::STATE_NORMAL;

// 		if( $s ) {
// 			$criteria->addColumnCondition( array('productCode'=>$s) );
// 		}
// 		if( $o ) {
// 			$criteria->addColumnCondition( array('orderId'=>$o) );
// 		}

// 		$pager = new CPagination ( tbOrderPurchase2::model ()->count ( $criteria ) );
// 		$pager->setPageSize( (int)tbConfig::model()->get( 'page_size' ));
// 		$pager->applyLimit( $criteria );

// 		$orderList = tbOrderPurchase2::model()->findAll( $criteria );
// 		$this->render ( 'index', array (
// 				'list' => $orderList,
// 				'pages' => $pager,
// 				'chooseCount' => tbOrderPurchase2::model()->countByAttributes(
// 						array(
// 								'state'=>1,
// 								'userId'=>Yii::app()->user->id
// 						)
// 					)
// 		) );
// 	}


	public function formDict($formId) {
		switch ($formId) {
			case 0 :
				$caption = 'REQUEST_FORM_COMPANY';
				break;

			case 1 :
				$caption = 'REQUEST_FORM_REPERTORY';
				break;

			case 2 :
				$caption = 'REQUEST_FORM_ORDER';
				break;
		}
		return Yii::t ( 'order', $caption );
	}

	/**
	 * 采购单草稿箱
	 * 存放采购产品队列
	 * 生成采购订单时队列后需要清除队列数据
	 */
	public function createBuyDraft() {
		$id = Yii::app ()->request->getPost ( 'id' );

		$transation = Yii::app ()->getDb ()->beginTransaction ();
		$rowsCount  = count($id);
		$criteria   = new CDbCriteria();
		$criteria->addInCondition('purchaseId',$id);
		$afterRows = tbOrderPurchase2::model()->updateAll(
			array(
				'state' => tbOrderPurchase2::STATE_CHOOSE,
				'userId'=>Yii::app()->user->id ),
			$criteria
		);

		if( $afterRows !== $rowsCount ) {
			$transation->rollback();

			$state = new AjaxData ( false );
			echo $state->toJson ();
			Yii::app ()->end ( 200 );
		}

		Yii::app()->session->add('alertSuccess',true);
		$transation->commit ();
		$count = tbOrderPurchase2::model()->countByAttributes( array('userId'=>Yii::app()->user->id,'state'=>tbOrderPurchase2::STATE_CHOOSE) );
		$state = new AjaxData ( true );
		$state->data = array('count'=>$count);
		echo $state->toJson ();
		Yii::app()->end(200);
	}

	/**
	 * 从采购队列中移除产品
	 */
	public function removeBuyDraft() {
		$result = false;
		$id = Yii::app ()->request->getQuery ( 'id' );
		$order = tbOrderPurchase2::model ()->findByPk ( $id );

		if ($order instanceof CActiveRecord) {
			$order->state = 0;
			$order->userId = 0;
			if ( $order->save() ) {
				$result = true;
			}
		}
		$state = new AjaxData ( $result );
		echo $state->toJson ();
		Yii::app ()->end ( 200 );
	}

	/**
	 * 创建工厂发货单
	 */
	public function createPostOrder() {
		$form = Yii::app ()->request->getPost ( 'form' );
		unset($form['logisticId']);
		$products = Yii::app ()->request->getPost ( 'post' );

		$purchase = tbOrderPurchasing::model()->findByPk($form['purchaseId']);
		if( is_null($purchase) ) {
			throw new CHttpException(404,Yii::t('base','Not found purchase by id:{id}',array('{id}'=>$form['purchaseId'])));
		}

		$purchaseInfo = $purchase->getDetails();
		foreach( $purchaseInfo as $item ) {
			$key  = $item->productCode;
			$info = $item->getAttributes(array('source','purchaseId','purchaseProId'));
			if( array_key_exists($key,$products) ) {
				$products[$key] = array_merge($products[$key],$info);
			}
		}

		$PostOrder = new tbOrderPost2();
		$PostOrder->setAttributes( $form );
		$PostOrder->orderType = 1;

		$trans = Yii::app()->getDb()->beginTransaction();
		if( !$PostOrder->save() ) {
			$errors = $PostOrder->getErrors();
			$error = array_shift($errors);
			$this->setError(array(Yii::t('order',$error[0])));
			goto throw_flag;
		}

		foreach( $products as $item ) {
			$PostProduct = new tbOrderPost2Product();
			$PostProduct->setAttributes( $item );
			$PostProduct->postId = $PostOrder->postId;

			if( !$PostProduct->save() ) {
				$this->setError(array(Yii::t('order','Failed to save post-order detail')));
				goto throw_flag;
			}
		}

		if( !$purchase->stateToDone() ) {
			$this->setError(array(Yii::t('order','Failed to change purchase state')));
			goto throw_flag;
		}

		$trans->commit();
		Yii::app()->session->add('alertSuccess',true);
		$this->redirect( $this->createUrl('list') );

		throw_flag:
			$trans->rollback();
	}



	public function hiddenFiled($index, $name, $value) {
		$profix = "product[{$index}][{$name}]";
		return CHtml::hiddenField ( $profix, $value );
	}

	/**
	 * 明细来源信息
	 * @param array $Relates  关联数据数组
	 * @param array $unitName 计价单位
	 * @return array
	 */
	public function getChildrens( $Relates, $unitName='码' ) {
		$rows     = count( $Relates );
		$total    = 0;

		ob_start();
		foreach( $Relates as $index => $children ){
			$total += $children->total;
			if( $index === 0 ) {
				continue;
			}

			echo '<tr>';
			echo CHtml::tag('td',array(),$children->orderId);
			echo CHtml::tag('td',array(),$children->total.$unitName);
			echo CHtml::tag('td',array(),'');
			echo '</tr>';
		}
		$childrens = ob_get_contents();
		ob_end_clean();

		return array(
			'list'  => $childrens,
			'rows'  => $rows,
			'total' => $total
		);
	}

	/**
	 * 对比所有采购的商品是否为同一家供应商的产品
	 * @param string $serials 产品单品编码
	 * @return boolean
	 */
	private function checkSupplier( $serials ) {
		$info = null;
		foreach( $serials as $item ) {
			$result = tbProcurement::model()->findByProductSerial( $item );

			if( is_null($info) ) {
// 				$this->_supplierInfo = $result;
				$info = array('supplierId'=>$result['supplierId']);
				continue;
			}

			$this->_supplierInfo = $result;
			if( $result['supplierId'] != $info['supplierId'] ) {
				return false;
			}
		}
		return true;
	}

	private function orderListFormat( $result ) {
		$newResult = array();
		foreach( $result as $item ) {
			$key = $item->productCode;
			if( !array_key_exists($key,$newResult) ) {
				$row = array(
					'productCode' => $item->productCode,
					'color'       => $item->color,
					'products'    => array(),
					'total'       => 0,
					'purchaseIds'  => array()
				);
				$newResult[$key] = $row;
			}
			array_push( $newResult[$key]['products'], $item );
			$newResult[$key]['total'] += $item->quantity;
			array_push( $newResult[$key]['purchaseIds'], $item->purchaseId );
		}
		return $newResult;
	}

	private function createPurchasingOrder() {
		$form = Yii::app()->request->getPost('form');
		$post = Yii::app()->request->getPost('product');

		if( !is_array($post) && empty( $post )){
			$this->setError( array( array('无采购产品')) );
			return false;
		}

		$purchasing = new tbOrderPurchasing();
		$purchasing->setAttributes( $form );
		$purchasing->userId = Yii::app()->user->id;

		$trans = Yii::app()->getDb()->beginTransaction();
		if( !$purchasing->save() ) {
			$trans->rollback();
			$this->setError( $purchasing->getErrors() );
			return false;
		}

		$purchase2 = array();
		foreach( $post as $item ) {
			$ids = $item['purchaseIds'];
			unset($item['purchaseIds']);
			$pro              = new tbOrderPurchasingProduct();
			$pro->setAttributes($item);
			$pro->purchaseIds = $ids;
			$pro->purchaseId  = $purchasing->purchaseId;

			if( !$pro->save() ) {
				$trans->rollback();
				$this->setError( $pro->getErrors() );
				return false;
			}

			foreach( explode(':',$ids) as $ar ) {
				array_push($purchase2,$ar);
				$ar = tbOrderPurchase2::model()->findByPk($ar);
				if( $ar ) {
					$detail = new tbOrderPurchasingDetail();
					$detail->import( $ar );
					$detail->setAttributes( $pro->getAttributes(array('purchaseId','purchaseProId')) );
					if( !$detail->save() ) {
						$trans->rollback();
						$this->setError( $detail->getErrors() );
						return false;
					}
				}
			}
		}

		$criteria = new CDbCriteria();
		$criteria->addInCondition('purchaseId',$purchase2);
		$after = tbOrderPurchase2::model()->updateAll(array('state'=>tbOrderPurchase2::STATE_DONE),$criteria);
		if( count($purchase2) !== $after ) {
			$trans->rollback();
			$this->setError( 'faild update purchase2 state' );
			return false;
		}

		$trans->commit();
		Yii::app()->session->add('alertSuccess',true);
		$this->redirect( $this->createUrl('index') );
		Yii::app()->end(200);
	}
}
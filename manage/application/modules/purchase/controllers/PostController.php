<?php
/**
 * @access 工厂发货单管理
 * @author yagas-office
 *
 */
class PostController extends Controller {

	/**
	 * 工厂发货单管理
	 * @access 工厂发货单列表
	 */
	public function actionIndex() {
		$condition['productCode'] = Yii::app()->request->getQuery('s');
		$condition['postId']  = Yii::app()->request->getQuery('o');
		$condition['postTime']  = Yii::app()->request->getQuery('postTime');
		$condition['purchaseId']  = Yii::app()->request->getQuery('purchaseId');
		$orders = tbOrderPost2::model()->postList( $condition,$pages );
		$this->render('index', array('orders'=>$orders, 'pages'=>$pages,'condition'=>$condition));
	}

	/**
	 * 匹配订单
	 * @access 匹配订单
	 */
	public function actionAssign() {
		$id = Yii::app()->request->getQuery('id');
		if( $id ) {

			if( Yii::app()->request->getIsPostRequest() ) {
				$this->saveAssign();
			}

			$postProduct = tbOrderPost2Product::model()->with('details')->findByPk($id);
			if( is_null($postProduct) ) {
				goto not_found_data;
			}

			$products = tbOrderPurchase2::model()->findUnassignProduct($postProduct->productCode);
			$detail = $postProduct->details;
			$unit = ZOrderHelper::getUnitName($detail->productCode);

			$this->render('assign',array('orderProduct'=>$postProduct,'products'=>$products,'detail'=>$detail,'unit'=>$unit));
			Yii::app()->end();
		}

		not_found_data:
			throw new CHttpException(404,'Not Found Product');
	}

	/**
	 * 查看匹配订单
	 * @access 查看匹配订单
	 */
	public function actionAssignView() {
		$id = Yii::app()->request->getQuery('id');
		if( $id ) {
			$postProduct = tbOrderPost2Product::model()->with('details')->findByPk($id);
			if( is_null($postProduct) ) {
				goto not_found_data;
			}

			$products = $postProduct->getAssigned();
			$detail = $postProduct->details;
			$unit = ZOrderHelper::getUnitName($detail->productCode);
			$this->render('assignview',array('orderProduct'=>$postProduct,'products'=>$products,'unit'=>$unit,'detail'=>$detail));
			exit;
		}

		not_found_data:
		throw new CHttpException(404,'Not Found Product');
	}

	/**
	 * 工厂订单查看
	 * @access 订单查看
	 */
	public function actionView() {
		$id = Yii::app()->request->getQuery('id');
		$postOrder = tbOrderPost2::model()->with('products')->findByPk($id);
		$purchaseOrder = tbOrderPurchasing::model()->findByPk($postOrder->purchaseId);

		$products = array_map(function($row){
			$line = $row->getAttributes();
			$line['detail'] = $row->details()->getAttributes();
			return $line;
		}, $postOrder->products);

		$this->render( 'view', array( 'orderPost'=>$postOrder, 'products'=>$products, 'purchase'=>$purchaseOrder ) );
	}


	/**
	 * 待匹配发货单列表
	 * 获取状态为tbOrder::TYPE_BOOKING(客户订货订单)的发货列表
	 * 使用CPageination的appliyLimit方法对列表进行分页处理
	 * tbOrder模型使用with关联products查询订单明细信息
	 */
	public function actionAssignwait( ) {
		$this->assignList ( array(0,1) );
	}

	/**
	 * @access 已匹配的发货单
	 */
	public function actionAssigned(){
		$this->assignList ( array(2,3) );
	}

	private function assignList ( array $state ){
		$condition['productCode'] = trim( Yii::app()->request->getQuery('s'));
		$condition['postId']  = trim( Yii::app()->request->getQuery('o'));
		$condition['postTime']  = trim( Yii::app()->request->getQuery('postTime'));
		$condition['purchaseId']  = trim( Yii::app()->request->getQuery('purchaseId'));
		$condition['state']  = $state ;
		$orders = tbOrderPost2::model()->postList( $condition,$pages );
		$this->render('assignwait', array('orders'=>$orders, 'pages'=>$pages,'condition'=>$condition));
	}

	/**
	 * 保存匹配订单
	 */
	public function saveAssign() {
		$postProId = Yii::app()->request->getQuery('id');
		$assigns   = Yii::app()->request->getPost('assign',array());

		$postPro = tbOrderPost2Product::model()->findByPk( $postProId );
		if( !$postPro ) {
			throw new CHttpException( 404, Yii::t('order','Not found record'));
		}

		$assigns = array_map(function($row){
			$keys = array('purchaseId','quantity');
			$values = explode(':', $row);
			return array_combine($keys, $values);
		}, $assigns);

		//计算进行匹配的产品总数
		$total = 0;
		$purchaseId = array();
		foreach ($assigns as $item) {
			$total += $item['quantity'];
			array_push($purchaseId, $item['purchaseId']);
		}

		$transaction = Yii::app()->getDb()->beginTransaction();
		//匹配数量大于发货总数
		if($postPro->postTotal < $total) {
			$this->setError(array(Yii::t('order','Assign quantity overflow')));
			goto failed_save_assign;
		}

		//更新匹配信息
		$purchase    = tbOrderPurchase2::model()->findAllByPk($purchaseId);
		foreach($purchase as $record) {
			//变更待采购订单状态为已匹配
			if(!$record->toAssign()) {
				$this->setError($record->getErrors());
				goto failed_save_assign;
			}

			$assign             = new tbOrderPost2Assign();
			$assign->postProId  = $postProId;
			$assign->purchaseId = $record->purchaseId;
			if(!$assign->save()) {
				$this->setError($assign->getErrors());
				goto failed_save_assign;
			}

			if(!tbStorageLock::lock(new CEvent($record))) {
				$this->setError(array('save'=>Yii::t('order','Failed push lock')));
				goto failed_save_assign;
			}
		}

		//将明细发货单状态变更为已匹配
		if( !$postPro->toAssign() ) {
			$this->setError(array('save'=>Yii::t('order','Failed save assign info')));
			goto failed_save_assign;
		}

		if( !$this->hasError() ) {
			$transaction->commit();
			Yii::app()->session->add('alertSuccess',true);
			$this->redirect( $this->createUrl('post/assignwait') );
		}

		failed_save_assign:
			$transaction->rollback();
			return false;
	}
}
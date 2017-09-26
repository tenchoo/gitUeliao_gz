<?php
/**
 * 内部请购管理
 * @access 内部请购管理
 * @author yagas-office
 *
 */
class RequestbuyController extends Controller {

	/**
	 * 默认请购单列表
	 * @access 请购单列表
	 */
	public function actionIndex() {
		$optional = $this->fetchOrderData('checked');

		//渲染模板
		$this->render('index', array('dataList'=>$optional['orderList'],'pages'=>$optional['pages']));
	}

	/**
	 * 内部请购单管理列表
	 * @access 内部请购单管理
	 */
	public function actionList() {
		$optional = $this->fetchOrderData( 'default', 'DESC' );

		//渲染模板
		$this->render('list', array('dataList'=>$optional['orderList'],'pages'=>$optional['pages']));
	}

	/**
	 * 新建内部请购订单
	 * @access 新建内部请购单
	 */
	public function actionAddnew() {
		if( Yii::app()->request->getIsPostRequest() ) {
			$this->saveRequestOrder();
		}
		$this->render('addnew', array('order'=>new RequestOrder()));
	}

	/**
	 * 查看请购单
	 * @access 查看订单
	 */
	public function actionView() {
		$id = Yii::app()->request->getQuery('id');
		if( is_null($id) || empty($id) ) {
			throw new CHttpException(404, Yii::t('base','Not found order'));
		}

		$order = new RequestOrder( $id );

		$oplog = tbRequestbuyOp::model()->getOp( $order->orderId );
		$this->render('view', array('order'=>$order,'oplog'=>$oplog) );
	}

	/**
	 * 编辑请购单
	 * @access 编辑订单
	 */
	public function actionEdit() {
		if( Yii::app()->request->getIsPostRequest() ) {
			$this->saveRequestOrder();
		}

		$id = Yii::app()->request->getQuery('id');
		if( is_null($id) || empty($id) ) {
			throw new CHttpException(404, Yii::t('base','Not found order'));
		}

		$order = new RequestOrder( $id );
		$this->render('addnew', array('order'=>$order) );
	}

	/**
	 * 关闭请购单
	 * @access 关闭订单
	 */
	public function actionClose() {
		$id = Yii::app()->request->getQuery('id');
		if( is_null($id) || empty($id) ) {
			throw new CHttpException(404, Yii::t('base','Not found order'));
		}

		if( Yii::app()->request->getIsPostRequest() ) {
			$form = Yii::app()->request->getPost('form');
			$order = new RequestOrder( $id );
			$order->setAttributes( $form );
			if( $order->close() ) {
				$this->dealSuccess( $this->createUrl( 'index' ) );
			} else {
				$this->dealError( $order->getErrors() );
			}
		}

		if( !isset($order) ) {
			$order = new RequestOrder( $id );
		}

		$this->render('close', array('order'=>$order) );
	}


	/**
	 * 内部请购单审核通过并添加到等采购队列
	 * @throws CHttpException
	 * @access 待审核请购单
	 */
	public function actionValidate() {

		if( Yii::app()->request->getIsAjaxRequest() ) {
			$id = Yii::app()->request->getQuery('id');
			$order = tbRequestbuy::model()->findByPk( $id );
			if( is_null($order) ) {
				$this->setError( array('Not found Record') );
				goto show_error_info;
			}

			$tr = Yii::app()->getDb()->beginTransaction();
			if( !tbOrderPurchase2::importOrder( $order ) ) {
				$this->setError( array(Yii::t('order','faild push order to purchase')) );
				goto show_error_info;
			}

			$order->state = $order::STATE_CHECKED;
			if( !$order->save() ) {
				$this->setError( array(Yii::t('order','faild change request buy order state')) );
				goto show_error_info;
			}

			$products = tbRequestbuyProduct::model()->findAllByAttributes(array('orderId'=>$order->orderId));
			foreach( $products as $item ) {
				$item->state = $item::STATE_CHECKED;
				$item->save();
			}

			//添加操作日志
			tbRequestbuyOp::addOp( $order->orderId,'pass' );

			Yii::app()->session->add('alertSuccess',true);
			$msg = new AjaxData(true);
			echo $msg->toJson();
			$tr->commit();
			Yii::app()->end(200);

			show_error_info:
				$msg = new AjaxData(false,$this->getError());
				echo $msg->toJson();
				$tr->rollback();
				Yii::app()->end(200);
		}

		$optional = $this->fetchOrderData('normal','ASC');

		//渲染模板
		$this->render('validate', array('dataList'=>$optional['orderList'],'pages'=>$optional['pages']));
	}

	/**
	 * 创建请购单产品搜索
	 * @access hidden
	 */
	public function actionFetch() {
		$quest    = Yii::app()->request->getParam('product');
		$criteria = new CDbCriteria();
		$criteria->addSearchCondition( 't.serialNumber', $quest, true );
		$criteria->limit = tbConfig::model()->get('search_tip_size');
		$data = new AjaxData(true);

		if( empty($quest) || !$quest ) {
			$data->state = false;
			echo $data->toJson();
			Yii::app()->end( 200 );
		}

		$result = tbProductStock::model()->with('product')->findAll( $criteria );
		if( $result ) {
			$products = array();
			foreach( $result as $item ) {
				//单位
				if(is_null($item->product)) {
					$item->product = new tbUnit();
					$item->product->unitId = 1;
				}
				//颜色值
				$spec = explode(':', $item->relation);
				$color = tbSpecvalue::model()->findByPk( $spec[1] );
				if( is_null($color) ) {
					$data->state = false;
					$data->message = 'Not found record';
					echo $data->toJson();
					Yii::app()->end( 200 );
				}

				$detail = array(
					'id' => $item->stockId,
					'productid' => $item->productId,
					'title' => $item->serialNumber,
					'unit' => tbUnit::getUnitName( $item->product->unitId ),
					'color' => $color->title
				);
				array_push( $products, $detail );
			}
			$data->data = $products;
		}
		else {
			$data->state = false;
		}

		echo $data->toJson();
	}


	/**
	 * #######################################
	 * ## 以下代码为内部方法，不可通过url进行访问  ##
	 * #######################################
	 */

	/**
	 * 向数据库中写请购单
	 */
	private function saveRequestOrder() {
		$form = Yii::app()->request->getPost( 'form' );
		//orderId不存在$form里说明该订单为新订单，则否打开订单进行修改
		if( array_key_exists('orderId', $form) ) {
			$order = new RequestOrder( $form['orderId'] );
			$order->pushClean();
		} else {
			$order = new RequestOrder();
		}

		$order->userId = Yii::app()->user->id;
		$order->typeId = tbRequestbuy::FORM_COMPANY;
		$order->setAttributes( Yii::app()->request->getPost("form") );


		if( $order->save() ) {
			$this->dealSuccess( $this->createUrl( 'list' ) );
		}else {
			$errors = $order->getErrors();
			$this->dealError( $errors );
		}
	}

	/**
	 * 获取列表数据
	 * @param string $stateType 数据类型 normal待审核数据,checked待采购数据
	 * @param string $order     列表的排序方式
	 * @todo
	 * 因为会包含产品编号的搜索，所以首先通过请购明细表查寻出订单的编号，再通过
	 * 订单的编号查寻出订单的信息以及订单的明细信息
	 */
	private function fetchOrderData( $stateType, $order='DESC' ) {
		//处理搜索请求
		$singleNumber = trim ( Yii::app()->request->getQuery('s') );
		$orderId      = trim ( Yii::app()->request->getQuery('o') );

		$criteria = new CDbCriteria();

		switch ($stateType) {
			case 'normal':
				//待审核采购单
				$criteria->condition = "state=".tbRequestbuy::STATE_NORMAL;
				break;

			case 'checked':
				//待采购请购单
				$criteria->condition = "state=".tbRequestbuy::STATE_CHECKED;
				break;

			default:
				//默认列表查看
				$criteria->condition = "state!=".tbRequestbuy::STATE_DELETE;
				break;
		}

		if( !empty($singleNumber) ) {
			$criteria->addColumnCondition(array('singleNumber'=>$singleNumber));
		}

		if( !empty($orderId) ) {
			$criteria->addColumnCondition(array('orderId'=>$orderId));
		}

		$count = tbRequestbuyProduct::model()->count( $criteria );
		$pages = new CPagination( $count );
		$pages->setPageSize( (int)tbConfig::model()->get('page_size') );
		$pages->applyLimit( $criteria );
		if( $count > 0) {
			$criteria->select = "orderId";

			$command = Yii::app()->getDb()->getCommandBuilder()->createFindCommand('db_request_buy_product', $criteria);
			$result = $command->queryAll();

			$orderIds = array();
			foreach( $result as $item ) {
				array_push( $orderIds, $item['orderId'] );
			}

			$criteria = new CDbCriteria();
			$criteria->order = "createTime {$order}";
			$orderList = tbRequestbuy::model()->findAllByPk( $orderIds, $criteria );
			return array('orderList'=>$orderList,'pages'=>$pages);
		}
		return array('orderList'=>array(),'pages'=>$pages);
	}
}
<?php
/**
 * 仓库入库单管理
 * @access 仓库入库单管理
 * @author liang
 * @package Controller
 * @version 0.2
 * @modify weihua.Lu
 * @date 2015-12-10
 *
 */
class DefaultController extends Controller {

	/**
	 * 待入库订单
	 * @access 待入库订单
	 */
	public function actionIndex() {
		$data['f'] =  Yii::app()->request->getQuery('f');
		$data['id'] =  Yii::app()->request->getQuery('id');
		$data['p'] =  Yii::app()->request->getQuery('p');

		$ids = tbOrderPost2::search($data['f'], $data['id'], $data['p']);
		$c = new CDbCriteria();
		$c->condition = "state=:state";
		$c->params = array(':state'=>tbOrderPost2::STATE_INSTORE);
		$c->order = "createTime ASC";
		if( $ids ) {
			$c->addInCondition('postId', $ids);
		}

		$pages = new CPagination( tbOrderPost2::model()->count($c) );
		$pages->setPageSize( (int)tbConfig::model()->get('page_size') );
		$pages->applyLimit( $c );

		$orderList = tbOrderPost2::model()->findAll( $c );

		$data['pages'] = $pages;
		$data['orders'] = $orderList;

		$this->render( 'index' ,$data );
	}

	/**
	 * 入库单管理
	 * @access 入库单管理
	 */
	public function actionList() {
		$data['f'] =  Yii::app()->request->getQuery('f');
		$data['id'] =  Yii::app()->request->getQuery('id');
		$data['p'] =  Yii::app()->request->getQuery('p');

		$c = new CDbCriteria();
		$c->addInCondition('source',[tbWarehouseWarrant::FROM_ORDER,tbWarehouseWarrant::FORM_ADDNEW]);
		$c->addInCondition('state',[tbWarehouseWarrant::STATE_NORMAL,tbWarehouseWarrant::STATE_APPLY]);
		$c->order = "createTime DESC";
		if( $data['f'] ){
			$c->compare('t.factoryNumber',$data['f']);
		}

		$pages = new CPagination( tbWarehouseWarrant::model()->count($c) );
		$pages->setPageSize( tbConfig::model()->get('page_size') );
		$pages->applyLimit( $c );

		$orderList = tbWarehouseWarrant::model()->findAll( $c );

		$data['pages']  = $pages;
		$data['list'] = $orderList;

		$this->render( 'list' ,$data );
	}

	/**
	 * 新建入库订单
	 * @access 新建入库单
	 */
	public function actionAddnew() {
		if( Yii::app()->request->getIsPostRequest() || Yii::app()->request->getIsAjaxRequest() ) {
			$this->addNewWarrant();
		}
		$this->render('addnew', [
				'order'=>new OrderInfo(Yii::app()->request->getPost('form')),

		]);
	}


	/**
	 * 编辑入库单
	 * @access 编辑入库单
	 */
	public function actionEdit() {
		$id = Yii::app()->request->getQuery('id');
		if( is_null($id) || !is_numeric($id) || ($model = tbWarehouseWarrant::model()->with('detail')->findByPk( $id ))==null ) {
			throw new CHttpException( '404', 'Not found record' );
		}
		if( Yii::app()->request->isPostRequest ) {
			$form = new WarehouseWarrantForm();
			$form->attributes = Yii::app()->request->getPost('data');
			if( $form->edit( $model ) ) {
				if(!$url = urldecode(Yii::app()->request->getQuery('from'))){
					$url = $this->createUrl('list');
				}
				$this->dealSuccess( $url );
			}else{
				$this->dealError( $form->getErrors() );
			}
		}
		$data = $model->attributes;
		foreach ( $model->detail as $detail ){
			if($detail->num>0){
				$data['products'][$detail->id] = $detail->attributes;
			}
		}
		$this->render( 'edit',array('data'=>$data ) );
	}

	/**
	 * 查看入库单
	 * @access 查看入库单
	 */
	public function actionView() {
		$id = Yii::app()->request->getQuery('id');
		if( is_null($id) || !is_numeric($id) || ($model = tbWarehouseWarrant::model()->with('posts')->findByPk( $id ))==null ) {
			throw new CHttpException( '404', 'Not found record' );
		}

		$data = $model->attributes;
		if( $model->posts ) {
			$data['postInfo'] = $model->posts->attributes;
		}
		else {
			$data['postInfo'] = new tbOrderPost2();
		}
		$data['products'] = tbWarehouseWarrantDetail::model()->findAllByWarrant( $id );

		$this->render( 'view',array('data'=>$data ) );
	}

	/**
	 * 添加撤消申请
	 * @access 撤消入库申请
	 */
	public function actionRepeal() {
		$id = Yii::app()->request->getQuery('id');
		$warrant = tbWarehouseWarrant::model()->with('posts')->findByPk($id);
		if(!$warrant) {
			$this->redirect( $this->createUrl('list') );
		}

		if (Yii::app()->request->getIsPostRequest()) {
			$repeal             = new tbWarrantRepeal;
			$repeal->attributes = [
				'state'     => tbWarrantRepeal::STATE_NEW,
				'warrantId' => $id ,
				'remark'    => Yii::app()->request->getPost('remark'),
			];

			$transaction = Yii::app()->db->beginTransaction();
			if ( $repeal->save() ) {
				$loger             = new tbWarrantRepealLog;
				$loger->attributes = [
					'repealId' => $repeal->repealId,
					'action'   => tbWarrantRepealLog::ACTION_NEW,
				];

				if ($loger->save()) {
					// 变更入库单状态
					$warrant->state = tbWarehouseWarrant::STATE_APPLY;
					$warrant->save();

					$transaction->commit();
					$this->dealSuccess( $this->createUrl('repeal') );
				} else {
					$transaction->rollback();
					$errors = $loger->getErrors();
					$this->dealError( $errors );
				}

			}else{
				$transaction->rollback();
				$errors = $repeal->getErrors();
				$this->dealError( $errors );
			}
		}
		return $this->render('repeal', ['warrant'=>$warrant]);
	}

	/**
	 * 创建入库单
	 * @access 创建入库单
	 */
	public function actionImport() {

		if( Yii::app()->request->getIsPostRequest() ) {
			$this->createImportOrder();
		}

		$id        = Yii::app()->request->getQuery('id');
		$postInfo = tbOrderPost2::model()->findByPk( $id );
		if( is_null($postInfo) ) {
			throw new CHttpException( 404, Yii::t('order','Not found record') );
		}
		$products  = $postInfo->getProducts();
		$orderInfo = tbOrderPurchasing::model()->findByPk( $postInfo->purchaseId );

		$this->render( 'import', array('post'=>$postInfo, 'order'=>$orderInfo, 'products'=>$products) );
	}

	private function addNewWarrant() {

	   	$products = Yii::app()->request->getPost('product');
        $trans = Yii::app()->getDb()->beginTransaction();
        try {
        	 //只能选择同一仓库下的仓位
         foreach ($products as $key => $value) {
        	 	     $warehouse[$key] = tbWarehousePosition::model()->getWarehouseID($value['positionId']);
        	 	     foreach ($warehouse as $ke => $val) {
        	 	     	 		if($warehouse[0] != $val){
												throw new CException( '请选择同一仓库下的仓位');
											}
        	 	     }
        	 }
            if(!$this->_save_newWarrant($warrant)) {
            	$errors = $warrant->getErrors();
            	$error = array_shift($errors);
                throw new CException( $error[0] );
            }

            foreach( $products as $row ) {
                //保存入库单明细
                if( !$this->_save_newWarrant_detail($warrant->warrantId, $row, $warrantPro) ) {
                    throw new CException( Yii::t('Failed save warrant detail information') );
                }
            }

            $trans->commit();
            $this->dealSuccess( $this->createUrl('index') );
        }
        catch( CException $err ) {
            $trans->rollback();
            $this->dealError( array($err->getMessage()) );
        }
	}

	/**
	 * 创建入库单
	 */
	private function createImportOrder() {
		$postId   = Yii::app()->request->getPost('postId');
		$products = Yii::app()->request->getPost('product');

		$orderPost = tbOrderPost2::model()->findByPk( $postId );
		if( is_null($orderPost) ) {
			$this->setError( array(Yii::t('order','Not found post Record')) );
			return false;
		}

		try{
			$trans = Yii::app()->getDb()->beginTransaction();

			//保存入库单
			if( !$this->_save_warrant( $orderPost, $warrant ) ) {
				$errors = $warrant->getErrors();
				$error = array_shift($errors);
				throw new CException( $error[0] );
			}

			foreach( $products as $row ) {
				//保存入库单明细
				if( !$this->_save_warrant_detail($warrant->warrantId, $row, $warrantPro) ) {
					throw new CException( Yii::t('order','Failed save warrant detail information') );
				}
			}

			//更新发货单状态
			$orderPost->state = $orderPost::STATE_FINISHED;
			if( !$orderPost->save() ) {
				throw new CException( Yii::t('order','Failed change post state') );
			}

			$trans->commit();

			$this->dealSuccess( $this->createUrl('index' ) );
		}
		catch( CException $err ) {
			$trans->rollback();
			$this->dealError( array( array( $err->getMessage() ) ) );
		}
	}

	/**
	 * 保存入库单信息
	 * @param tbOrderPost2 $post
	 * @param tbWarehouseWarrant $warrant 指向入库单的引用
	 * @return bool
	 */
	private function _save_warrant( tbOrderPost2 $post, & $warrant ) {

		$warrant    = new tbWarehouseWarrant();
		$purchasing = $post->getPurchasing();

		$warrant->factoryNumber = $purchasing->supplierSerial;
		$warrant->factoryName   = $purchasing->supplierName;
		$warrant->contactName   = $purchasing->supplierContact;
		$warrant->phone         = $purchasing->supplierPhone;
		$warrant->address       = $purchasing->address;
		$warrant->remark        = $purchasing->comment;
		$warrant->source        = $warrant::FROM_ORDER;
		$warrant->postId        = $post->postId;

		return $warrant->save();
	}

    /**
     * 保存自建入库单信息
     * @param tbWarehouseWarrant $warrant
     * @return bool
     */
	private function _save_newWarrant( & $warrant ) {
		$form = new OrderInfo(Yii::app()->request->getPost('form'));
		$warrant = new tbWarehouseWarrant();
		$warrant->factoryNumber = $form->id;
		$warrant->factoryName   = $form->name;
		$warrant->contactName   = $form->contact;
		$warrant->phone         = $form->phone;
		$warrant->address       = $form->address;
		$warrant->remark        = $form->comment;
		$warrant->postId        = $form->postId;
		$warrant->source        = tbWarehouseWarrant::FORM_ADDNEW;

		return $warrant->save();
	}

    /**
     * 保存自建入库单明细信息
     * @param $warrantId
     * @param $productInfo
     * @param $warrantPro
     * @return bool
     * @throws CException
     */
    private function _save_newWarrant_detail($warrantId, $productInfo, & $warrantPro) {
        $warrantPro = new tbWarehouseWarrantDetail();
        $product    = tbProductStock::model()->findByAttributes( array('singleNumber'=>$productInfo['productCode']));
        if( is_null($product) ) {
            throw new CException(Yii::t('order','Not found product record'));
        }

        $color = $product->getColor();
        if(is_null($color)) {
        	throw new CException(Yii::t('order','Not found product color by {serial}',array('{serial}'=>$productInfo['productCode'])));
        }

        $warrantPro->positionId   = $productInfo['positionId'];
        $warrantPro->total        = $productInfo['total'];
      //  $warrantPro->batch        = $productInfo['batch'];
		$warrantPro->batch        = tbWarehouseProduct::DEATULE_BATCH;//产品批次暂时先不让输入，统一设为默认值：
        $warrantPro->orderId      = 0;
        $warrantPro->singleNumber = $productInfo['productCode'];
        $warrantPro->color        = $product->getColor();
        $warrantPro->warrantId    = $warrantId;

		if( !$warrantPro->validate() ) {
			$errors = $warrantPro->getErrors();
			$field  = array_shift($errors);
			throw new CException($field[0]);
		}

        return $warrantPro->save();
    }


	/**
	 * 保存入库单明细信息
	 * @param integer $warrantId 入库单编号
	 * @param array $productInfo 入库产品信息 array('positionId'=>0,'total'=>0,'batch'=>0,'detailId'=>0)
	 * @param tbWarehouseWarrantDetail $warrantPro 指向入库单详情的引用
	 * @throws CException
	 * @return bool
	 */
	private function _save_warrant_detail( $warrantId, $productInfo, & $warrantPro ) {
		$postPro = tbOrderPost2Product::model()->findByPk( $productInfo['detailId'] );
		if( !$postPro ) {
			throw new CException(404, Yii::t('order','Not found post record'));
		}

		$purchasePro = $postPro->getPurchaseInfo();
		$warrantPro = new tbWarehouseWarrantDetail();
		$warrantPro->positionId   = $productInfo['positionId'];
		$warrantPro->total        = $productInfo['total'];
		//$warrantPro->batch      = $productInfo['batch'];
		$warrantPro->batch        = tbWarehouseProduct::DEATULE_BATCH;//产品批次暂时先不让输入，统一设为默认值：
		$warrantPro->postQuantity = $productInfo['postQuantity'];

		$warrantPro->orderId      = $purchasePro->purchaseId;
		$warrantPro->singleNumber = $purchasePro->productCode;
		$warrantPro->color        = $purchasePro->color;
		$warrantPro->warrantId    = $warrantId;

		if( !$warrantPro->validate() ) {
			$errors = $warrantPro->getErrors();
			$field  = array_shift($errors);
			throw new CException($field[0]);
		}

		$result = $warrantPro->save();
		if( $result ) {
			$assigns = tbOrderPost2Assign::model()->orders($productInfo['detailId']);
			foreach($assigns as $lock) {
				if($lock->isLocked()) {
					tbOrderMessage::addMessage( $lock->orderId,'has_purchase' );
					tbOrderDistribution::addOne( $lock->orderId );
				}
			}
		}

		return $result;
	}


	/**
	 * 生成表单字段名称
	 @param integer $index
	 @param integer $subIndex
	 @param string $name
	 */
	public function fieldName( $index, $subIndex, $name ) {
		return sprintf( 'product[%s][st][%s][%s]', $index, $subIndex, $name );
	}
}

/**
 * 发货单产品明细
 * @author yagas
 *
 */
class PostInfo {
	private $_proInfo;
	private $_assigns;
	private $_assignTotal;

	public function __construct( tbOrderPostProduct $postProduct ) {
		$this->_proInfo = $postProduct;
		$this->_assigns = $postProduct->fetchAssigns();

		if( !$this->_assigns ) {
			$assign = new tbOrderPostAssign();
			$assign->orderbuyProductId = '-';
			$assign->total  = '-';
			$this->_assigns = array( $assign );
		}
	}

	/**
	 * 获取已匹配订单数量
	 * @return integer
	 */
	public function assignTotal() {
		if( !$this->_assignTotal ) {
			$total = 0;
			foreach( $this->_assigns as $item ) {
				$total += $item->total;
			}
			$this->_assignTotal = $total;
		}
		return $this->_assignTotal;
	}

	/**
	 * 获取可入库的产品数量
	 * @return integer
	 */
	public function total() {
		return $this->_proInfo->total - $this->_assignTotal;
	}

	/**
	 * 输出产品列表
	 */
	public function output() {
		ob_start();
		foreach( $this->_assigns as $index=>$assign ) {
			echo "<tr>";
			echo CHtml::tag('td',array(),$assign->orderbuyProductId);
			echo CHtml::tag('td',array(),$assign->total);
			if( $index === 0 ) {
				$rows = count($this->_assigns);
				if( $rows > 1 ) {
					$htmlOption = array( 'rowspan'=>$rows );
				}
				else {
					$htmlOption = array();
				}

				echo CHtml::tag('td',$htmlOption,$this->assignTotal());
				echo CHtml::tag('td',$htmlOption,$this->assignTotal());
				$total = CHtml::tag('span',array('class'=>'text-danger'),$this->total());
				echo CHtml::tag('td',$htmlOption,$total.$this->_proInfo->unitName);
			}
			echo "</tr>";
		}
		$source = ob_get_contents();
		ob_end_clean();
		return $source;
	}
}

class OrderInfo {
	private $id; //厂家编号
	private $name; //厂家名称
	private $contact; //联系人
	private $phone; //联系电话

	private $address; //收货地址
	private $comment; //订单备注
	private $postId; //发货单号
	private $postTime; //发货日期

	public function __construct($values) {
		if(is_array($values)) {
			$this->initialization($values);
		}
	}

	/**
	 * 初始化参数设置
	 * @param array $values
	 */
	private function initialization($values) {
		foreach ($values as $k => $v) {
			if(property_exists($this, $k)) {
				$this->$k = $v;
			}
		}
	}

	public function __get($name) {
		if(property_exists($this, $name)) {
			return $this->$name;
		}
		return '';
	}
}
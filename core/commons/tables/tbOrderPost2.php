<?php
/**
 * 发货单
 * 发货单可由工厂创建，也可由业务员创建
 * @package CActiveRecord
 */
class tbOrderPost2 extends CActiveRecord {

	public $postId;	//发货单编号
	public $orderType;	//0:采购发货 1:工厂发货
	public $userId;	//操作员
	public $state;	//0:正常 1:删除
	public $purchaseId;	//采购单编号
	public $logisticsCode;	//物流单号
	public $logisticsName;	//物流公司
	public $postTime;	//发货时间
	public $createTime;	//订单创建时间

	//未处理：默认状态
	const STATE_NORMAL   = 0;

	//待分配
	const STATE_ASSIGN   = 1;

	//待入库
	const STATE_INSTORE  = 2;

	//已完成
	const STATE_FINISHED = 3;

	const TYPE_USER    = 0;
	const TYPE_FACTORY = 1;


	public static function model( $className=__CLASS__) {
		return parent::model( $className );
	}

	public function init() {
		parent::init();
		$this->createTime = date('Y-m-d H:i:s');
		$this->state      = self::STATE_NORMAL;
		$this->userId     = Yii::app()->getUser()->id;
		$this->orderType  = self::TYPE_USER;
	}

	public function tableName() {
		return '{{order_post2}}';
	}

	public function primaryKey() {
		return 'postId';
	}

	public function rules() {
		return array(
			array('logisticsName,logisticsCode,postTime,orderType,purchaseId','required'),
		);
	}

	public function relations() {
		return [
			'products' => array(self::HAS_MANY, 'tbOrderPost2Product', 'postId'),
			'purchase' => array(self::BELONGS_TO, 'tbOrderPurchasing', 'purchaseId'),
			'user' => array(self::BELONGS_TO, 'tbUser', 'userId'),
		];
	}

	public function getProducts() {
		$result = tbOrderPost2Product::model()->with('details')->findAllByAttributes(array('postId'=>$this->postId));
		return $result;
	}

	/**
	 * 获取采购订单信息
	 * @throws CHttpException
	 */
	public function getPurchasing() {
		if( !$this->isNewRecord && !empty($this->postId) ) {
			$purchasing = tbOrderPurchasing::model()->findByPk( $this->purchaseId );
			if( !is_null($purchasing) ) {
				return $purchasing;
			}
		}
		throw new CHttpException( 500, Yii::t('order','Not found record') );
	}

	public function attributeLabels()
	{
		return [
			'logisticsCode' => '物流单号',
			'logisticsName' => '物流公司',
			'postTime'      => '发货时间',
			'orderType'     => '发货方',
			'purchaseId'    => '采购单编号'
		];
	}

	/**
	 * 检测订单是否已全部匹配
	 * 将全部匹配的订单添加到待入库列表
	 * @param integer $postId
	 * @throws CHttpException
	 */
	public function toAssign() {
		$products = $this->countProducts();
		$assigned = (int)tbOrderPost2Product::model()->countByAttributes(array('postId'=>$this->postId,'isAssign'=>tbOrderPost2Product::STATE_ASSIGN));

		if( $products === $assigned ) {
			$this->state = self::STATE_INSTORE;

			if( !$this->save() ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * 获取发货单下产品条数
	 * @return int
	 */
	public function countProducts() {
		return (int)tbOrderPost2Product::model()->countByAttributes(array('postId'=>$this->postId));
	}

	/**
	 * 发货单搜索功能
	 * @return array $condition 搜索条件
	 * @param CPagination $pages  分页对象
	 * @param string $order		 指定排序方式，非指定用默认
	 * @throws CDbException
	 */
	public function postList( $condition = array(), &$pages, $order = null ) {
		$criteria = new CDbCriteria();
		if( is_array( $condition ) ){
			foreach ( $condition as $key=>$val ){
				if( empty($val) ) continue;

				if( $key == 'productCode'){
					$criteria->join = "inner join {{index_post}} ip on ip.`postId` = t.`postId` and ip.productCode = '$val'";
				}else{
					$criteria->compare('t.'.$key,$val);
				}
			}
		}

		$criteria->order = empty($order)?" t.postId DESC":$order;

		$count = $this->count( $criteria );
		$pages  = new CPagination();
		$pages->setPageSize((int)tbConfig::model()->get('page_size'));
		$pages->setItemCount( $count );

		return $this->findAll($criteria);
	}

	/**
	 * 后台管理列表订单搜索
	 * @param string $supplierCode
	 * @param integer $purchaseId
	 * @param string $productCode
	 * @return array
	 */
	public static function search( $supplierCode, $purchaseId, $productCode ) {
		$tPostPro = tbOrderPost2Product::model()->tableName();
		$tPost    = tbOrderPost2::model()->tableName();
		$tpurchasePro = tbOrderPurchasingProduct::model()->tableName();

		$where = array();
		if( $supplierCode ) {
			array_push( $where, "supplierCode=:supplierCode");
		}

		if( $purchaseId ) {
			array_push( $where, "Pro.purchaseId=:purchaseId");
		}

		if( $productCode ) {
			array_push( $where, "productCode=:productCode");
		}

		$sql = "SELECT Pro.postId FROM {$tPostPro} Pro LEFT JOIN {$tPost} Pos USING (postId) LEFT JOIN {$tpurchasePro} Pur USING (purchaseProId) where Pos.state=:state %s group by Pro.postId";
		if( $where ) {
			$where = ' and ' . implode( ' and ', $where );
		}
		else {
			$where = '';
		}
		$sql = sprintf( $sql, $where );

		$cmd = Yii::app()->getDb()->createCommand( $sql );
		$cmd->bindValue(':state', tbOrderPost2::STATE_INSTORE);

		if( $supplierCode ) {
			$cmd->bindValue(':supplierCode', $supplierCode, PDO::PARAM_STR);
		}
		if( $purchaseId ) {
			$cmd->bindValue(':purchaseId', $purchaseId, PDO::PARAM_INT);
		}
		if( $productCode ) {
			$cmd->bindValue(':productCode', $productCode, PDO::PARAM_STR);
		}

		$ids = $cmd->queryColumn();
		return $ids;
	}

	/**
	 * 查询订单记录
	 * @param null $productCode
	 * @param null $PostId
	 * @return static[]
	 * @throws CDbException
	 */
	public function FactorySearch($productCode=null,$PostId=null) {
		if(empty($productCode))
			$productCode = null;

		if(empty($PostId))
			$PostId = null;

		$total = $this->FactorySearchCount($productCode,$PostId);

		if(is_null($productCode) && is_null($PostId)) {
			$critail = new CDbCriteria();
			$critail->order = "createTime DESC";
// 			$critail->condition = "state!=0";
			$pages = new CPagination( $total );
			$pages->setPageSize( tbConfig::model()->get('page_size') );
			$pages->applyLimit($critail);

			return $this->findAll($critail);
		}

		if(is_null($productCode) && !is_null($PostId)) {
			$pages = new CPagination( $this->FactorySearchCount(null,$PostId) );
			$pages->setPageSize( tbConfig::model()->get('page_size') );

			$critail = new CDbCriteria();
			$critail->condition = "state=".self::STATE_POSTED;
			$critail->order = "createTime DESC";
			$critail->addColumnCondition(array('postId'=>$PostId));
			$pages->applyLimit($critail);
			return $this->findAllByPk($PostId);
		}

		$tbPost     = tbOrderPost2::model()->tableName();
		$tbPostPro  = tbOrderPost2Product::model()->tableName();
		$tbOrderPurchase = tbOrderPurchasingProduct::model()->tableName();
		$page = intval(Yii::app()->request->getQuery("page",0));
		$pageSize = (int)tbConfig::model()->get('page_size');

		$cmd = $this->getDbConnection()->createCommand();
		$cmd->select("post.postId");
		$cmd->from("{$tbPostPro} as post");
		$cmd->leftJoin("{$tbOrderPurchase} as purchase"," post.purchaseProId=purchase.purchaseProId");
		$cmd->leftJoin("{$tbPost} as order","order.postId=post.postId");
// 		$cmd->where("order.state=".self::STATE_POSTED);
		$cmd->limit($pageSize, $page*$pageSize);

		if(!empty($productCode)) {
			$cmd->andWhere("purchase.productCode=:productCode");
			$cmd->bindValue(':productCode',$productCode, PDO::PARAM_STR);
		}

		if(!empty($PostId)) {
			$cmd->andWhere("post.postId=:postId");
			$cmd->bindValue(':postId',$PostId, PDO::PARAM_INT);
		}

		$result = $cmd->queryAll();
		$ids = array_map(function($row){
			return $row['postId'];
		}, $result);
		return tbOrderPost2::model()->findAllByPk($ids);
	}

	/**
	 * 统计订单启记录
	 * @param null $productCode
	 * @param null $PostId
	 * @return int|string
	 * @throws CDbException
	 */
	public function FactorySearchCount($productCode=null,$PostId=null) {
		if(empty($productCode))
			$productCode = null;

		if(empty($PostId))
			$PostId = null;

		if(is_null($productCode) && is_null($PostId)) {
			return $this->countByAttributes(array("state"=>0));
		}

		if(is_null($productCode) && !is_null($PostId)) {
			return $this->countByAttributes(array('postId'=>$PostId,"state"=>0));
		}

		$tbPost     = tbOrderPost2::model()->tableName();
		$tbPostPro  = tbOrderPost2Product::model()->tableName();
		$tbOrderPurchase = tbOrderPurchasingProduct::model()->tableName();
		$page = intval(Yii::app()->request->getQuery("page",0));
		$pageSize = (int)tbConfig::model()->get('page_size');

		$cmd = $this->getDbConnection()->createCommand();
		$cmd->select("count(post.postId)");
		$cmd->from("{$tbPostPro} as post");
		$cmd->leftJoin("{$tbOrderPurchase} as purchase"," post.purchaseProId=purchase.purchaseProId");
		$cmd->leftJoin("{$tbPost} as order", "order.postId=post.postId");
		$cmd->where("order.state=0");
		$cmd->limit($pageSize, $page*$pageSize);

		if(!empty($productCode)) {
			$cmd->andWhere("purchase.productCode=:productCode");
			$cmd->bindValue(':productCode',$productCode, PDO::PARAM_STR);
		}

		if(!empty($PostId)) {
			$cmd->andWhere("post.postId=:postId");
			$cmd->bindValue(':postId',$PostId, PDO::PARAM_INT);
		}

		$result = $cmd->queryColumn();
		return (int)$result[0];
	}

	/**
     * 创建发货单
     * 启动事物，创建发货单
     * 创建发货单明细订单记录
     * 发货单及发货单明细写入成功，提事物
     * 如果发货单或发货单明细写入失败，事物回滚
     *
	 * @param CActiveRecord $orderType 要发货的采购单
	 * @param integer $orderType 发货方
	 * @throws CDbException
     */
    public function createPost( $purchase,$orderType ) {
		if( !$this->isNewRecord ) {
			return false;
		}
		$form    = Yii::app()->request->getPost('form');
        unset($form['logisticId']);
		$product = Yii::app()->request->getPost('product');
		if( !is_array(  $product )  || empty (  $product ) ){
			$this->addError ( 'product', '发货产品不能为空' );
			return false;
		}

        //创建发货单记录
		$this->setAttributes( $form );

		$this->purchaseId = $purchase->purchaseId;
		$this->orderType =  $orderType;

		//启动事物处理
        $tr = Yii::app()->db->beginTransaction();
        //保存发货单记录
        if( $this->save() ) {
            //循环创建发货单明细记录
            foreach($product as $pid=>$item) {
                $purchasePro = tbOrderPurchasingProduct::model()->findByPk( $pid );
                if(is_null($purchasePro)) {
                    //无法找到采购的产品记录明细，回滚事物并提示错误信息
                    $this->addError( 'product', Yii::t('order','Not found record') );
					goto roll_back;
                }

                $postPro = new tbOrderPost2Product();
                $postPro->setAttributes(['postId'=>$this->postId, 'purchaseId'=>$purchasePro->purchaseId, 'purchaseProId'=>$purchasePro->purchaseProId,'postTotal'=>$item]);
                $postPro->comment = '';

                if(!$postPro->save()) {
					//写入发货单明细记录失败，回滚事物并提示错误信息
					$this->addErrors ( $postPro->getErrors() );
					goto roll_back;
                }
            }

            //无写入错误则提交事物，并跳转页面到列表页面
            $tr->commit();
			return true;
		}


		roll_back:
			$tr->rollback();
			return false;

    }
}
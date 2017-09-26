<?php
class tbOrderPostProduct extends CActiveRecord {
	
	public $id;
	public $postId;
	public $orderbuyId;
	public $singleNumber;
	public $total;
	public $unitName;
	public $color;
	public $comment='';
	public $isAssign;
	
	private $_assignProducts;
	private $_info;
	
	public static function model( $className=__CLASS__) {
		return parent::model( $className );
	}
	
	public function tableName() {
		return '{{order_post_product}}';
	}
	
	public function primaryKey() {
		return 'id';
	}
	
	public function rules() {
		return array(
			array('postId,orderbuyId','required'),
			array('postId,total','numerical','min'=>1),
			array('singleNumber,total,unitName,comment,color','safe')
		);
	}
	
	public function init() {
		parent::init();
		$this->id = ZOrderHelper::getOrderId();
		$this->isAssign = 0;
	}
	
	public function getUnitName() {
		return ZOrderHelper::getUnitName( $this->singleNumber);
	}
	
	public function getAssign() {
		if( is_null($this->_assignProducts) ) {
			$criterai = new CDbCriteria();
			$criterai->condition = "postProductId=:id and singleNumber=:single";
			$criterai->params = array(':id'=>$this->id,':single'=>$this->singleNumber);
			$this->_assignProducts = tbOrderPostAssign::model()->findAll($criterai);
		}
		
		if( $this->_assignProducts ) {
			$total = 0;
			foreach( $this->_assignProducts as $item ) {
				$total += $item->total;
			}
			$assignCount = $total;
		}
		else {
			$assignCount = 0;
		}
		return $assignCount;
	}
	
	public function isAssign() {
		$products = $this->getAssign();
		if( $products > 0 ) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public function relations() {
		return array(
			'orderPost' => array(self::BELONGS_TO, 'tbOrderPost', '', 'on'=>'orderPost.postId=t.postId')
		);
	}
	
	/**
	 * 订单是否已进行匹配
	 * @param string $orderId
	 * @return boolean
	 */
	public function assign( $orderId ) {
		$product = tbOrderPostAssign::model()->findByAttributes(array('postProductId'=>$this->id,'orderbuyProductId'=>$orderId));
		if( $product instanceof CActiveRecord ) {
			return true;
		}
		return false;
	}
	
	public function fetchAssigns() {
		$products = tbOrderPostAssign::model()->findAllByAttributes( array('postProductId'=>$this->id) );
		return $products;
	}
	
	/**
	 * 订单信息
	 */
	private function fetchRelation() {
		if( is_null($this->_info) ) {
			$orderInfo   = tbOrderPost::model ()->with ( 'order' )->findByPk ( $this->postId );
			$productInfo = tbOrderbuyProduct::model ()->findByAttributes ( array (
					'orderId'      => $orderInfo->orderId,
					'singleNumber' => $this->singleNumber
			) );
			$this->_info = array( 'orderInfo'=>$orderInfo, 'productInfo'=>$productInfo );
		}
		return $this->_info;
	}
	
	/**
	 * 革厂商品编码
	 */
	public function getCorpProductNumber() {
		$result = $this->fetchRelation();
		return $result['productInfo']->corpProductNumber;
	}
	
	/**
	 * 产品采购量
	 */
	public function getBuyTotal() {
		$result = $this->fetchRelation();
		return $result['productInfo']->total;
	}
	
	/**
	 * 采购单编号
	 * @throws CHttpException
	 * @return string
	 */
	public function getOrderId() {
		$post = tbOrderPost::model()->findByPk( $this->postId );
		if( !$post ) {
			throw new CHttpException( 404, 'Not found Data' );
		}
		return $post->orderId;
	}
	
	/**
	 * 通过发货单编号查找采购信息
	 * @param integer $postId 发货单号
	 */
	public function findAllOrderIds($postId) {
		$source = tbOrderPurchase::FROM_ORDER;
		$sql = "SELECT fromOrderId FROM db_order_post_product A LEFT JOIN db_order_buy_relate B ON A.`orderbuyId`=B.`orderId` 
WHERE A.postId=:postId and source={$source} GROUP BY fromOrderId";
		$cmd = $this->getDbConnection()->createCommand( $sql );
		$cmd->bindValue(':postId', $postId, PDO::PARAM_STR);
		$result = $cmd->queryAll();
		if( !$result ) {
			return false;
		}
		return $result;
	}
}
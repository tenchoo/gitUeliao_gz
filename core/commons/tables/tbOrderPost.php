<?php

/**
 * 发货单
 * 发货单可由工厂创建，也可由业务员创建
 * @package CActiveRecord
 */
class tbOrderPost extends CActiveRecord {
	
	public $postId;
	public $orderId;
	public $state;
	public $userId;
	public $orderType;
	public $createTime;
	public $postTime;
	public $logisticId;
	public $logisticName;
	public $logisticNumber;
	public $comment='';

	//未处理：默认状态
	const STATE_NORMAL   = 0;

	//已发货
	const STATE_POSTED   = 1;

	//待分配
	const STATE_ASSIGN   = 2;

	//待入库
	const STATE_INSTORE  = 3;

	//已完成
	const STATE_FINISHED = 4;
	
	const TYPE_USER    = 0;
	const TYPE_FACTORY = 1;

	
	public static function model( $className=__CLASS__) {
		return parent::model( $className );
	}
	
	public function init() {
		parent::init();
		$this->createTime = time();
		$this->postId     = ZOrderHelper::getOrderId();
		$this->state      = self::STATE_NORMAL;
		$this->userId     = Yii::app()->getUser()->id;
	}
	
	public function tableName() {
		return '{{order_post}}';
	}
	
	public function primaryKey() {
		return 'postId';
	}
	
	public function getProducts() {
		return $this->_products;
	}
	
	public function setProducts($values) {
		$this->_products = $values;
	}
	
	public function rules() {
		return array(
			array('state,userId,postTime,logisticId,logisticName,logisticNumber,orderId','required'),
			array('state,userId,postTime,logisticId','numerical')
		);
	}
	
	public function relations() {
		return array(
			'products' => array( self::HAS_MANY, 'tbOrderPostProduct', 'postId' ),
			'order' => array( self::BELONGS_TO, 'tbOrderbuy', '', 'on'=> 't.orderId=order.orderId' ),
		);
	}
	
	/**
	 * 获取产品列表
	 * @param string $criteria
	 * @param array $params
	 * @return array
	 */
	public function fetchPostList( $criteria='',$params=array() ) {
		$result = $this->with('products')->findAll( $criteria, $params );
		return $result;
	}
	
	public static function checkAccess( $postId ) {
		$postDetail = tbOrderPostProduct::model()->findByPk($postId);		
		if( is_null($postDetail) ) {
			return false;
		}
		
		$criteria = new CDbCriteria();
		$criteria->condition = "postId=:id";
		$criteria->params = array(':id'=>$postDetail->postId);
		$postTotal = tbOrderPostProduct::model()->count( $criteria );
		
		
		$criteria->condition = "postId=:id and isAssign=1";
		$assignTotal = tbOrderPostProduct::model()->count( $criteria );		
		
		if( $postTotal==$assignTotal ) {
			$post = tbOrderPost::model()->findByPk( $postDetail->postId );
			if( $post ) {
				$post->state = tbOrderPost::STATE_INSTORE;
				$post->save();
			}
		}
	}
}
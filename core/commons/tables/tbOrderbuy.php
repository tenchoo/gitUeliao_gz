<?php
/**
 * 采购单
 * @author yagas
 * @package CActiveRecord
 */
class tbOrderbuy extends CActiveRecord {
	
	public $orderId;
	public $state;
	public $userId;
	public $createTime;
	public $updateTime;
	public $phone;
	public $factoryNumber;
	public $contacts;
	public $factoryName;
	public $address;
	public $comment;
	public $memberId;
	
	private $_products = array();
	
	const STATE_NORMAL   = 0;
	const STATE_DELETE   = 1;
	const STATE_CHECKED  = 2;
	const STATE_FINISHED = 3;
	const STATE_CLOSE    = 4;
	
	public function tableName() {
		return '{{order_buy}}';
	}
	
	public static function model( $className=__CLASS__ ) {
		return parent::model( $className );
	}
	
	public function rules() {
		return array(
			array('memberId,phone,contacts,address,factoryName,factoryNumber','required'),
			array('comment','safe')
		);
	}
	
	public function beforeSave() {
		if( $this->getScenario() === 'update' ) {
			$this->updateTime = time();
		}

		return parent::beforeSave();
	}
	
	public function init() {
		$this->userId     = Yii::app()->getUser()->id;
		$this->orderId    = ZOrderHelper::getOrderId();
		$this->state      = self::STATE_NORMAL;
		$this->createTime = time();
		$this->updateTime = $this->createTime;
	}
	
	public function relations() {
		return array(
			'products' => array( self::HAS_MANY, 'tbOrderbuyProduct', 'orderId' ),
			'user' => array(self::HAS_ONE, 'tbUser',array('userId'=>'memberId'))
		);
	}
	
	public function products( $products=null ) {
		if( !is_null($products) ) {
			$this->_products = $products;
		}
		else {
			return $this->_products;
		}
	}
	
	public function findAll( $condition='', $params=array() ) {
		$result = parent::findAll( $condition, $params );
		foreach( $result as &$item ) {
			$products = tbOrderbuyProduct::model()->findAll( "orderId=:id", array(':id'=>$item->orderId) );
			$item->products( $products );
		}		
		return $result;
	}
}
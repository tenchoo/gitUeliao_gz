<?php
/**
 * 请购单数据库表
 * @author yagas
 * @package CActiveRecord
 */
class tbRequestbuy extends CActiveRecord {
	
	public $orderId;
	public $userId;
	public $userName;
	public $cause;
	public $comment;
	public $createTime;
	public $updateTime;
	public $state;
	public $typeId;
	
	const STATE_NORMAL      = 0;	
	const STATE_CHECKED     = 1;
	const STATE_WAITING     = 2;
	const STATE_PROCCESSING = 3;
	const STATE_FINISHED    = 4;
	const STATE_CLOSE       = 5;
	const STATE_DELETE      = 6;
	
	//内部请购
	const FORM_COMPANY = 0;	
	//低安全库存
	const FORM_STORE   = 1;	
	//客户订购
	const FORM_ORDER   = 2;
	
	public function init() {
		parent::init();
		$this->state  = self::STATE_NORMAL;
		$this->createTime = $this->updateTime = time();
		$this->userId = Yii::app()->getUser()->id;
	}
	
	public static function model( $className=__CLASS__ ) {
		return parent::model( $className );
	}
	
	public function tableName() {
		return '{{request_buy}}';
	}
	
	public function primaryKey() {
		return 'orderId';
	}
	
	public function rules() {
		return array(
			array('userId,userName,cause,typeId','required'),
			array('updateTime,state,typeId','numerical'),
			array('comment','safe')
		);
	}
	
	public function attributeLabels() {
		return array(
			'cause' => '请购理由',
			'typeId'=>'请购类型',
			'userName'=>'请购人',
			'comment'=>'备注',
		);
	}
	
	public function orderSerial( $orderId ) {
		$result = $this->findByAttributes(array('orderId'=>$orderId));
		if( $result ) {
			return $result->serial;
		}
		return '';
	}
	
	public function relations() {
		return array(
			'products' => array(self::HAS_MANY,'tbRequestbuyProduct','orderId')
		);
	}
	
	public function setState( $state ) {
		$error = false;
		$transaction = $this->getDbConnection()->beginTransaction();
		$products = tbRequestbuyProduct::model()->findAllByAttributes( array('orderId'=>$this->orderId) );
		if( $products ) {
			foreach( $products as $item ) {
				$item->state = $state;
				$result = $item->save();
				if( !$result ) {
					$transaction->rollback();
					$error = true;
					break;
				}
			}
			if( $error ) {
				return false;
			}
		}
		$this->state = $state;
		$this->save();
		
		$transaction->commit();
		return true;
	}

	/**
	 * 审核内部采购单
	 * @return bool
	 */
	public function accessToBuy() {
		$this->state = self::STATE_CHECKED;
		if( $this->save() ) {
			$rows = tbRequestbuyProduct::model()->updateAll(array('state'=>tbRequestbuyProduct::STATE_CHECKED),"orderId=:id", array(':id'=>$this->orderId));

			if( $rows ) {
				return true;
			}
		}
		return false;
	}

	public function getProducts() {
		$result = tbRequestbuyProduct::model()->findAllByAttributes( array('orderId'=>$this->orderId));
		return $result;
	}
}
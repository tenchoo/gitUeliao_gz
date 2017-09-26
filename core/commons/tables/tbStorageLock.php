<?php
/**
 * 产品可销售量库存锁定表
 * @author yagas
 * @package CActiveRecord
 */
class tbStorageLock extends CActiveRecord {

	//主键
	public $id;

	//订单编号
	public $orderId;

	//单品编码
	public $singleNumber;

	//锁定库存
	public $total;

	//创建锁定时间
	public $createTime;

	/**
	 * 初始化赋值
	 * @see CActiveRecord::init()
	 */
	public function init() {
		parent::init();
		$this->createTime = time();
	}

	public static function model( $className=__CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return '{{storage_lock}}';
	}

	public function primaryKey() {
		return 'id';
	}

	public function attributeLabels() {
		return [
			'orderId' => '订单编号',
			'singleNumber' => '商品编码',
			'total' => '锁定数量'
		];
	}

	/**
	 * 数据校验规则
	 * @see CModel::rules()
	 */
	public function rules() {
		return array(
			array('orderId,singleNumber,total','required'),
			array('orderId,total','numerical'),
		//	array('total','CFloatValidator')
		);
	}

	/**
	 * 数据库表关联关系
	 * @see CActiveRecord::relations()
	 */
	public function relations() {
		return array(
			'orderInfo' => array(self::BELONGS_TO,'tbOrder','orderId')
		);
	}

	/**
	 * 添加库存锁定申请
	 * @param CEvent $orderRecord CEvent对象
	 * @return bool
	 */
	public static function lock( CEvent $orderRecord ) {
		$lock = new tbStorageLock();
		$source = $orderRecord->sender;

		/** 传入待采购订单对象进行锁定量的添加 */
		if($source instanceof tbOrderPurchase2) {
			return self::fromPurchase2($source);
		}

//		/** 检查对象属性是否可用 */
//		$must_fields = array('orderId','total','singleNumber');
//		foreach( $must_fields as $item ) {
//			if( !property_exists($source,$item) ) {
//				Yii::app()->getController()->setError('Not found property:'.$item);
//				return false;
//				break;
//			}
//		}

		$lock->orderId = $source->orderId;
		$lock->singleNumber = $source->singleNumber;
		$lock->total = $source->total;
		if( !$lock->save() ) {
			Yii::app()->getController()->setError( $lock->getErrors() );
			return false;
		}
		else {
			return true;
		}
	}

	/**
	 * 通过待采购订单对象获取需要添加的锁定订单数据
	 * @param tbOrderPurchase2 $activeRecord
	 * @return bool
	 */
	private static function fromPurchase2(tbOrderPurchase2 $activeRecord) {
		if(  $activeRecord->source != tbOrderPurchase2::FROM_ORDER ) return true;		
		
		$lock               = new tbStorageLock();
		$lock->orderId      = $activeRecord->orderId;
		$lock->total        = $activeRecord->quantity;
		$lock->singleNumber = $activeRecord->productCode;
		$lock->createTime   = time();
		if(!$lock->save()) {
			Yii::app()->getController()->setError( $lock->getErrors() );
			return false;
		}
		return true;
	}

	/**
	 * 释放库存锁定量,或更改库存锁定量。
	 * @param CEvent $orderRecord CEvent对象
	 * @return bool
	 */
	public static function free( CEvent $orderRecord ) {
		$sender = $orderRecord->sender;
		/** 检查对象属性是否可用 */
		$must_fields = array('orderId','total','singleNumber','state');
		foreach( $must_fields as $item ) {
			if( !property_exists($sender,$item) ) {
				Yii::app()->getController()->setError('Not found property:'.$item);
				return false;
				break;
			}
		}

		$lock = tbStorageLock::model()->findByAttributes( array('orderId'=>$sender->orderId,'singleNumber'=>$sender->singleNumber));

		if( is_null($lock) ) {
			Yii::app()->getController()->setError( 'Not found lock by orderId:'.$sender->orderId.' singleNumber:'.$sender->singleNumber );
			return false;
		}

		if( $sender->state == '1' ) {
			if( $lock->delete() ) {
				return true;
			}
			else {
				Yii::app()->getController()->setError( $lock->getErrors() );
				return false;
			}
		}
		else {
			$lock->total = $sender->total;
			if( $lock->save() ) {
				return false;
			}
			else {
				Yii::app()->getController()->setError( $lock->getErrors() );
				return false;
			}
		}
	}

	/**
	 * 全部释放锁定数量
	 * @param CEvent $orderRecord
	 * @return bool
	 */
	public static function freeAll($orderRecord) {
		$sender = $orderRecord->sender;
		if( !property_exists($sender,'orderId') ) {
			Yii::app()->getController()->setError('Not found property:orderId');
			return false;
		}

		$criteria  = new CDbCriteria();
		$criteria->compare('orderId',$sender->orderId);
		return tbStorageLock::model()->deleteAll(  $criteria );
	}

	/**
	 * 获取单品锁定数量
	 * @param $singleNumber
	 * @return mixed
	 * @throws CDbException
	 */
	public function singleCount( $singleNumber ) {
		$sql = "select SUM(total) from {$this->tableName()} where singleNumber=:serial";
		$cmd = $this->getDbConnection()->createCommand( $sql );
		$cmd->bindValue(':serial',$singleNumber,PDO::PARAM_STR);
		$result = $cmd->queryScalar();
		return floatval($result);
	}
}
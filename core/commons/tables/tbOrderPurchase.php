<?php
/**
 * 待采购队列
 * @author yagas
 * @package CActiveRecord
 * @example
 * $this->attachEventHandler("onAfterSave", array("tbOrderPurchase","pushProducts"));
 */
class tbOrderPurchase extends CActiveRecord {
	
	public $purchaseId;
	public $source; //来源
	public $fromOrderId; //来源订单编号
	public $fromDetailId; //来源订单明细编号
	public $state;
	public $userId;
	public $createTime;
	public $singleNumber;
	public $color; //产品颜色
	public $total; //采购数量
	public $unitName; //计价单位
	public $comment; //来源订单备注信息
	
	private $_product;
	
	//0:内部请购  1:低库存采购  2:客户订单
	const FROM_INNER = 0;
	const FROM_LOWER = 1;
	const FROM_ORDER = 2;
	
	//0:未处理  1:正在处理  2:已处理完成
	const STATE_NORMAL   = 0;
	const STATE_CHOOSE   = 1;
	const STATE_PROCCESS = 2;
	const STATE_FINISHED = 3;
	
	public function init() {
		parent::init();
		$this->createTime = time();
		$this->state      = self::STATE_NORMAL;
		$this->unitName   = '码';
		$this->comment    = '';
		$this->userId     = Yii::app()->user->id;
	}
	
	public static function model( $className=__CLASS__) {
		return parent::model( $className );
	}
	
	public function tableName() {
		return '{{order_purchase}}';
	}
	
	public function primaryKey() {
		return 'purchaseId';
	}
	
	public function rules() {
		return array(
			array('source,fromOrderId,fromDetailId,state,singleNumber','required'),
			array('purchaseId,source,fromOrderId,fromDetailId,state','numerical'),
			array('color,total,unitName,comment','safe')
		);
	}
	
	/**
	 * 事件驱动
	 * 添加一条请购数据
	 * @param CEvent $event 被添加的订单对象
	 * @return boolean
	 */
	public static function pushProducts( CEvent $event ) {
		$class = get_class( $event->sender );
		return call_user_func( array(__CLASS__,'from'.$class), $event->sender );
	}
	
	/**
	 * 将客户订单转化为请购单
	 * @param CActiveRecord $record
	 * @return boolean
	 */
	private static function fromtbOrder( $record ) {
		$errors = false;
		$products = tbOrderProduct::model()->findAllByAttributes(
			array(
				'orderId'=>$record->orderId,
				'state'=>0
			)
		);

		if( $products ) {
			foreach( $products as $item ) {
				$order = new tbOrderPurchase();
				$order->source = self::FROM_ORDER;

				$order->fromOrderId  = $record->orderId;
				$order->fromDetailId = $item->orderProductId;
				$order->singleNumber = $item->singleNumber;
				$order->color        = $item->color;
				$order->total        = $item->num;
				$order->unitName     = ZOrderHelper::getUnitName($item->singleNumber);
				$order->comment      = $item->remark;

				if( !$order->save() ) {
					return false;
					break;
				}
			}
		}

		return true;
	}
	
	/**
	 * 内部请购单
	 @param CActiveRecord $record
	 * @return boolean
	 */
	private static function fromtbRequestbuy( tbRequestbuy $record ) {
		$error = false;

		$transaction = Yii::app()->getDb()->beginTransaction();
		Yii::trace('access request buy order to purchase', 'order');
		if( $record->accessToBuy() ) {
			$products = $record->products;

			if( $products ) {
				foreach( $products as $item ) {
					$order = new tbOrderPurchase();
					$order->source       = self::FROM_INNER;
					$order->fromOrderId  = $item->orderId;
					$order->fromDetailId = $item->requestProductId;
					$order->singleNumber = $item->singleNumber;
					$order->color        = $item->color;
					$order->total        = $item->total;
					$order->unitName     = $item->unitName;
					$order->comment      = $item->comment;

					if( !$order->save() ) {
						$error = true;
						break;
					}
				}
			}
			else {
				$error = true;
			}
		}

		if( !$error ) {
			$transaction->commit();
			return true;
		}
		
		$transaction->rollback();
		return false;
	}
	
	/**
	 * 订单明细
	 * @param string $record
	 */
	public function detail( $record=null ) {
		if( !is_null($record) ) {
			$this->_product = $record;
		}
		else {
			return $this->_product;
		}
	}
	
	/**
	 * 自动关联查询订单明细
	 * @see CActiveRecord::findAll()
	 */
	public function findAll( $condition='', $params=array() ) {
		$result = parent::findAll( $condition, $params );
		if( $result ) {
			foreach( $result as &$item ) {
				switch ( $item->source ) {
					case 2:
						$product = tbOrderProduct::model()->findByPk($item->fromDetailId);
						break;
						
					case 0:
						$product = tbRequestbuyProduct::model()->findByPk($item->fromDetailId);
						break;
						
					case 1:
						$product = tbRequestlower::model()->findByPk($item->fromDetailId);
						break;
						
				}
				$item->detail( $product );
			}
		}
		return $result;
	}
	
	public function findAllByProccess() {
		$data = array();
		$criteria = new CDbCriteria();
		$criteria->condition = "userId=:uid and state=:state";
		$criteria->params = array(':uid'=>Yii::app()->user->id, ':state'=>self::STATE_CHOOSE);
		$result = $this->findAll( $criteria );
		
		foreach( $result as $item ) {
			$key = $item->singleNumber;
			if( !array_key_exists($key,$data) ) {
				$rows = array(
					'count' => 1,
					'singleNumber' => $item->singleNumber,
					'color' => $item->color,
					'total' => 0,
					'childrens' => array()
				);
				$data[$key] = $rows;
			}
			array_push( $data[$key]['childrens'], $item );
			
			$data[$key]['total'] += $item->total;
			++$data[$key]['count'];
		}
		return $data;
	}
}
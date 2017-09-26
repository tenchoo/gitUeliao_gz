<?php
/**
 * 采购单产品信息
 * @author yagas
 * @package CActiveRecord
 */
class tbOrderbuyProduct extends CActiveRecord {

	public $id;
	public $orderId;
	public $corpProductNumber;
	public $dealTime;
	public $remark;
	public $total;
	public $singleNumber;
	public $color;
	public $comment='';
	
	private $_request = array();
	private $_relate;

	public function init() {
		parent::init();

		//事件绑定，记录采购单与来源订单的关系
		$this->attachEventHandler('onAfterSave', array('tbOrderbuyRelate','event_relate') );
	}

	public function tableName() {
		return '{{order_buy_product}}';
	}
	
	public function primaryKey() {
		return 'id';
	}
	
	public static function model( $className=__CLASS__ ) {
		return parent::model( $className );
	}
	
	public function rules() {
		return array(
			array('orderId,dealTime,corpProductNumber','required'),
			array('total','numerical'),
			array('singleNumber,color,remark,comment,relate','safe')
		);
	}
	
	protected function beforeValidate() {
		$this->id = ZOrderHelper::getOrderId();
		return parent::beforeValidate();
	}
	
	public function pushRequest( $data ) {
		array_push( $this->_request, $data );
	}
	
	public function fetchRequest() {
		return $this->_request;
	}
	
	public function setRelate($value) {
		$this->_relate = $value;
	}
	
	public function getRelate() {
		return $this->_relate;
	}
	
	public function getUnitName() {
		return tbProductStock::model()->unitName( $this->singleNumber);
	}
	
	/**
	 * 更新源订单状态信息
	 @param string $source
	 @param string $orderId
	 */
	protected function updateSourceState($source,$orderId) {
		switch( $source ) {
			case 0:
				$detail = tbRequestbuyProduct::model()->findByPk( $orderId );
				break;
			
			case 2:
				$detail = tbOrderProduct::model()->findByPk( $orderId );
				break;
				
			default:
				$detail = null;
		}
		if( is_null($detail) ) {
			$this->addError('changeSource', 'Not found change source state');
			return false;
		}
		$detail->state = $detail::STATE_PROCCESSING;
		return $detail->save();
	}
	
	protected function updatePurchaseState() {
		$sql = "update {{order_purchase}} set state=1 where userId=:uid and state=0";
		$cmd = $this->getDbConnection()->createCommand( $sql );
		$cmd->bindValue( ':uid', Yii::app()->getUser()->id );
		return $cmd->execute();
	}
}
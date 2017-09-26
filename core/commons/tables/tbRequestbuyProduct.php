<?php
/**
 * 请购单对应产品数据表
 * @author yagas
 * @package CActiveRecord
 * @todo 调用 updateState方法时，依赖于tbOrderbuyDraft模型
 */
class tbRequestbuyProduct extends CActiveRecord {
	
	public $requestProductId;
	public $productId;
	public $orderId;
	public $singleNumber;
	public $total;
	public $dealTime;
	public $state;
	public $createTime;
	public $color;
	public $comment;
	public $from;
	public $unitName='码';
	
	const STATE_NORMAL      = 0;
	const STATE_CHECKED     = 1;
	const STATE_WAITING     = 2;
	const STATE_PROCCESSING = 3;
	const STATE_FINISHED    = 4;
	const STATE_CLOSE       = 5;
	const STATE_DELETE      = 6;
	
	
	const FORM_COMPANY = 0;
	const FORM_STORE   = 1;
	const FORM_ORDER   = 2;
	
	public function init() {
		parent::init();
		$this->createTime = time();
		$this->state = self::STATE_NORMAL;
	}
	
	public static function model( $className=__CLASS__ ) {
		return parent::model( $className );
	}
	
	public function tableName() {
		return '{{request_buy_product}}';
	}
	
	public function primaryKey() {
		return 'requestProductId';
	}
	
	public function rules() {
		return array(
			array('singleNumber,total,color,dealTime,orderId,productId','required'),
			array('total,dealTime,from','numerical'),
			array('comment,unitName','safe')
		);
	}
	
	public function attributeLabels() {
		return array(
			'singleNumber' => '产品编号',
			'total'=>'订货数量',
			'dealTime'=>'交货日期',
			'comment'=>'备注',
		);
	}
	
	
	/**
	 * 获取未处理的请购单记录列表
	 * @param integer $page_size
	 * @param integer $page
	 */
	public function findAllByWaiting( CDbCriteria $criteria ) {
		$joinTableName = tbRequestbuy::model()->tableName();
		$criteria->select = "t.id,t.`from`,o.orderId,o.serial,t.total,t.unitName,t.dealTime,t.singleNumber,t.color,t.comment";
		$criteria->join = "inner join {$joinTableName} o on t.orderId=o.orderId";
		return $this->findAll($criteria);
	}
	
	/**
	 * 通过审核的请购单记录总数
	 */
	public function countByChecked() {
		$draftTableName = tbOrderbuyDraft::model()->tableName();
		$sql = "select count(*) from {$this->tableName()} A where not exists(select 1 as `id` from {$draftTableName} B where B.requestId=A.id) and A.state=".self::STATE_CHECKED;
		$cmd = $this->getDbConnection()->createCommand( $sql );
		$result = $cmd->queryColumn();
		return intval($result[0]);
	}
	
	public function findAllByChecked( CDbCriteria $criteria=null ) {
		if( is_null($criteria) ) {
			$criteria = new CDbCriteria();
		}
		$draftTableName = tbOrderbuyDraft::model()->tableName();
		$requestOrderName = tbRequestbuy::model()->tableName();
		$cmd = $this->getDbConnection()->createCommand();
		$cmd->select = "A.id,C.typeId,A.serial,C.serial as orderId,A.singleNumber,A.color,A.total,A.unitName,A.comment,A.dealTime";
		$cmd->join = "left join {$requestOrderName} C on A.orderId=C.orderId";
		$cmd->limit( $criteria->limit, $criteria->offset);
		$cmd->from("{$this->tableName()} A");
		$cmd->where = "not exists(select 1 as `id` from {$draftTableName} B where B.requestId=A.id ) and A.state=".self::STATE_CHECKED;
		$result = $cmd->queryAll();
		return $result;
	}
	
	protected function afterFind() {
		$this->dealTime = intval($this->dealTime);
		return parent::afterFind();
	}
	
	/**
	 * 在创建工厂发货单后更新本明细单状态
	 * 检查同级明细订单是否全部发货
	 * 如果同级明细订单全部完成发货则更新内部请购单状态
	 * @throws CException
	 */
	public function stateToDone() {
		if($this->isNewRecord ||is_null($this->requestProductId)) {
			throw new CException("new record not support this method");
		}
		
		$this->state = self::STATE_FINISHED;
		if($this->save()) {
			$this->checkOrderDone($this->orderId);
		}
	}
	
	/**
	 * 检查同级明细订单是否全部发货
	 * 如果同级明细订单全部完成发货则更新内部请购单状态
	 * @param integer $orderId 内部请购单编号
	 */
	private function checkOrderDone($orderId) {
		$orderCount = $this->countByAttributes(array("orderId"=>$orderId));
		$doneCount  = $this->countByAttributes(array("orderId"=>$orderId,"state"=>self::STATE_FINISHED));
		if($orderCount === $doneCount) {
			tbRequestbuy::model()->updateByPk($orderId, array('state'=>tbRequestbuy::STATE_FINISHED));
		}
	}
}
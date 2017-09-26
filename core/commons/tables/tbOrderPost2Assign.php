<?php
/**
 * 发货订单匹配记录
 * @package CActiveRecord
 */
class tbOrderPost2Assign extends CActiveRecord {

	public $purchaseId;		//待采购队列订单号
	public $postProId;		//发货单号
	public $userId;			//订单编号
	public $createTime;		//匹配状态

	const STATE_ASSIGN   = 1;
	const STATE_UNASSIGN = 0;
	
	public static function model( $className=__CLASS__) {
		return parent::model( $className );
	}
	
	public function tableName() {
		return '{{order_post2_assign}}';
	}
	
	public function primaryKey() {
		return 'id';
	}
	
	public function init() {
		parent::init();
		$this->createTime = time();
		$this->userId = Yii::app()->user->id;
	}

	public function rules() {
		return array(
			array('purchaseId,postProId,userId,createTime','required'),
			array('purchaseId,postProId,userId,createTime','numerical')
		);
	}

	/**
	 * 获取已匹配的客户订单
	 * @param $postProId
	 * @return array|tbOrder
	 * @throws CDbException
	 */
	public function orders($postProId) {
		$cmd = $this->getDbConnection()->createCommand();
		$cmd->from($this->tableName());
		$cmd->selectDistinct("purchaseId");
		$cmd->where("postProId=:id", ['id'=>$postProId]);
		$purchaseId = $cmd->queryColumn();

		if($purchaseId) {
			$tableName = tbOrderPurchase2::model()->tableName();
			$ids = implode(',',$purchaseId);
			$sql = "SELECT DISTINCT orderId FROM {$tableName} WHERE purchaseId in({$ids})";
			$cmd2 = $this->getDbConnection()->createCommand($sql);
			$purchases = $cmd2->queryColumn();

			if($purchases) {
				return tbOrder::model()->findAllByPk($purchases);
			}
		}
		return [];
	}
}
<?php
/**
 * 留货单审核情况
 * @author yagas
 * @package CActiveRecord
 */
class tbOrderKeep extends CActiveRecord {

	public $orderKeepId;
	public $orderId;
	public $state;
	public $userId;
	public $createTime;
	public $expireTime;
	public $cause;
	public $buyState;

	const STATE_NORMAL = 0;
	const STATE_CHECKED = 1;
	const STATE_REFUSE = 2;

	const BUYSTATE_NO = 0;
	const BUYSTATE_DO = 1;

	public function init() {
		$this->orderKeepId = ZOrderHelper::getOrderId ();
		$this->createTime = time ();
		$this->state = 0;
		$this->userId = 0;
		$this->cause = '';
	}

	public function rules() {
		return array (
				array ( "state,orderId,userId,expireTime","required"),
				array ( "state,userId,orderId,userId","numerical"),
				array (	"cause,expireTime","safe" )
		);
	}

	public function attributeLabels(){
		return array(
			'orderId' => '订单ID',
			'state' => '审核结果',
			'cause' => '审核原因',
			);
	}

	public function tableName() {
		return "{{order_keep}}";
	}

	public function primaryKey() {
		return "orderKeepId";
	}

	public static function model($className = __CLASS__) {
		return parent::model ( $className );
	}

	public function relations() {
		return array(
			'orderInfo' => array( self::BELONGS_TO, 'tbOrder', 'orderId' )
		);
	}

	public function getUserInfo() {
		$orderInfo = tbOrder::model()->findByPk($this->orderId);
		if($orderInfo) {
			$memberInfo = tbMember::model()->with('profiledetail')->findAllByPk($orderInfo->memberId);
			return $memberInfo;
		}
		return false;
	}

	public function stateTitle(){
		$arr = array('0'=>'待审核','1'=>'审核通过','2'=>'审核不通过');
		return array_key_exists( $this->state,$arr )?$arr[$this->state]:'';
	}
	
	public function userName(){
		return tbUser::model()->getUserName( $this->userId );
	}
}

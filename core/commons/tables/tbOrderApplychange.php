<?php
/**
 * 订单修改申请信息表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$applyId
 * @property integer	$orderId			订单ID
 * @property integer	$state				状态：0未审，1审核通过，2审核不通过,3删除,4订单已关闭
 * @property integer	$applyType			申请来源：0客户申请，1业务员申请
 * @property integer	$memberId			申请人memberId
 * @property integer	$checkUserId		审核人userId
 * @property date		$createTime			申请时间
 * @property date		$checkTime			审核时间
 * @property string		$meno				订单备注
 * @property string		$address			订单地址
 * @property string		$checkInfo			审核信息
 *
 */

 class tbOrderApplychange extends CActiveRecord {
	
	const STATE_CLOSE = 4; //订单已关闭
 
	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{order_applychange}}";
	}

	public function relations(){
		return array(
			'order'=>array(self::BELONGS_TO,'tbOrder','orderId'),
			'detail'=>array(self::HAS_MANY,'tbOrderApplychangeDetail','applyId'),
		);
	}

	public function rules() {
		return array(
			array('orderId,freight,address','required'),
			array('applyType','in','range'=>array(0,1)),
			array('freight', "numerical"),
			array('orderId', "numerical","integerOnly"=>true),
			array('meno,address,checkInfo','safe'),
			array('orderId','unique'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'orderId' => '订单号',
			'address'=> '订单地址'
		);
	}

	/**
	 * 初始化
	 */
	public function init(){		
		$this->createTime = new CDbExpression('NOW()');
		if(  Yii::app()->user->getState('usertype') == tbMember::UTYPE_SALEMAN ){
			$this->applyType  = 1;
			$this->state  = 1;
		}else{
			$this->applyType  = 0;
			$this->state  = 0;
		}
	}

	/**
	* 已申请修改的订单记录
	* @param array $orderIds
	*/
	public function hasApplyChange( $orderIds = array() ){
		if( empty($orderIds) || !is_array($orderIds) ) return array();
		$c = new CDbCriteria;
		$c->compare('orderId',$orderIds);
		$model = $this->findAll( $c );

		if( !$model ) return array();
		return array_map(function($i){return $i->orderId;},$model);
	}

	/**
	* 关闭审核，当订单关闭时，状态为未审核的，状态更改为已关闭。
	* @param integer $orderId
	*/
	public function closeCheck( $orderId ){
		$model = $this->findByAttributes( array('orderId'=>$orderId,'state'=>0) );
		if( $model ){
			$model->state = self::STATE_CLOSE;
			if( $model->save() ){
				return true;
			}
		}
		
		return false;
	}
		
		

	/**
	 * 保存后的操作
	 */
	protected function afterSave(){
		if($this->isNewRecord){
			//记录订单追踪信息
			if(  Yii::app()->user->getState('usertype') == tbMember::UTYPE_SALEMAN ){
				$track = 'saleman_applyChange';
			}else{
				$track = 'member_applyChange';
			}
			tbOrderMessage::addMessage( $this->orderId,$track );
		}
	}
}
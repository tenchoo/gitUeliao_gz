<?php
/**
 * 客户申请取消订单信息审核记录表
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$id
 * @property integer	$orderId			订单ID
 * @property integer	$state				状态：0未审，1审核通过，2审核不通过,3删除
 * @property integer	$checkUserId		审核人userId
 * @property date		$createTime			申请时间
 * @property date		$checkTime			审核时间
 * @property string		$reason				申请取消理由
 * @property string		$remark				审核说明
 *
 */

 class tbOrderApplyclose extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{order_applyclose}}";
	}

	public function relations(){
		return array(
			'order'=>array(self::BELONGS_TO,'tbOrder','orderId'),
		);
	}

	public function rules() {
		return array(
			array('orderId,reason','required'),
			array('orderId', "numerical","integerOnly"=>true),
			array('reason,remark', "safe"),
		);
	}

	/**
	 * 初始化
	 */
	public function init(){
		$this->state  = 0;
		$this->createTime = new CDbExpression('NOW()');
		$this->remark = '';
	}

	/**
	* 判断是否已申请过取消
	* @param integer $orderId
	*/
	public function hasApply( $orderId ){
		return $this->exists( 'state in(0,1) and orderId = :orderId',array(':orderId'=>$orderId) );
	}


	/**
	* 已申请取消的订单记录
	* @param integer $orderIds
	*/
	public function hasApplyClose( $orderIds = array() ){
		if( empty($orderIds) || !is_array($orderIds) ) return array();
		$c = new CDbCriteria;
		$c->compare('orderId',$orderIds);
		$c->compare('state',array(0,1));
		$model = $this->findAll( $c );
		if( !$model ) return array();
		return array_map(function($i){return $i->orderId;},$model);
	}
}
<?php
/**
 * 结算单明细
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$id
 * @property integer	$settlementId		结算单ID
 * @property integer	$orderProductId		订单明细表ID
 * @property numerical	$num				实际结算数量
 * @property integer	$isSample			是否样板
 * @property integer	$remark				备注
 *
 */

 class tbOrderSettlementDetail extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{order_settlement_detail}}";
	}

	public function rules() {
		return array(
			array('orderProductId,settlementId,num','required'),
			array('isSample','in','range'=>array(0,1)),
			array('orderProductId,settlementId', "numerical","integerOnly"=>true),
			array('num', "numerical"),
			array('remark', "safe"),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'orderProductId' => '订单明细表ID',
			'settlementId' => '结算单ID',
			'num'=>'实际结算数量',
			'isSample'=>'是否样板',
			'remark'=>'备注',
		);
	}	
}
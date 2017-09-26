<?php
/**
 * 送货备注
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$id
 * @property integer	$deliveryOrderId
 * @property integer	$state
 * @property integer	$deliverymanId
 * @property string		$opTime 			时间
 * @property string		$remark				备注
 *
 */

 class  tbDeliveryOrderOp extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{delivery_order_op}}";
	}

	public function rules() {
		return array(
			array('deliveryOrderId,deliverymanId,state,remark','required'),
			array('deliveryOrderId,deliverymanId,state', "numerical","integerOnly"=>true),
			array('state','in','range'=>array(0,1,2,3)),
			array('deliveryOrderId,remark','safe'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
	}

	/**
	* 取得
	*/
	public function getStates(){
		return array(
			'0'=>'待送',
			'1'=>'已送',
			'2'=>'挂起',
			'3'=>'放弃',
		);
		return $result;
	}

	/**
	* 取得全部的送货备注
	*/
	public function getAllops( $deliveryOrderId ){
		if( !is_numeric( $deliveryOrderId ) || empty ( $deliveryOrderId ) ) return array();

		$criteria = new CDbCriteria;
		$criteria->select = 'opTime,remark';
		$criteria->compare( 't.deliveryOrderId',$deliveryOrderId );
		$criteria->order = 'opTime desc';
		$models = $this->findAll( $criteria );
		return array_map( function( $i ){
				$i->opTime = date('m/d H:i',strtotime( $i->opTime ) );
				return $i->getAttributes( array( 'opTime','remark') );
				}, $models);
	}


}
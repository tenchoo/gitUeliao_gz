<?php
/**
 * 归单订单记录
 * @author liang
 * @version 0.2
 *
 * @property int    $id
 * @property int    $state  		  归单状态:0未完成归单，1已全部归单完成
 * @property int    $actionType  	  订单处理状态:0待处理，1已调拨，2已发货
 * @property int    $orderId  		  订单ID
 * @property int    $warehouseId  	  分拣仓库ID
 * @property int    $mergeUserId  	  调度安排的归单人
 * @property string $createTime	 	  加入归单队列时间
 * @property string $mergeTime	 	  全部归单完成时间
 */

class tbOrderMerge  extends CActiveRecord {
	const STATE_WAIT = 0;//待归单
	const STATE_DONE = 1;//已归单完成
	const STATE_ALLOTTED = 2;//已调拨确认，调拨过来的订单只允许发货，不再调拨

	const ACTION_NONE = 0; //0待处理
	const ACTION_ALLOTTED = 1;//1已调拨
	const ACTION_DELIVERY = 2;//2已发货


	public static function model($className=__CLASS__) {
		return parent::model( $className );
	}

	public function tableName() {
		return '{{order_merge}}';
	}

	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
		}

		return parent::beforeSave();
	}

	/**
	 * 保存前的操作
	 */
	protected function afterSave(){
		if($this->isNewRecord && $this->state == self::STATE_WAIT ){
			tbOrder::model()->updateByPk( $this->orderId,array('state'=>2),'state = 1' );
		}

		return parent::afterSave();
	}




	public function actionTypes( $i= null ){
		$arr = array( '0'=>'待处理','1'=>'已调拨','2'=>'已发货', );
		if( is_null( $i ) ) return $arr;
		return array_key_exists( $i,$arr )?$arr[$i]:'';
	}
}

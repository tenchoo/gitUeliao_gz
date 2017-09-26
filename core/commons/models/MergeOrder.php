<?php
/**
* 订单归单---仓库管理
* @version 0.1
* @package CFormModel
*/

class MergeOrder extends CFormModel {

	public $userId;

	function __construct( $userId ) {
		parent::__construct();

		$this->userId = $userId;
	}

	/**
	* 确认归单
	*/
	public function doMerge( $model ){

		//使用事务处理
		$transaction = Yii::app()->db->beginTransaction();
		try {
			$model->state = tbPack::STATE_MERGE;
			$model->mergeUserId = $this->userId;
			$model->mergeTime = date( 'Y-m-d H:i:s' );

			if( !$model->save() ){
				$transaction->rollback();
				$this->addErrors( $model->getErrors() );
				return false;
			}

			if( !$this->changeState( $model ) ){
				$transaction->rollback();
				return false;
			}

			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			throw new CHttpException(503,$e);
			return false;
		}
	}

	private function changeState( $model ){
		//检查当前订单是否还有产品未归单完毕
		$flag = $model->exists( 'orderId=:oid and state <:state and warehouseId =:wid',
								array( ':oid'=>$model->orderId, ':state'=>$model->state,':wid'=>$model->warehouseId) );

		if( $flag ) return true;

		//更改整单的归单状态
		$merge = tbOrderMerge::model()->find( 'orderId=:oid and state=:state and warehouseId =:wid',
								array( ':oid'=>$model->orderId, ':state'=>tbOrderMerge::STATE_WAIT,':wid'=>$model->warehouseId) );
		if( !$merge ) return true;

		$merge->state = tbOrderMerge::STATE_DONE;
		if( !$merge->save() ){
			$this->addErrors( $merge->getErrors() );
			return false;
		}

		return true;
	}
}


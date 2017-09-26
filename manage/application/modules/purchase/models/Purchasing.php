<?php
/**
* 采购单管理
*
*/
class Purchasing extends CFormModel {

	public $reason;

	/**
	* 取消采购单
	*
	*/
	public function cancle( $model,$reason ){
		$len = mb_strlen($reason,'UTF8' );
		if( $len<6 || $len>80 ){
			//理由长度为6-80个字。
			$this->addError('reason',Yii::t('base','number of {name} in the {n1}-{n2}',array('{name}'=>'取消理由','{n1}'=>6,'{n2}'=>80)));
			return false;
		}
		//判断是否有发过货，有发货不允许取消
		$falg = tbOrderPost2::model()->exists('purchaseId=:purchaseId',array(':purchaseId'=>$model->purchaseId));
		if( $falg ){
			$this->addError('reason',Yii::t('order','This purchase order has a shipping record, which is not allowed to cancel.'));
			return false;
		}

		//使用事务处理
		$transaction = Yii::app()->db->beginTransaction();
		try {
			//生成取消记录
			$close = new tbPurchasingClose();
			$close->purchaseId = $model->purchaseId;
			$close->reason = $reason;
			if( !$close->save() ) {
				$this->addErrors( $close->getErrors() );
				return false;
			}


			//更改采购单状态为已取消
			$model->state = tbOrderPurchasing::STATE_CLOSE;
			if( !$model->save() ) {
				$this->addErrors( $model->getErrors() );
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

}
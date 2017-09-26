<?php
class ProductAtBatch extends CAction {

	public function run() {
		$warehouseId = Yii::app()->request->getQuery('house');
		$serial      = Yii::app()->request->getQuery('serial');
		$extraOrderId = Yii::app()->request->getQuery('extraOrderId');

		$result = tbWarehouseProduct::model()->findAllBatch( $warehouseId, $serial );
		if( !$result ) {
			return new AjaxData( false, Yii::t('api', 'Not found record') );
		}

		//可分配数量需减去锁定量。
		$batchs = array_map ( function ( $i ){ return $i['productBatch'];},$result );
		$lockNum = tbWarehouseLock::model()->findGroupBatch( $serial, $warehouseId,$batchs,$extraOrderId );

		foreach ( $result as $index => & $item ) {
			if( array_key_exists( $item['productBatch'],$lockNum) ){
				$item['num'] = bcsub($item['num'], $lockNum[$item['productBatch']]);
			}

			if($item['num']<=0) {
            	unset($result[$index]);
            	continue;
            }

			$item['unit'] = ZOrderHelper::getUnitName($serial);
			$item['num'] = Order::quantityFormat($item['num']);
		}
		return new AjaxData( true, null, $result );
	}
}
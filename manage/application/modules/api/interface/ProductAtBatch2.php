<?php
class ProductAtBatch2 extends CAction {

	public function run() {
		$position = Yii::app()->request->getQuery('position');
		$serial      = Yii::app()->request->getQuery('serial');

		$unit =		 ZOrderHelper::getUnitName($serial);

		$result = tbWarehouseProduct::model()->findAllByPosition2( $position, $serial );
		if( !$result ) {
			return new AjaxData( false, Yii::t('api', 'Not found record') );
		}

		//需减去对应锁定量。
		$lockNum = tbWarehouseLock::model()->findGroupBatchOfPosition( $serial, $position );

		foreach ( $result as $index => & $item ) {
			if( array_key_exists( $item['productBatch'],$lockNum) ){
				$item['total'] = bcsub($item['total'], $lockNum[$item['productBatch']],1);
			}

/* 			if($item['total']<=0) {
				unset($result[$index]);
				continue;
			} */

			$item['total'] = Order::quantityFormat($item['total']);
			$item['unit'] = $unit;

			//实时查找可用数量
			// $condition = array( 'positionId'=>$item['positionId'],'singleNumber'=>$serial,'productBatch'=>$item['productBatch']);
			// $item['ValidNum'] =  tbWarehouseProduct::model()->findValidNum( $condition );

		}
		return new AjaxData( true, null, $result );
	}
}
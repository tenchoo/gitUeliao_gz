<?php
/**
 * 查询包含产品的分区信息
 * Created by PhpStorm.
 * User: yagas
 * Date: 2015/11/27
 * Time: 17:40
 */
class ContainProductArea extends CAction {

    public function run() {
        $storage = Yii::app()->request->getQuery('house');
        $serial  = Yii::app()->request->getQuery('serial');
		$extraOrderId = Yii::app()->request->getQuery('extraOrderId');
		
        $unitName = ZOrderHelper::getUnitName( $serial );

		$model = new tbWarehouseProduct();
        $result = $model->areaContainProduct($serial,$storage);
        if( !$result ) {
            return new AjaxData(false,"Not found Record");
        }
        else {
			//需减去对应锁定量。
			$lockNum = tbWarehouseLock::model()->findGroupArea( $serial, $storage ,$extraOrderId );
        	foreach( $result as $index => & $item ) {
				if( array_key_exists( $item['areaId'],$lockNum) ){
					$item['num'] = bcsub($item['num'], $lockNum[$item['areaId']]);
				}

        		if($item['num']<=0) {
        			unset($result[$index]);
        			continue;
        		}

        		$item['num'] = Order::quantityFormat($item['num']);
        		$item['houseTitle'] = $model->warehouseName( $item['warehouseId']);
                $item['areaTitle'] = $model->positionName( $item['areaId']);
        		$item['unit']  = $unitName;
        	}
            return new AjaxData(true,null,$result);
        }
    }
}
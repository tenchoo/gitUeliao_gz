<?php
/**
 * 查询包含产品的仓位信息
 * Created by PhpStorm.
 * User: yagas
 * Date: 2015/11/27
 * Time: 17:40
 */
class ContainProductPosition extends CAction {

    public function run() {
        $area     = Yii::app()->request->getQuery( 'area' );
        $serial   = Yii::app()->request->getQuery( 'serial' );
		$extraOrderId = Yii::app()->request->getQuery('extraOrderId');
		
        $unitName = ZOrderHelper::getUnitName( $serial );

		$model = new tbWarehouseProduct();
        $result = $model->positionContainProduct($serial,$area);
        if( !$result ) {
            return new AjaxData(false,"Not found Record");
        }
        else {
			//需减去对应锁定量。
			$lockNum = tbWarehouseLock::model()->findGroupPositionOfArea( $serial, $area,$extraOrderId );
        	foreach( $result as $index => & $item ) {
				if( array_key_exists( $item['positionId'],$lockNum) ){
					$item['num'] = bcsub($item['num'], $lockNum[$item['positionId']]);
				}
        		if($item['num']<=0) {
        			unset($result[$index]);
        			continue;
        		}

        		$item['num'] = Order::quantityFormat($item['num']);
        		$item['houseTitle'] = $model->positionName( $item['warehouseId']);
                $item['areaTitle'] = $model->positionName( $item['areaId']);
        		$item['unit']  = $unitName;
        	}
            return new AjaxData(true,null,$result);
        }
    }
}
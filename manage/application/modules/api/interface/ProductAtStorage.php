<?php
/**
 * 查找包含单品编码的仓库信息
 * Created by PhpStorm.
 * User: yagas
 * Date: 2015/11/26
 * Time: 15:23
 */
class ProductAtStorage extends CAction {

    public function run() {
        $serial   = Yii::app()->request->getQuery('serial');


        $storages = $this->fetchAll( $serial );
        if( !$storages ) {
            return new AjaxData(false,'Not found record');
        }

        return new AjaxData(true,null,$storages);
    }

    private function fetchAll( $serial ) {
		$extraOrderId = Yii::app()->request->getQuery('extraOrderId');

    	$unitName = ZOrderHelper::getUnitName( $serial );
    	if( empty($serial) ) {
    		return false;
    	}

		$model = new tbWarehouseProduct();
        $result = $model->findProductInStroage( $serial );

		if( !is_array(  $result ) || empty( $result ) ) return  $result;

		//可分配数量需减去锁定量。
		$warehouseIds = array_map ( function ( $i ){ return $i['warehouseId'];},$result );

		$lock = tbWarehouseLock::model()->findGroupWarehouse( $serial, $warehouseIds,$extraOrderId );
		$lockNum = array();
		if( is_array ( $lock ) ){
			foreach ( $lock as $val  ){
				$lockNum[$val['warehouseId']] = $val['num'];
			}
		}

        if( $result ) {
            foreach( $result as $index => & $item ) {
				if( array_key_exists( $item['warehouseId'],$lockNum) ){
					$item['total'] = bcsub($item['total'], $lockNum[$item['warehouseId']]);
				}
            	if($item['total']<=0) {
            		unset($result[$index]);
            		continue;
            	}

            	$item['total'] = Order::quantityFormat($item['total']);
                $item['title']    = $model->warehouseName( $item['warehouseId']);
                $item['unit']     = $unitName;
                $item['position'] = $model->positionName( $item['positionId'] );
            }
        }
        return $result;
    }
}
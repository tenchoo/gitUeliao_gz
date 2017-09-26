<?php

/**
 * Created by PhpStorm.
 * User: yagas-office
 * Date: 2015/12/7
 * Time: 17:33
 * @package CActive
 */
class ProductAtArea extends CAction {

    public function run() {
        $serial   = Yii::app()->request->getQuery('serial');
        $unit = ZOrderHelper::getUnitName( $serial );
        $storageId = Yii::app()->request->getQuery('house');

        $storageInfo = tbWarehouseProduct::model()->strageContainProduct( $serial, $storageId );
        foreach( $storageInfo as $index => & $storage ) {
        	if($storage['num']<=0) {
        		unset($storage[$index]);
        		continue;
        	}

            $storage = array_merge($storage,tbWarehouse::model()->findPositionFather( $storage['positionId'] ));
            $storage['positionTitle'] = tbWarehouse::getTitle( $storage['positionId'] );
            $storage['unit'] = $unit;
        }
        return new AjaxData(true, null, $storageInfo);
    }
}
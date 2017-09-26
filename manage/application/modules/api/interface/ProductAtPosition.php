<?php
/**
 * 查找仓库所包含的仓位信息
 * Created by PhpStorm.
 * User: yagas
 * Date: 2015/11/27
 * Time: 17:40
 */
class ProductAtPosition extends CAction {

    public function run() {
        $storage = Yii::app()->request->getQuery('house');
        $serial  = Yii::app()->request->getQuery('serial');
        $unitName = ZOrderHelper::getUnitName( $serial );

        $result = tbWarehouseProduct::model()->findAllPosition($storage,$serial);
        if( !$result ) {
            return new AjaxData(false,"Not found Record");
        }
        else {
        	foreach( $result as $index => & $item ) {
        		if($item['sum']==0) {
        			unset($result[$index]);
        			continue;
        		}
        		
        		$item['sum'] = Order::quantityFormat($item['sum']);
        		$item['title'] = $this->positionName( $item['positionId']);
        		$item['unit']  = $unitName;
        	}
            return new AjaxData(true,null,$result);
        }
    }
    
    
    private function positionName( $positionId ) {
    	$result = tbWarehouse::model()->findByPk( $positionId );
    	if( $result ) {
    		return $result->title;
    	}
    	return;
    }
}
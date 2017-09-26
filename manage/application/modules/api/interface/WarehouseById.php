<?php
/**
 * 查找根据所属分区ID查找其所属仓位
 * Created by PhpStorm.
 * User: liang
 * Date: 2016/04/29
 * Time: 16:10
 */
class WarehouseById extends CAction {

    public function run() {
        $parentId = Yii::app()->request->getQuery('parentId');
		$warehouseId = Yii::app()->request->getQuery('warehouseId');

		$criteria = new CDbCriteria;
		if( is_numeric($parentId) && $parentId>0 ){
			$criteria->compare('t.parentId',$parentId);
		}else if(  is_numeric($warehouseId) && $warehouseId>0 ){
			$criteria->compare('t.warehouseId',$warehouseId);
			$criteria->compare('t.parentId','0');
		}else{
			return new AjaxData(false,'error parentId',null);
		}

		$criteria->compare('t.state','0');
		$data = tbWarehousePosition::model()->findAll( $criteria );
		$data = array_map( function($i){return $i->getAttributes(array('positionId','title'));},$data);
		return new AjaxData(true,null,$data);
	}
}
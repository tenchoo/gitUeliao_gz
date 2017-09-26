<?php
/**
 * 仓库仓位信息
 * @author yagas
 * @package CAction
 * @request get id
 *
 */
class StoragePositionInfo extends CAction implements IAction {

	public function run() {
		$warehouseId = Yii::app()->request->getQuery('warehouseId');
		$id = intval( Yii::app()->request->getQuery('id',0) );

		if( is_numeric($id) && $id>0 ){
			$storage = tbWarehousePosition::model()->findAllByAttributes(array('parentId'=>$id,'state'=>0));
		}else if(  is_numeric($warehouseId) && $warehouseId>0 ){
			$storage = tbWarehousePosition::model()->findAllByAttributes(array('warehouseId'=>$warehouseId,'parentId'=>0,'state'=>0));
		}else{
			$storage = tbWarehouseInfo::model()->findAllByAttributes(array('state'=>0));
			$record = array( 'parent'=>0, 'childs'=>new stdClass() );
			foreach( $storage as $index=>$item ) {
				$itemId = $item->warehouseId;
				$record['childs']->$itemId = $item->title;
			}
			return new AjaxData(true,null,$record);
		}

		if( is_null($storage) ) {
			return new AjaxData(false,array('storage','Not found record'));
		}

		$record = array( 'warehouseId'=>0,'parent'=>$id, 'childs'=>new stdClass() );
		foreach( $storage as $index=>$item ) {
			$record['warehouseId'] = $item->warehouseId;
			$itemId = $item->positionId;
			$record['childs']->$itemId = $item->title;
		}
		return new AjaxData(true,null,$record);
	}
}

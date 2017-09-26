<?php
/**
 * 当前库存
 * @access 仓库库存
 * @author liang
 * @package Controller
 * @version 0.1
 *
 */
class StockController extends Controller {

	/**
	 * 仓库库存列表
	 * @access 仓库库存列表
	 */
	public function actionIndex() {
		$warehouseId = (int)Yii::app()->request->getQuery('warehouseId');
		$singleNumber = trim ( Yii::app()->request->getQuery('singleNumber') );
		$productBatch = trim ( Yii::app()->request->getQuery('productBatch') );

		$totalNum = 0;
		$flag = false;

		$criteria = new CDbCriteria;
		$criteria->addCondition( 't.num != 0 ');
		if( $singleNumber ){
			$criteria->addSearchCondition('t.singleNumber',$singleNumber);
			$flag = true;
		}

		if( $warehouseId ){
			$criteria->compare('t.warehouseId',$warehouseId);
		}

		if( $productBatch ){
			$criteria->compare('t.productBatch',$productBatch);
			$flag = true;
		}

		$criteria->order = 'warehouseId ASC,singleNumber asc,productBatch asc,positionId asc';
		$pageSize =  tbConfig::model()->get('page_size');
		$model = new CActiveDataProvider('tbWarehouseProduct', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
		));
		$list = $model->getData();
		$pages = $model->getPagination();

		$warehouse = tbWarehouseInfo::model()->getAll();

		if( $flag && !empty($list) ){
			$c = clone $criteria;
			$c->select = ' sum( `num` )  as num ';
			$m = tbWarehouseProduct::model()->find($c);
			if( $m->num > 0 ) $totalNum = $m->num;
		}

		$this->render( 'index', array('list' => $list,'pages'=>$pages,'warehouseId'=>$warehouseId,'singleNumber'=>$singleNumber,'productBatch'=>$productBatch,'warehouse'=>$warehouse,'totalNum'=>$totalNum ) );
	}
}
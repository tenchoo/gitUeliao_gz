<?php
/**
 * 产品单品编号搜索
 * @author yagas-office
 *
 */
class SearchProductSerial extends CAction {

	public function run() {
		$serial    = Yii::app()->request->getParam('serial');
		$warehouseId  = Yii::app()->request->getParam('warehouseId');

		if( is_null($serial) || empty($serial) ) {
			return new AjaxData(false);
		}

		if( is_numeric( $warehouseId ) && $warehouseId >0 ){
			//若有仓库参数，从仓库里查数据
			$criteria = new CDbCriteria();
			$criteria->select ='singleNumber';
			$criteria->compare( 't.warehouseId', $warehouseId );
			$criteria->addSearchCondition( 't.singleNumber', $serial, true );
			$criteria->addCondition( 't.num>0 ' );
			$criteria->distinct = true; //是否唯一查询
			$criteria->limit = tbConfig::model()->get('search_tip_size');
			$criteria->order = 'singleNumber ASC';
			$result = tbWarehouseProduct::model()->findAll( $criteria );

			if( !$result ){
				return new AjaxData(true,null,array());
			}

			$singleNumbers = array_map( function( $i ) { return $i->singleNumber;} ,$result );

			$criteria = new CDbCriteria();
			$criteria->compare( 't.singleNumber', $singleNumbers );
		}else{
			$criteria = new CDbCriteria();
			$criteria->addSearchCondition( 't.singleNumber', $serial, true );
			$criteria->limit = tbConfig::model()->get('search_tip_size');
		}

		$result = tbProductStock::model()->findAll( $criteria );

		if( $result ) {
			$listData = array();
			foreach( $result as $item ) {
				$row = array(
						'id'        => $item->stockId,
						'productid' => $item->productId,
						'title'    => $item->singleNumber,
						'color'     => $item->color,
						'unit'      => ZOrderHelper::getUnitName($item->singleNumber)
				);
				array_push($listData, $row);
			}
			return new AjaxData(true,null,$listData);
		}
		return new AjaxData(false,Yii::t("api","Not Found Data"));
	}
}
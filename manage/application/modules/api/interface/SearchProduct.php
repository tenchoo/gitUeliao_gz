<?php
/**
 * 产品编号搜索
 * @author liang
 *
 */
class SearchProduct extends CAction {

	public function run() {
		$serial    = Yii::app()->request->getParam('serial');

		if( is_null($serial) || empty($serial) ) {
			return new AjaxData(false);
		}


		$criteria = new CDbCriteria();
		$criteria->addSearchCondition( 't.serialNumber', $serial, true );
		$criteria->compare( 't.state', array('0','1') );
		$criteria->limit = tbConfig::model()->get('search_tip_size');
		$criteria->order = 'serialNumber asc';

		$result = tbProduct::model()->findAll( $criteria ,'state');

		if( $result ) {
			$listData = array();
			foreach( $result as $item ) {
				$row = array(
						'id'        => $item->productId,
						'title'    => $item->serialNumber,
				);
				array_push($listData, $row);
			}
			return new AjaxData(true,null,$listData);
		}
		return new AjaxData(false,Yii::t("api","Not Found Data"));
	}
}
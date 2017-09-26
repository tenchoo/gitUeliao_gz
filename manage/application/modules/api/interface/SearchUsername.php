<?php
/**
 * 产品编号搜索
 * @author liang
 *
 */
class SearchUsername extends CAction {

	public function run() {
		$username    = Yii::app()->request->getParam('username');

		if( is_null($username) || empty($username) ) {
			return new AjaxData(false);
		}


		$criteria = new CDbCriteria();
		$criteria->addSearchCondition( 't.username', $username, true );
		$criteria->compare( 't.state', '0' );
		$criteria->limit = tbConfig::model()->get('search_tip_size');
		$criteria->order = 'username asc';

		$result = tbUser::model()->findAll( $criteria );

		if( $result ) {
			$listData = array();
			foreach( $result as $item ) {
				$row = array(
						'id'        => $item->userId,
						'title'    => $item->username,
				);
				array_push($listData, $row);
			}
			return new AjaxData(true,null,$listData);
		}
		return new AjaxData(false,Yii::t("api","Not Found Data"));
	}
}
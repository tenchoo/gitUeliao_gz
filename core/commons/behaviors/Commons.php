<?php
class Commons extends CBehavior {

	/**
	 * 记录最近浏览的产品
	 */
	public function viewLog( $id ) {
		$cookie = Yii::app()->request->cookies['view'];
		if( is_null($cookie) ) {
			$cookie = new CHttpCookie('view','');
		}
		$ids = empty( $cookie->value )? array() : explode( ';', $cookie->value );
		if( ($index = array_search($id, $ids)) !== false ) {
			unset( $ids[$index] );
		}
		array_unshift( $ids, $id );
		$ids = array_slice( $ids, 0, 10 );
		$cookie->value = implode( ';', $ids );
		Yii::app()->request->cookies['view'] = $cookie;
	}
}
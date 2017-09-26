<?php
/**
 * 网铺管理入口
 * @author yagas
 * @package CWidet
 * @version 0.1
 */
class widgetShoplink extends CWidget {
		
	public function run() {
		if( !$this->getIsOpened() ) {
			$linkName = "我要开店";
			$route    = '/shop/apply';
		}
		else {
			$linkName = "店铺管理";
			$route    = '/shop/index';
		}
		
		$route = $this->owner->getSiteUrl( 'eshop',array('route'=>$route) );
		echo CHtml::link( $linkName, $route );
	}
	
	private function getIsOpened() {
		$domain = 'eshop'.strstr( $_SERVER['HTTP_HOST'], '.' );
		$url    = 'http://'.$domain.'/service/getIsOpened';
		$param  = array( "memberId" => Yii::app()->user->id );
		$state  = ZService::getUrlData( $url, $param );
		return ($state == 'true')? true : false;
	}
}
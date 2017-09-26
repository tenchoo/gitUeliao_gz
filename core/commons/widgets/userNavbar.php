<?php
/**
 * 会员中心用户导航菜单
 * @author yagas
 * @package CWidget
 * @version 0.1
 * @date 11/07/2015
 */
class userNavbar extends CWidget {
	
	public $menus = array();
	private $_api = array();
	
	/**
	 * 获取项目路径
	 * @param string $route
	 */
	private function fetchUrl( $route ) {
		list($domain,$route) = explode('::', $route);
		if( !array_key_exists($domain, $this->_api) ) {
			$this->_api[ $domain ] = new ApiClient( $domain );
		}
		return $this->_api[ $domain ]->createUrl( $route );
	}
	
	/**
	 * 执行物件操作
	 * @see CWidget::run()
	 */
	public function run() {
		$menus = $this->getMenuData();
		if( !is_array( $menus ) ){
			return;
		}
		
		$code = CHtml::tag('ul',array('class'=>'frame-nav text-center text-bold list-unstyled'),'',false);

		foreach ( $menus as $item ) {
			$option = array();
			//$url    = $this->fetchUrl( $item['route'] );					
			$link   = CHtml::link($item['title'], $item['url']);

			if( $item['route'] == $this->owner->routeFlag ) {
				$option['class'] = "active";
				$link .= "<span></span>";
			}
			
			$code  .= CHtml::tag( "li", $option, $link );
		}
		$code .= "</ul>";
		echo $code;
	}
	
	/**
	 * 获取菜单项数据
	 */
	public function getMenuData() {
		$data = array(
			array(
				"title" => "首页",
				"route" => "member::/site/index",
				'url'=>'/',
			),
			array(
				"title" => "账号设置",
				"route" => "member::/membercenter/info",
				'url'=>'/membercenter/info.html',
			),
 			array(
 				"title" => "消息",
 				"route" => "member::/message",
				'url'=>'/message.html',
 			)
		);
		
		return $data;
		
	}
	
	/**
	 * 根据会员网铺的开通状态，返回不同类型的菜单项
	 * @param array $data
	 * @return array
	 */
	private function shopIsOpen( $data ) {
		$api = new ApiClient( 'eshop' );
		if( $api->getIsOpened( Yii::app()->user->id ) === 'true' ) {
			array_push( $data, array(
				"title" => "店铺管理",
				"route" => "eshop::/shop/index"
			));
		}
		else {
			array_push( $data, array(
				"title" => "申请开铺",
				"route" => "eshop::/shop/apply"
			));
		}
		return $data;
	}
}
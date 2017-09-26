<?php
/**
 * 会员中心左侧菜单物件
 * @author yagas
 * @version 0.1
 * @package CWidget
 */
class userMenubar extends CWidget {

	public $title='会员中心';

	/**
	 * 加载左侧导航菜单数据文件
	 * @param string $controllerName 控制器名称
	 * @throws CException
	 * @return multitype:NULL multitype: string
	 */
	private function _loadData() {
		$userType = Yii::app()->user->getState('usertype');
		$type = $this->getCotrollerType();
		$cacheName = 'usernav_'.$userType.'_'.$type;
		$data = Yii::app()->cache->get( $cacheName );//获取缓存
		if( !empty($data) ){
			return $data;
		}


		if( $userType == 'saleman' ){
			$this->title = '业务员个人中心';
		}

		$group = tbNav::model()->find( "title=:title", array(':title'=>$this->title) );
		if( $group ) {
			$criteria = new CDbCriteria();
			$criteria->condition = "state=1 and parentId=:parent and type=:type";
			$criteria->params = array( ':parent'=>$group->navId, 'type'=>$type );
			$criteria->order = "listOrder ASC";
			$result = tbNav::model()->findAll( $criteria );

			if( $result ) {
				$result = array_map( function( $item ){
					$row = $item->getAttributes();
					$condition = new CDbCriteria();
					$condition->condition = "parentId=:parent and state=1";
					$condition->order = "listOrder ASC";
					$condition->params = array(':parent'=>$item->navId );
					$row['childrens'] = tbNav::model()->findAll( $condition );
					if( $row['childrens'] ) {
						$row['childrens'] = array_map( function($menu){
							$item = $menu->getAttributes();
							list($subtitle,$subroute) = explode('::',$item['route']);
							if(empty($subroute)) {
								$subroute = "javascript:;";
							}

							$url =  'http://'.$subtitle.DOMAIN.$subroute;
							/* $api = new ApiClient($subtitle,'service');
							$url = $api->createUrl($subroute); */
							$item['link']  = CHtml::link( $item['title'], $url );
							return $item;
						}, $row['childrens'] );
					}
					return $row;
				}, $result );
			}
			Yii::app()->cache->set( $cacheName,$result,3600*24 );
			return $result;
		}
		return array();
	}

	/**
	 * 当前的菜单分组
	 */
	private function getCotrollerType() {
		$route = 'member::'.$this->getRoute();
		$result = tbNav::model()->findByAttributes(array("route"=>$route));
		if($result instanceof CActiveRecord) {
			return intval($result->type);
		}
		return 0;
	}

	/**
	 * 获取当前路由
	 * @return string
	 */
	private function getRoute() {
		$route = strtolower( '/' . $this->owner->getRoute() );

		switch( $route ){
			case '/order/settlement/view':
				$route = '/order/settlement/index';
				return $route;
				break;
			CASE '/order/modity/cview':
			CASE '/order/modity/checkchange':
				return '/order/modity/changelist';
				break;
			CASE '/order/modity/change':
				return '/order/default/index';
				break;
			CASE '/order/modity/view':
			CASE '/order/modity/checkclose':
				return '/order/modity/index';
				break;
			CASE '':
				return '/order/modity/changelist';
				break;
		}

		if( strpos($route,"/order/comment") === 0 ){
			return '/order/comment/index';
		}

		if( strpos($route,"/order/default") === 0 ){
			return '/order/default/index';
		}

		if( strpos($route,"/member") === 0 && $route != '/member/add'){
			return '/member';
		}

		if( strpos($route,"/applyprice") === 0 ){
			return '/applyprice/default/index';
		}

		if( strpos($route,"/message") === 0 ){
			return '/message';
		}
		
		if( strpos($route,"/address") === 0 ){
			return '/address';
		}

		if( strpos($route,"/order/refund") === 0 ){
			return '/order/refund/index';
		}

		return $route ;
	}

	/**
	 * 渲染物件
	 * @see CWidget::run()
	 */
	public function run() {
		$controllerName = strtolower( $this->owner->id );
		$data           = $this->_loadData();
		$route = $this->getRoute();
		ob_start();
		echo '<div class="pull-left list-unstyled frame-menu">';
		foreach ( $data as $menu ) {
			echo '<h3 class="active"><span><i></i></span>'.$menu['title'].'</h3>';
			echo '<ul class="list-unstyled">';
			if( $menu['childrens'] ) {
				foreach( $menu['childrens'] as $item ) {
					$options = array();
					list($subtitle,$subroute) = explode('::',$item['route']);
					if( $route === $subroute) {
						$options['class'] = 'active';
					}
					echo CHtml::tag( 'li', $options, $item['link'] );
				}
			}
			echo '</ul>';
		}
		echo "</div><script>seajs.use('app/member/frame/js/menu.js');</script>";
		ob_end_flush();
	}
}
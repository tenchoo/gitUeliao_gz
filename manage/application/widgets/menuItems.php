<?php
class menuItems extends CWidget {

	public $index;
	private $_activeId;

	//菜单路径
	private $_path = array();

	public function run() {
		ob_start();
		$menus = $this->fetchMenus();
		if( $menus ) {
			foreach( $menus as $k=>$item ) {
				echo '<div class="panel panel-default">
		<div class="panel-heading">';
				echo "<h4 class=\"panel-title\" data-toggle=\"collapse\" data-target=\"#panel".$k."\"><a href=\"javascript:\">{$item['title']}</a></h4>
				</div>
				<div class=\"panel-collapse list-group collapse in\" id=\"panel".$k."\">";

				if( $item['childrens'] ) {
					foreach( $item['childrens'] as $menu ) {
						$class = 'list-group-item';
						if( $menu['menuId'] == $this->_activeId ){
							$class .= ' active';
						}

						echo CHtml::link($menu['title'],$menu['url'],array('class'=>$class));
					}
				}

				echo '</div>
		</div>';
			}
		}
		ob_end_flush();
	}

	public function fetchMenus() {
		$route = sprintf("%s:/%s", Yii::app()->name, $this->owner->getRoute() );
//		$route = '/'.$this->owner->getRoute();
		if( $route == 'manage:/default/index' ){
//		if( $route == '/default/index' ){
			$id = (int)Yii::app()->request->getQuery('id','1');
			$this->findToRoot( $id );
			$second_menus = $this->fetch( $this->_path[0]['menuId'] );
			if( $second_menus ) {
				return $second_menus;
			}
		}else{
			$menu = tbMenu::model()->findByAttributes( array('route'=>$route) );
			if( $menu ) {
				array_unshift($this->_path, $menu->getAttributes());
				$this->findToRoot( $menu->parentId );
				$menus_depth = array_slice( $this->_path, 0, 3 );
				$active = array_pop( $menus_depth );
				$this->_activeId = $active['menuId'];
				$second_menus = $this->fetch( $this->_path[0]['menuId'] );
				if( $second_menus ) {
					return $second_menus;
				}
			}
		}
		return array();
	}

	/**
	 * 获取菜单路径
	 * @param integer $id 菜单ID
	 * @return bool
	 */
	public function findToRoot( $id ) {
		if( $id == 0 ) {
			return false;
		}
		$menu = tbMenu::model()->findByPk( $id );
		if( $menu instanceof CActiveRecord ) {
			array_unshift($this->_path, $menu->getAttributes());
			$this->findToRoot( $menu->parentId );
		}
	}

	private function fetch( $id ) {
		$criteria            = new CDbCriteria();
		$criteria->condition = "parentId=:id and hidden=0";
		$criteria->params    = array(':id'=>$id);
		$criteria->order     = "orderList ASC ,menuId ASC";
		$childrens = tbMenu::model()->findAll( $criteria );
		if( $childrens ) {
			foreach ( $childrens as & $item ) {
				$item = $item->getAttributes();
				$item['childrens'] = $this->fetch( $item['menuId'] );
			}
		}
		return $childrens;
	}
}
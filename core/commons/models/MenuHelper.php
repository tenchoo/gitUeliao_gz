<?php
/**
 * 菜单数据获取助手
 * @author yagas
 * @version 0.1
 * @package CApplicationComponent
 */
class MenuHelper extends CApplicationComponent {
	private $_parentId;
	
	public function __construct( $itemName ) {
		$result = tbMenu::model()->find( "title=:menu", array(':menu'=>$itemName) );
		if( $result instanceof CActiveRecord ) {
			$this->_parentId = $result->navId;
		}
	}
	
	/**
	 * 递归通过父ID获得子菜单项目
	 * @param integer $parentId
	 * @return array
	 */
	public function getChildren( $parentId ) {
		$criteria         = new CDbCriteria();
		$criteria->select = "navId,title,route";
		$criteria->compare("parentId", $parentId);
		$criteria->compare("state", 1);
		$criteria->order = "listOrder ASC";
		$result = tbMenu::model()->findAll( $criteria );
		
		if( !$result ) {
			return array();
		}
		
		//使用迭代器循环处理结果
		$iterator = new ArrayIterator( $result );
		while( $iterator->valid() ) {
			$row              = $iterator->current()->getAttributes();
			$row['url']       = $this->createUrl( $row['route'] );
			$row['childrens'] = $this->getChildren( $row['navId'] );
			
			$iterator->offsetSet( $iterator->key(), $row );
			$iterator->next();
		}
		return $iterator;
	}
	
	/**
	 * 获取菜单url地址
	 * @param string $string
	 * @return string
	 */
	public function createUrl( $string ) {
		if( empty($string) ) {
			return '';
		}
		
		list($prefix,$route) = explode('::', $string);
		$api = new ApiClient( $prefix );
		return $api->createUrl( $route );
	}
	
	/**
	 * 获取菜单数据
	 * @return array
	 */
	public function fetchAll( $raw = false ) {
		if( is_null($this->_parentId) ) {
			return array();
		}
		
		$menus = $this->getChildren( $this->_parentId );
		if( $raw ) {
			return $menus;
		}
		else {
			return $menus->getArrayCopy();
		}
	}
	
	/**
	 * 快捷调用助手方法
	 * @param string $menuItem
	 * @return array|null
	 */
	static public function run( $menuItem, $raw=false ) {
		$menu = new MenuHelper( $menuItem );
		return $menu->fetchAll( $raw );
	}
}
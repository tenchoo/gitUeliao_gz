<?php
class CategoryMenu extends CWidget {

	protected $list_url = "default/product";

	public function run() {
		$params = array();
		$params['c'] = Yii::app()->request->getQuery('c',0);
		$currentPath          = tbCategory::model()->findParent( $params['c'] );
		$params['cur_level1'] = $currentPath[0]['categoryId'];
		$params['cur_level2'] = isset($currentPath[1])? $currentPath[1]['categoryId'] : 0;
		$params['cur_level3'] = isset($currentPath[2])? $currentPath[2]['categoryId'] : 0;
		$params['categorys']  = $this->fetchCategoryData();

		$this->render( 'default', $params );
	}

	private function fetchCategoryData() {
		$id = 'global.category.data';
		if( Yii::app()->cache->offsetExists( $id ) ) {
			return Yii::app()->cache->get( $id );
		}
		else {
			$tree = tbCategory::model()->getTrees(0,true);
			Yii::app()->cache->set( $id, $tree, 60 );
			return $tree;
		}
	}

	public function createUrl( $title, $params ) {
		$action = $this->owner->getRoute();
		if( $action == 'default/tail' ){
			$this->list_url = $action;
		}

		$url = $this->owner->createUrl( $this->list_url, $params );
		return CHtml::link( $title, $url );
	}
}
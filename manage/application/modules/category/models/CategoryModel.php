<?php
class CategoryModel extends CFormModel {
	
	public $title;
	public $parentId;
	
	
	public function insert( $lists ) {
		foreach( $lists as $item ) {
			$id = $this->write(
				array(
					'categoryId' => $item['categoryId'],
					'title'      => $item['title'],
					'parentId'   => $item['parentId']
				)
			);
			
			if( !is_null( $id ) ) {
				$item['categoryId'] = $id;
			}
			
			if( isset($item['childrens']) ) {
				foreach( $item['childrens'] as & $children ) {
					$children['parentId'] = $id;
				}
				$this->insert( $item['childrens'] );
			}
		}
		return !$this->hasErrors();
	}
	
	
	protected function write( $row ) {
		$category = null;
		$this->setAttributes( $row );
		if( $this->validate() ) {
			$category = tbCategory::model()->find( "categoryId=:cid", array(":cid"=>$row['categoryId']) );
			if( is_null($category) ) {
				$category = new tbCategory;
				unset($row['categoryId']);
			}
			
			$category->setAttributes( $row );
			$result = $category->save();
			if( $result ) {
				return $category->categoryId;
			}
			$this->addError('insert', sprintf("%s unable to save.", $row['title']) );
			return;
		}
	}
}
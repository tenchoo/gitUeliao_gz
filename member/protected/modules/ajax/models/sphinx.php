<?php
class sphinx extends CModel {
	
	public function attributeNames() {
		return false;
	}
	
	public function findAll( $keyword ) {
		$sp = new sphinx();
	}
}
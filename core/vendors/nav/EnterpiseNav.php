<?php
/**
 * 商家中心菜单
 * @author morven
 * 
 */
class EnterpiseNav extends Templates{
	
	/**
	 * 保存菜单
	 * @param array $attributes 保存条件
	 * @param int $id 模板ID
	 * @return boolean
	 */
	public function save( $attributes , $id =null){
		if($id==null){
			$model = new Nav();
		}else{
			$model = Nav::model()->findByPk($id);
		}
		$model->attributes = $attributes;
		$model->type = 2;
		if($model->validate() && $model->save()){
			return true;
		}else{
			return $model->errors;
		}
	}
}
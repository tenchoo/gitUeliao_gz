<?php
/**
 * 菜单基础类
 * @author morven
 *
 */
abstract class Nav {

	
	
	/**
	 * 查询单条数据
	 * @param int $id 主键ID
	 * @return static
	 */
	public function findOne($id){
		$model = Nav::model()->findByPk($id);
		return $model;
	}
	
			
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
		if($model->validate() && $model->save()){
			return true;
		}else{
			return $model->errors;
		}
	}
	
	/**
	 * 查询所有菜单
	 * @param array $attributes 保存条件
	 * @param int $id 模板ID
	 * @return boolean
	 */
	public function findAll( $attributes ){
		$model = Nav::model()->findAll($attributes);		
		if($model){
			return $model;
		}else{
			return false;
		}
	}
	
	/**
	 * 查询菜单列表
	 * @param int $page 分页数
	 * @param array $condition 搜索条件
	 * @return CActiveDataProvider
	 */
	public function search( $condition=array('type'=>0,'state'=>1) , $page=10 ){	
		$criteria=new CDbCriteria;		
		if(!empty($condition)){
			foreach ($condition as $key=>$val){
			$criteria->compare($key,$val,true);
			}
		}
		$criteria->order = "listOrder ASC";//默认为时间倒序
		return new CActiveDataProvider('Nav', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$page),
		));
	}
	
  
}


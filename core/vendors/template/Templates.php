<?php
/**
 * 网铺模板
 * @author morven
 *
 */
abstract class Templates {

	public $select = 'templateId,title,image';
	
	/**
	 * 查询单条数据
	 * @param int $id 主键ID
	 * @return static
	 */
	public function find($id){
		$model = EshopTemplates::model()->findByPk($id);
		return $model;
	}
	
			
	/**
	 * 保存模板信息
	 * @param array $attributes 保存条件
	 * @param int $id 模板ID
	 * @return boolean
	 */
	public function save( $attributes , $id =null){
		if($id==null){
			$model = new EshopTemplates();
		}else{
			$model = EshopTemplates::model()->findByPk($id);
		}
		$model->attributes = $attributes;
		if($model->validate() && $model->save()){
			return true;
		}else{
			return false;
		}
	}
	
	
	/**
	 * 查询模板列表
	 * @param int $page 分页数
	 * @param array $condition 搜索条件
	 * @return CActiveDataProvider
	 */
	public function search($page=10,$condition=array('type'=>0,'status'=>1) ){	
		$criteria=new CDbCriteria;
		$criteria->select = $this->select;
		if(!empty($condition)){
			foreach ($condition as $key=>$val){
			$criteria->compare($key,$val,true);
			}
		}
		$criteria->order = "listOrder ASC, createTime DESC ";//默认为时间倒序
		return new CActiveDataProvider('EshopTemplates', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$page),
		));
	}
	
  
}


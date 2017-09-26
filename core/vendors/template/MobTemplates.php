<?php
/**
 * 手机端模板
 * @author morven
 *
 */
class MobTemplates extends Templates{
	
	public function search( $page=10 ,$condition=array('type'=>1,'status'=>1) )
	{
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
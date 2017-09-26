<?php

/**
 * 行业分类数据库表模型 "{{category}}"
 *
 * 数据库表 '{{eshop_apply}}'包含以下字段:
 * @property integer $categoryId
 * @property integer $title
 * @property integer $parentId
*/
class Category extends CActiveRecord {

	/**
	 * 构造数据库表模型单例方法
	 * @param system $className
	 * @return static
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{category}}';
	}
	
	
	public function getCache(){
		$data = json_decode(Yii::app()->cache->get('category'),true);//获取缓存		
		//如果缓存不存在，则直接读取数据库
		if(empty($data)){
			$model = $this->findAll();
			$data = json_decode(CJSON::encode($model),TRUE);
			Yii::app()->cache->set('category',CJSON::encode($data),3600*24*30);
			return $data;
		}
		else{
			return $data;
		}
	}
	
	/**
	* 取得其分类下的所有子分类的ＩＤ,含自身ID。
	* @param integer $categoryId
	* @return array $categoryIds
	* @use 规格属性继承时调用
	*/	
	public function getAllExtendids( $categoryId ){
		$categoryIds[] =   $categoryId;
		$data =  $this->getCache();
		foreach ( $data as $val ){
			if( in_array( $val['parentId'],$categoryIds ) ){
				$categoryIds[] =  $val['categoryId'];
			}
		}
		return $categoryIds;
	}
	
	/*
	* 取得当前分类名称
	* @param integer $categoryId
	*/
	public function getInfo( $categoryId ){
		$model = $this->findbyPk( $categoryId );
		return $model;
	}

}
?>
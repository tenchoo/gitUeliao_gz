<?php

/**
 * 帮助信息表模型
 *
 * @property integer	$helpId
 * @property integer	$categoryId		帮助分类
 * @property integer	$state			状态：0正常，1删除
 * @property timestamp	$createTime
 * @property timestamp	$updateTime
 * @property string		$title			标题
 * @property string		$content		内容
 * @version 0.1
 * @package CActiveRecord
 */

class tbHelp extends CActiveRecord {

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
		return '{{help}}';
	}

	/**
	 * @return array 模型验证规则.
	 */
	public function rules()	{
		return array(
			array('categoryId,title,content','required'),
			array('categoryId', 'numerical','integerOnly'=>true,'min'=>'0'),
			array('title,content','safe'),

		);
	}

	public function attributeLabels(){
		return array(
			'content'=>'内容',
			'title'=>'标题',
			'categoryId'=>'分类ID',
		);

	}


	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
		}
		$this->updateTime = new CDbExpression('NOW()');
		return true;
	}

	/**
	* 查找分类列表
	* @param array   $condition  搜索条件
	* @param integer $pageSize   每页条数
	*/
	public static function search( $condition = array(),$pageSize = '2'){

		$criteria=new CDbCriteria;
		foreach ( $condition  as $key=>$val ){
			if($val == '' ) continue;
			if( $key == 'title'){
				$criteria->compare('t.'.$key,$val,true);
			}else{
				$criteria->compare('t.'.$key,$val);
			}
		}
		$criteria->compare('state','0');
		$criteria->order = 'createTime DESC';
		$model = new CActiveDataProvider('tbHelp',array(
					'criteria'=>$criteria,
					'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
				));
		$data = $model->getData();
		$return['list'] = array_map(function ($i){
							$i->updateTime = date('Y/m/d',strtotime( $i->updateTime ));
							return $i->attributes;},$data);
		$return['pages']= $model->getPagination();
		return $return;
	}
}
?>
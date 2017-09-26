<?php

/**
 * 帮助分类表模型
 *
 * @property integer $categoryId
 * @property integer $type			类型：0列表，1单页
 * @property integer $state			状态：0正常，1删除
 * @property integer $parentId		上级分类ID
 * @property integer $listOrder		排序值
 * @property string  $title			分类名称
 * @version 0.1
 * @package CActiveRecord
 */

class tbHelpCategory extends CActiveRecord {

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
		return '{{help_category}}';
	}

	/**
	 * @return array 模型验证规则.
	 */
	public function rules()	{
		return array(
			array('type,title,parentId','required'),
			array('type','in','range'=>array(0,1)),
			array('parentId', 'numerical','integerOnly'=>true),
			array('title','length','min'=>'2','max'=>'15'),
			array('title','safe'),

		);
	}

	public function attributeLabels(){
		return array(
			'type'=>'类型',
			'listOrder'=>'排序值',
			'title'=>'分类名称',
			'parentId'=>'上级分类ID',
		);

	}

	/**
	* 取得全部分类
	*/
	public function getTree(){
		$model = $this->findAll(array(
		  'condition'=>'state = 0',
		  'order' => 'parentId ASC,listOrder ASC',
		));

		$result = array();
		foreach ( $model as $val ){
			$result[$val->categoryId] = $val->attributes;
			if($val->parentId != '0'){
				$result[$val->parentId]['childs'][] = $val->attributes;
			}
		}
		return $result;
	}


	/**
	* 取得分类信息
	* @param integer  $parentId
	* @param boolean  $single	 是否包含单页
	*/
	public static function getByParentId( $parentId,$issingle = true ){
		$criteria = new CDbCriteria;
		$criteria->compare('t.parentId',$parentId);
		$criteria->compare('t.state','0');
		if( !$issingle ){
			$criteria->addCondition('t.type !=1');
		}


		$criteria->order = 'parentId ASC,listOrder ASC';
		$data = self::model()->findAll( $criteria );

		foreach($data as $key=>&$val){
			$c = 0;
			if( $parentId=='0' ){
				$c = $val->count('state = 0 and type = 0 and parentId = '.$val->categoryId);
				if( !$issingle && $c == '0' ){
					$c = $val->count('state = 0 and parentId = '.$val->categoryId);
					if( $c ){
						unset($data[$key]);//过滤掉子分类全部是单页的一级分类。
						continue;
					}
				}
			}

			$val = $val->getAttributes(array('categoryId','title','parentId','type'));
			$val['childrens'] = ($c)?true:false;
		}
		return $data;
	}

	/**
	* 删除某一分类ID
	* @param integer  $id		 分类表PK
	* @param integer  $message	 提示信息
	*/
	public function del( $id,&$message ){
		if(!is_numeric($id) || $id<1 ) return false;

		$model = $this->findByPk( $id );
		if( !$model ) return false;

		//如果是列表，删除前要先判断列表下是否有信息，如有信息，返回提示：列表下有信息，请先删除信息。
		if( $model->type == '0' ){
			//查找是否有子分类，有子分类不允许删除
			$count = $this->count('state = 0 and parentId = '.$id);
			if($count){
				$message = Yii::t('category','This classifier has a sub category, which is not allowed to delete.');
				return false;
			}

			//列表
			$count = tbHelp::model()->count( 'state=0 and categoryId = '.$id );
			if($count){
				$message = Yii::t('category','Under this classification and information, please delete the information');
				return false;
			}
		}

		$model->state = '1';
		if( $model->save() ){
			$message = null;
			return true;
		}
		return false;
	}


	/**
	* 帮助类目管理--更改页面类型,如果原类型是列表，需判断列表是否为空，不为空时提示请先删除或转移帮助内容。
	* @param integer  $id		 分类表PK
	* @param integer  $type		 页面类型
	* @param integer  $message	 提示信息
	*/
	public function changetype( $id, $type,&$message ){
		if(!is_numeric($id) || $id<1 || !in_array($type,array('0','1'))) return false;

		$model = $this->findByPk( $id,'type!=:type',array(':type'=>$type));
		if( !$model ) return false;

		//原类型是列表，需判断列表是否为空
		if( $model->type == '0' ){
			//列表
			$count = tbHelp::model()->count( 'state=0 and categoryId = '.$id );
			if($count){
				$message = Yii::t('category','This classification has help information and is not allowed to turn into a single page. Please delete or transfer the information to help the information to be transferred.');
				return false;
			}
		}
		$model->type = $type;
		if( $model->save() ){
			$message = null;
			return true;
		}
		return false;
	}


	/**
	* 设置排序
	* @param integer  $id		分类表PK
	* @param integer  $goto  	移动方向，上升(up)或下降(down)
	*/
	public function changePosition( $id,$goto ){
		if(!is_numeric($id) || $id<1 || !in_array( $goto,array('up','down')) ) return false;

		$model = $this->findByPk( $id );
		if( !$model ) return false;

		$c = new CDbCriteria;
		$c->compare('parentId',$model->parentId);

		if( $goto == 'up'){
			$c->addCondition('listOrder <'.$model->listOrder);
			$c->order = 'listOrder desc';
		}else{
			$c->addCondition('listOrder >'.$model->listOrder);
			$c->order = 'listOrder asc';
		}

		$model2 = $this->find( $c );
		if( !$model2 ) return false;

		//使用事务处理
		$transaction = Yii::app()->db->beginTransaction();
		try {
			$listOrder = $model->listOrder;
			$model->listOrder = $model2->listOrder;
			$model2->listOrder = $listOrder;
			if( !$model->save() || !$model2->save() ){
				return false;
			}
			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			throw new CHttpException(503,$e);
			return false;
		}
	}

	/**
	* 新增时设置排序值
	*/
	private function setlistOrder(){
		$model = $this->find( array(
		  'select'=>'MAX(listOrder) as listOrder',
		));
		$this->listOrder = 1 + $model->listOrder;
	}

	protected function beforeSave()	{
		if( $this->isNewRecord ) {
			$this->setlistOrder();
		}
		return true;
	}
}
?>
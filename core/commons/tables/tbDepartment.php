<?php
/**
 * 部门表
 *
 * @property integer	$departmentId
 * @property integer	$state			状态：0正常，1删除
 * @property timestamp	$createTime		新建时间
 * @property string		$departmentName	部门名称
 *
 */

class tbDepartment extends CActiveRecord
{
	/**
	 * 返回基于自身的AR实例
	 * @param string $className 类名
	 * @return CActiveRecord 实例
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string 返回表名
	 */
	public function tableName()
	{
		return '{{department}}';
	}


	/**
	 * @return array 模型验证规则.
	 */
	public function rules()
	{
		return array(
			array('departmentName', 'required'),
			array('departmentName', 'length', 'max'=>12, 'min'=>2),
			array('state','in','range'=>array(0,1)),
			array('departmentName','safe'),
			array('departmentName','unique','criteria'=>array('condition'=>'state = 0') ),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'departmentName' => '部门名称',
		);
	}

	/**
	* 取得仓库列表
	*/
	public function getAll(){
		$result = array();
		$model = $this->findAll('state = 0');
		foreach ($model as $val){
			$result[$val->departmentId] =  $val->departmentName;
		}
		return $result;
	}

	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
		}
		return true;
	}
	
	/**
	* 查询/列表
	* @param array $condition 查询的条件
	* @param integer $perSize 每页显示条数
	*/
	public function search( $condition = array(),$perSize = 1 ){
		$criteria = new CDbCriteria;
		if( is_array($condition) ){
			foreach ( $condition as $key=>$val ){
				if( $val=='' ) continue;
				$criteria->compare('t.'.$key,$val);
			}
		}
		$criteria->compare('t.state','0');
		$model = new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$perSize,'pageVar'=>'page'),
		));

		$result['list'] = $model->getData();
		$result['pages'] = $model->getPagination();
		return $result;
	}
}
<?php
/**
 * 部门职位表
 *
 * @property integer	$depPositionId
 * @property integer	$departmentId	所属部门ID
 * @property integer	$state			状态：0正常，1删除
 * @property timestamp	$createTime		新建时间
 * @property string		$positionName	职位名称
 *
 */

class tbDepPosition extends CActiveRecord
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
		return '{{dep_position}}';
	}


	/**
	 * @return array 模型验证规则.
	 */
	public function rules()
	{
		return array(
			array('departmentId,positionName', 'required'),
			array('positionName', 'length', 'max'=>12, 'min'=>2),
			array('state','in','range'=>array(0,1)),
			array('departmentId', "numerical","integerOnly"=>true),
			array('positionName','safe'),
			array('positionName','unique','criteria'=>array('condition'=>'state = 0') ),
		);
	}

	public function relations() {
		return [
			'department' => array(self::BELONGS_TO, 'tbDepartment', 'departmentId')
		];
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'positionName' => '职位名称',
			'departmentId' => '所属部门',
		);
	}

	/**
	* 取得仓库列表
	*/
	public function getAll(){
		$result = array();
		$model = $this->findAll('state = 0');
		foreach ($model as $val){
			$result[$val->departmentId] =  $val->title;
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

	/**
	* 根据部门ID取得职位信息
	* @param integer $id 部门ID
	*/
	public function getByDepId( $id ){
		$result = array();
		if( !empty( $id )){
			$data = tbDepPosition::model()->findAll( 'state = 0 and departmentId = '.$id );
			foreach ( $data as $val ){
				$result[$val->depPositionId] = $val->positionName;
			}
		}
		return $result;
	}
}
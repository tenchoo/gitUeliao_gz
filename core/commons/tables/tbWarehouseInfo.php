<?php
/**
 * 仓库信息表
 *
 * @property integer	$warehouseId
 * @property integer	$state			状态：0正常，1删除
 * @property integer	$areaId			所属地区ID
 * @property integer	$type			仓库类型
 * @property timestamp	$createTime		新建时间
 * @property string		$title			仓库名称
 *
 */

class tbWarehouseInfo extends CActiveRecord
{
	const STATE_NORMAL = 0;
	const STATE_DELETE = 1;

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
		return '{{warehouse_info}}';
	}


	/**
	 * @return array 模型验证规则.
	 */
	public function rules()
	{
		return array(
			array('title,type,areaId', 'required'),
			array('type','in','range'=>array(1,2,3)),
			array('areaId', "numerical","integerOnly"=>true,'min'=>'1'),
			array('title','unique'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'title' => '仓库名称',
			'areaId'=>'所在地区',
			'type'=>'仓库类型'
		);
	}

	/**
	* 取得仓库列表
	* @param integer $type 仓库类型
	*/
	public function getAll( $type = null ){
		$result = array();
		$c = new CDbCriteria;
		$c->compare( 'state',0 );

		$types = $this->types();
		if( array_key_exists( $type,$types ) ){
			$c->compare( 'type',$type );
		}

		$models = $this->findAll( $c );
		foreach ($models as $val){
			$result[$val->warehouseId] =  $val->title;
		}
		return $result;
	}

	/**
	* 仓库类型
	*/
	public function types(){
		return array('1'=>'普通仓','2'=>'样品仓','3'=>'损耗仓');
	}


	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
		}
		return parent::beforeSave();
	}

	/**
	* 取得仓库名称
	*@param int $warehouseId 所属仓库ID
	*/
   public function getWarehouseInfoTitle( $warehouseId ){
		   $model = $this->findByPk( $warehouseId );
			if($model){
				return $model->title;
			}
			 return '';
	 }

}
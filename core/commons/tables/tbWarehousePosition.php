<?php
/**
 * 仓库仓位/分区信息表
 *
 * @property integer	$positionId
 * @property integer	$warehouseId	所属仓库ID
 * @property integer	$parentId		所属仓库区域ID
 * @property integer	$state			状态：0正常，1删除
 * @property timestamp	$createTime		新建时间
 * @property string		$title			仓位名称
 *
 */

class tbWarehousePosition extends CActiveRecord
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
		return '{{warehouse_position}}';
	}


	/**
	 * @return array 模型验证规则.
	 */
	public function rules()
	{
		return array(
			array('title,warehouseId,parentId,printerId', 'required'),
			array('state','in','range'=>array(0,1)),
			array('parentId,warehouseId,printerId', "numerical","integerOnly"=>true),
			array('title', 'length', 'max'=>12, 'min'=>2),
			array('title','safe'),
			array('title','checkExists'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'title' => '仓位名称',
			'warehouseId'=>'所属仓库ID',
			'parentId'=>'所属仓库区域ID',
			'printerId'=>'默认打印机'
		);
	}

	/**
	* 检查是否存在，同一规格下，名称值编号不能重复,rules 规格
	*/
	public function checkExists( $attribute,$params ){
		if( empty( $this->$attribute ) ){
			return;
		}

		$criteria=new CDbCriteria;
		$criteria->compare($attribute,$this->$attribute);
		$criteria->compare('warehouseId',$this->warehouseId);
		$criteria->compare('parentId',$this->parentId);
		if( $this->positionId ){
			$criteria->addCondition("positionId !='".$this->positionId."'");
		}

		$model = self::model()->find( $criteria );
		if( $model ){
			$label = $this->getAttributeLabel($attribute);
			$this->addError($attribute,Yii::t('base','{attribute} already exists',array('{attribute}'=>$label)));
		}
	}



	/**
	* 删除分区/仓位
	* 若是分区，查找下面有没有仓位，若有仓位，不允许删除。
	* 若是仓位，查找当前仓位下面有没有相关库存信息，若有，不允许删除
	* @param integer $id 分区/仓位ID
	* @return boolean
	*/
	public function pdel( $id ){
		if( !is_numeric($id) || $id<1 ) return false;

		$model = $this->findByPk( $id,'state=0');
		if( !$model ){
			$this->addError('positionId','not found the position');
			return false;
		}
		if( $model->parentId>0 ){
			//仓位，查找当前仓位下面有没有相关库存信息，若有，不允许删除
			$flag = tbWarehouseProduct::model()->exists( 'positionId = :p',array(':p'=>$id) );
			if( $flag ){
				$this->addError('positionId','仓位下面拥有产品库存信息，不允许删除');
				return false;
			}
		}else{
			//分区，查找下面有没有仓位，若有仓位，不允许删除。
			$flag = $this->exists( 'state =:s and parentId = :p',array(':s'=>'0',':p'=>$id) );
			if( $flag ){
				$this->addError('positionId','分区下面拥有仓位，不允许删除');
				return false;
			}
		}

		$model->state = '1';//标删
		return $model->save();
	}

	/**
	* 取得仓库下面所有分区
	*/
	public function getAllZoning( $warehouseId,$type = '1' ){
		$model = $this->findAll( 'warehouseId=:w and state=:s and parentId=0',array(':w'=>$warehouseId,':s'=>'0') );
		if( $type == '1' ){
			$result = array_map( function($i){return $i->getAttributes(array('positionId','title'));},$model);
		}else{
			$result = array();
			foreach ( $model as $val ){
				$result[$val->positionId] = $val->title;
			}
		}

		return $result;
	}

	/**
	 * 仓位信息是否存在
	 * @param integer $storageId
	 * @param string $title
	 * @return boolean|integer
	 */
	public function positionExists( $warehouseId, $title ){
		$positions = $this->findByAttributes(array('warehouseId'=>$warehouseId,'title'=>$title,'state'=>'0'));
		if( $positions ) {
			return $positions->positionId;
		}
		return null;
	}


	/**
	 * 根据仓位ID查找仓位标题 仓位信息是否存在
	 * @param integer $storageId
	 * @param string $title
	 * @return boolean|integer
	 */
	public function positionName( $positionId,&$warehouseId ){
		$positions = $this->findByPk( $positionId );
		if( $positions ) {
			$warehouseId = $positions->warehouseId;
			return $positions->title;
		}
		return null;
	}

	/**
	* 分区类型
	*/
	public function areaTypes(){
		return array('1'=>'普通区','2'=>'流转区');
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
	* 取得所属仓库ID
	*@param int $positionId 仓位ID
	*/
   public function getWarehouseID( $positionId ){
		   $model = $this->findByPk( $positionId );
		if( $model ){
			return $model->warehouseId;
		}
		 return '';
   }

  /**
	* 取得仓位名称
	*@param int $positionId 仓位ID
	*/
   public function getWarehouseTitle( $positionId ){
		$model = $this->findByPk( $positionId );
		if( $model ) {
			return $model->title;
		}
		return null;
	}

}

<?php
/**
 * 仓库产品锁定信息
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$id
 * @property integer	$type				锁定来源：1分配单，2分拣单，3调拔单，4.待发货
 * @property integer	$sourceId			来源单ID
 * @property integer	$warehouseId		仓库ID
 * @property integer	$positionId			仓位ID
 * @property integer	$orderId			订单ID
 * @property numerical	$num				锁定数量
 * @property timestamp	$createTime			锁定时间
 * @property string		$singleNumber		单品编码
 * @property string		$productBatch		产品批次
 *
 */

 class tbWarehouseLock extends CActiveRecord {

    /**
	* 分配锁定，sourceId对应为分配单ID，分配完成，锁定对应数量不可再被分配。
	* 可分配数量 = 库存量-锁写表已锁定的全部对应的数量
	*/
	 CONST TYPE_DISTRIBUTION = 1 ;

	/**
	* 分拣锁定，sourceId对应为分拣单ID，分拣完成，锁定分拣数量，并翻译对应仓库的分配单的锁定数量。
	* 可分拣数量 = 库存量-分拣锁定-调拔锁定-待发货锁定
	*
	*/
	 CONST TYPE_PACKING 	 = 2 ;

	/**
	* 调拔锁定，sourceId对应为调拔单ID，新建内部调拔单，锁定数量不可操作，调拔完成后释放。
	* 可调拔数量 = 库存量-全部锁定
	*/
	CONST TYPE_ALLOCATION	 = 3 ;

	/**
	* 待发货锁定（订单锁定），sourceId对应为订单ID，订单调拔完成后，产品处于待发状态，锁定数量
	*/
	CONST TYPE_ORDER		 = 4 ;

     public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{warehouse_lock}}";
	}

	public function rules() {
		return array(
			array('warehouseId,type,sourceId,orderId,num,singleNumber,productBatch','required'),
			array('warehouseId,positionId,sourceId,orderId', "numerical","integerOnly"=>true),
			array('type','in','range'=>array(1,2,3,4)),
			array('num', "numerical",'min'=>'0'),
			array('singleNumber,productBatch', "safe"),
		);
	}


	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'warehouseId' => '仓库编号',
			'positionId' => '仓位ID',
			'type' => '来源类型',
			'sourceId' => '来源单ID',
			'orderId' => '订单ID',
			'num' => '锁定数量',
			'singleNumber' => '产品编码',
			'productBatch' => '产品批次',
		);
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
	  * 通过单品编码查寻包含产品的仓库编号和全部锁定量
	  * @param string $singleNumber
	  * @param array $warehouseIds
	  * @return array
	  * @throws CDbException
	  */
	 public function findGroupWarehouse( $singleNumber, $warehouseIds,$extraOrderId = null ) {
		if( empty( $singleNumber ) || empty ( $warehouseIds ) ) return array();

		$c = new CDbCriteria;
		$c->select = 'warehouseId,SUM(num) AS `num`';
		$c->compare('singleNumber',$singleNumber);
		$c->compare('warehouseId',$warehouseIds);
		$c->group = 'warehouseId';

		if( !empty($extraOrderId) ){
			if( !is_array( $extraOrderId ) ){
				$extraOrderId = array( $extraOrderId );
			}
			$c->addNotInCondition( 't.orderId',$extraOrderId );//NOT IN
		}

		$model = $this->findAll( $c );

		$lockNum = array();
		if( is_array ( $model ) ){
			foreach ( $model as $val  ){
				$lockNum[] = $val->getAttributes( array('warehouseId','num') );
			}
		}

		return $lockNum;

		/* $sql = "SELECT warehouseId,SUM(num) AS `num` FROM {$this->tableName()} WHERE singleNumber=:singleNumber and warehouseId in( $warehouseIds ) group by warehouseId";
		$cmd = $this->getDbConnection()->createCommand( $sql );
		$cmd->bindValue(':singleNumber',$singleNumber,PDO::PARAM_STR);
		$result = $cmd->queryAll();
		return $result; */
	 }

	 /**
	  * 通过单品编码查寻包含产品的仓库编号和全部锁定量
	  * @param string $singleNumber
	  * @param integer $warehouseId
	  * @param array $batchs
	  * @return array
	  * @throws CDbException
	  */
	 public function findGroupBatch( $singleNumber, $warehouseId,$batchs ,$extraOrderId = null ){
		if( empty( $singleNumber ) || empty ( $warehouseId ) || empty ( $batchs ) ) return array();

		$c = new CDbCriteria;
		$c->select = 'productBatch,SUM(num) AS `num`';
		$c->compare('singleNumber',$singleNumber);
		$c->compare('warehouseId',$warehouseId);
		$c->compare('productBatch',$batchs);
		$c->group = 'productBatch';

		if( !empty($extraOrderId) ){
			if( !is_array( $extraOrderId ) ){
				$extraOrderId = array( $extraOrderId );
			}
			$c->addNotInCondition( 't.orderId',$extraOrderId );//NOT IN
		}

		$model = $this->findAll( $c );

		$lockNum = array();
		if( is_array ( $model ) ){
			foreach ( $model as $val  ){
				$lockNum[$val->productBatch] = $val->num;
			}
		}
		return $lockNum;
	 }


	  /**
	  * 通过单品编码和仓库ID查寻包含产品的分区和对应的锁定量
	  * @param string $singleNumber
	  * @param integer $warehouseId
	  * @return array
	  * @throws CDbException
	  */
	 public function findGroupArea( $singleNumber, $warehouseId,$extraOrderId = null ){
		if( empty( $singleNumber ) || empty ( $warehouseId ) ) return array();

		$c = new CDbCriteria;
		$c->select = 'SUM(t.num) AS `num`,w.parentId as positionId';
		$c->compare('t.singleNumber',$singleNumber);
		$c->compare('t.warehouseId',$warehouseId);
		$c->addCondition(" t.positionId >0 ");

		$tPosition = tbWarehousePosition::model()->tableName();
		$c->join = "left join $tPosition  w on w.positionId = t.positionId";
		$c->group = 'w.parentId';

		if( !empty($extraOrderId) ){
			if( !is_array( $extraOrderId ) ){
				$extraOrderId = array( $extraOrderId );
			}
			$c->addNotInCondition( 't.orderId',$extraOrderId );//NOT IN
		}

		$model = $this->findAll( $c );
		$lockNum = array();
		if( is_array ( $model ) ){
			foreach ( $model as $val  ){
				$lockNum[$val->positionId] = $val->num;
			}
		}
		return $lockNum;
	 }

	 /**
	  * 通过单品编码和仓库区域ID查寻包含产品的仓位和对应的锁定量
	  * @param string $singleNumber
	  * @param integer $areaId
	  * @param array $batchs
	  * @return array
	  * @throws CDbException
	  */
	 public function findGroupPositionOfArea( $singleNumber, $areaId,$extraOrderId = null ){
		if( empty( $singleNumber ) || empty ( $areaId ) ) return array();

		$c = new CDbCriteria;
		$c->select = 'SUM(t.num) AS `num`,t.positionId';
		$c->compare('t.singleNumber',$singleNumber);
		$c->addCondition(" t.positionId >0 ");

		$tPosition = tbWarehousePosition::model()->tableName();
		$c->join = "join $tPosition  w on ( w.positionId = t.positionId and w.parentId = $areaId )";
		$c->group = 't.positionId';

		if( !empty( $extraOrderId ) ){
			if( !is_array( $extraOrderId ) ){
				$extraOrderId = array( $extraOrderId );
			}
			$c->addNotInCondition( 't.orderId',$extraOrderId );//NOT IN
		}

		$model = $this->findAll( $c );
		$lockNum = array();
		if( is_array ( $model ) ){
			foreach ( $model as $val  ){
				$lockNum[$val->positionId] = $val->num;
			}
		}
		return $lockNum;
	 }

	 /**
	  * 通过单品编码和仓位ID查寻包含产品的批次和对应的锁定量
	  * @param string $singleNumber
	  * @param integer $positionId
	  * @param array $batchs
	  * @return array
	  * @throws CDbException
	  */
	 public function findGroupBatchOfPosition( $singleNumber, $positionId ){
		if( empty( $singleNumber ) || empty ( $positionId ) ) return array();

		$c = new CDbCriteria;
		$c->select = 'SUM(t.num) AS `num`,t.productBatch';
		$c->compare('t.singleNumber',$singleNumber);
		$c->compare('t.positionId',$positionId);
		$c->group = 't.productBatch';

		$model = $this->findAll( $c );
		$lockNum = array();
		if( is_array ( $model ) ){
			foreach ( $model as $val  ){
				$lockNum[$val->productBatch] = $val->num;
			}
		}
		return $lockNum;
	 }
}
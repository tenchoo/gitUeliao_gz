
<?php
/**
 * 仓库产品信息
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$id
 * @property integer	$warehouseId		仓库ID
 * @property integer	$positionId			仓位ID
 * @property numerical	$num				当前库存数量
 * @property integer	$productId			产品ID
 * @property integer	$isGlassy			是否呆滞产品
 * @property timestamp	$createTime			新建时间
 * @property timestamp	$updateTime			更新时间
 * @property string		$singleNumber		单品编码
 * @property string		$productBatch		产品批次
 *
 */

 class tbWarehouseProduct extends CActiveRecord {

	//默认产品批次
	CONST DEATULE_BATCH = 'B001';

    public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{warehouse_product}}";
	}

	public function primaryKey() {
        return 'id';
    }

	public function relations(){
		return array(
			'warehouse'=>array(self::BELONGS_TO,'tbWarehouseInfo','warehouseId'),
			'product'=>array(self::BELONGS_TO,'tbProduct','productId'),
		);
	}

	public function rules() {
		return array(
			array('warehouseId,positionId,productId,num,singleNumber,productBatch','required'),
			array('warehouseId,positionId,productId', "numerical","integerOnly"=>true),
			array('num', "CFloatValidator","message"=>Yii::t('warehouse','invalid quantity vlaue')),
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
			'productId' => '产品编号',
			'num' => '当前库存数量',
			'singleNumber' => '产品编码',
			'productBatch' => '产品批次',
		);
	}

	/**
	* 取得仓位名称
	*/
	public function getPositionName(){
		return $this->positionName( $this->positionId );
	}


	public function positionName( $positionId ) {
    	$result = tbWarehousePosition::model()->findByPk( $positionId );
    	if( $result ) {
    		return $result->title;
    	}
    	return;
    }

	public function warehouseName( $warehouseId ) {
    	$result = tbWarehouseInfo::model()->findByPk( $warehouseId );
    	if( $result ) {
    		return $result->title;
    	}
    	return;
    }

	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
		}
		$this->updateTime = new CDbExpression('NOW()');
		return parent::beforeSave();
	}

	/**
	* 清除某仓库全部信息，只在全仓清空盘点时使用，清除前需备份
	* @param integer $warehouseId  仓库ID
	* @param integer $stocktakingId 盘点单ID
	*/
	public function clearStorge( $warehouseId, $stocktakingId){
		if( empty($warehouseId) || !is_numeric($warehouseId) ||empty($stocktakingId) || !is_numeric($stocktakingId) ){
			return false;
		}
		//备份数据
		$sql = "INSERT INTO {{warehouse_oldrecords}}(`stocktakingId`, `id`, `warehouseId`, `positionId`, `productId`, `num`, `createTime`, `updateTime`, `singleNumber`, `productBatch`) SELECT $stocktakingId,`id`, `warehouseId`, `positionId`, `productId`, `num`, `createTime`, `updateTime`, `singleNumber`, `productBatch` FROM {$this->tableName()} where `warehouseId`='$warehouseId' ";
		$command = Yii::app()->db->createCommand($sql);
		$command->execute();

		$flag =  $this->deleteAllByAttributes(array(
								'warehouseId'=>$warehouseId,
							));

		if( $flag ){
			$flag = tbWarehouseCount::model()->deleteAllByAttributes(array(
								'warehouseId'=>$warehouseId,
							));
		}

		return $flag;
	}

	/**
	* 按产品ID备份当前库存某产品的全部信息，只在盘点时使用
	* @param integer $warehouseId  仓库ID
	* @param string $singleNumber	单品编码
	* @param integer $stocktakingId 盘点单ID
	*/
	public function backupByProductId( $warehouseId,$productId, $stocktakingId){
		if( empty($warehouseId) || !is_numeric($warehouseId) || empty($productId) ||empty($stocktakingId) || !is_numeric($stocktakingId) ){
			return false;
		}
		//备份数据
		$sql = "INSERT INTO {{warehouse_oldrecords}}(`stocktakingId`, `id`, `warehouseId`, `positionId`, `productId`, `num`, `createTime`, `updateTime`, `singleNumber`, `productBatch`) SELECT $stocktakingId,`id`, `warehouseId`, `positionId`, `productId`, `num`, `createTime`, `updateTime`, `singleNumber`, `productBatch` FROM {$this->tableName()} where `warehouseId`='$warehouseId' and  `productId` = '$productId'";
		$command = Yii::app()->db->createCommand($sql);
		return  $command->execute();
	}

	 /**
	  * 增加库存
	  * @param CEvent $event
	  */
	 public static function event_push( CEvent $event ) {
		 $sender      = $event->sender;
		 if(!($sender instanceof tbWarehouseWarrantDetail)) {
		 	Yii::log('sender not is tbWarehouseWarrantDetail',CLogger::LEVEL_ERROR, 'tbWarehouseProduct::event_push');
		 	return false;
		 }

         $control = Yii::app()->getController();
		 $storageInfo = tbWarehousePosition::model()->findByPk($sender->positionId);

		 if( is_null($storageInfo) ) {
		 	 Yii::log('Not found position',CLogger::LEVEL_ERROR, 'tbWarehouseProduct::event_push');
			 $control->setError( array(array('Not found position')) );
			 return false;
		 }

		 $row = tbWarehouseProduct::model()->find( "positionId=:pid and singleNumber=:serial and productBatch=:batch", array(':pid'=>$sender->positionId,':serial'=>$sender->singleNumber,':batch'=>$sender->batch) );

		 if( is_null($row) ) {
             $sock = tbProductStock::model()->find('singleNumber=:serial',array(':serial'=>$sender->singleNumber));

             if( !$sock ) {
                 $control->setError( array(Yii::t('order','Not found record by:{serial}',array('{serial}'=>$sender->singleNumber))) );
                 return false;
             }

			 $row               = new tbWarehouseProduct();
			 $row->warehouseId  = $storageInfo->warehouseId;
			 $row->positionId   = $sender->positionId;
			 $row->singleNumber = $sender->singleNumber;
             $row->productBatch = $sender->batch;
             $row->productId    = $sock->productId;
		 }
         else {
             $row->updateTime = date('Y-m-d H:i:s');
         }

		 $row->num = bcadd( $row->num,$sender->num ,2 );
		 if( !$row->save() ) {
		 	Yii::log(Yii::t('warehouse',"Not update warehouse product quantity"), CLogger::LEVEL_ERROR, __CLASS__.'::'.__FUNCTION__);
             return false;
         }

		 //更改统计数量--当触发器用
		tbWarehouseCount::numAdd( $sender->num,$row->warehouseId, $row->productId,$row->singleNumber );

        return true;
	 }


	 /**
	  * 减少库存--出库时调用
	  * @param CEvent $event
	  */
	 public static function event_outbound( CEvent $event ) {
		 $sender      = $event->sender;
		 $control = Yii::app()->getController();

		 $row = self::model()->find( "positionId=:pid and singleNumber=:serial and productBatch=:batch", array(':pid'=>$sender->positionId,':serial'=>$sender->singleNumber,':batch'=>$sender->productBatch) );

		 if( is_null($row) ) {
			$control->setError( array('product',array('Not found product')) );
            return false;
		 }

		 $row->num = bcsub( $row->num,$sender->num ,2 );
		 if( !$row->save() ) {
            $control->setError( $row->getErrors() );
            return false;
         }

		  //更改统计数量--当触发器用
		 tbWarehouseCount::numSub( $sender->num,$row->warehouseId, $row->productId,$row->singleNumber );

         return true;
	 }

	 /**
	  * 产品总库存量
	  * @param $singleNumber
	  * @return int|mixed
	  * @throws CDbException
	  */
	 public function singleCount( $singleNumber ) {

		 $sql = "select sum(num) from {$this->tableName()} where singleNumber=:serial";
		 $cmd = $this->getDbConnection()->createCommand( $sql );
		 $cmd->bindValue(':serial',$singleNumber,PDO::PARAM_STR);
		 $result = $cmd->queryScalar();
		 return floatval($result);
	 }

	 /**
	  * 产品可供销售的总库存量--可销售量不计算样品仓和损耗仓的库存
	  * @param $singleNumber
	  * @return int|mixed
	  * @throws CDbException
	  */
	 public function singleSaleCount( $singleNumber ) {
		 $w = tbWarehouseInfo::model()->tableName();
		 $sql = "select sum(t.num) from {$this->tableName()} t
				where exists ( select null  from {$w} w  where  w.`warehouseId` = t.warehouseId and w.type=1 ) and t.singleNumber=:serial";
		 $cmd = $this->getDbConnection()->createCommand( $sql );
		 $cmd->bindValue(':serial',$singleNumber,PDO::PARAM_STR);
		 $result = $cmd->queryScalar();
		 return floatval($result);
	 }

	/**
	 * 查找产品的总库存量,后台产品列表调用。
	 * @param integer $productId
	 */
	 public function productCount( $productId ) {
		 $sql = "select sum(num) as total from {$this->tableName()} where productId=:id";
		 $cmd = $this->getDbConnection()->createCommand( $sql );
		 $cmd->bindValue(':id',$productId,PDO::PARAM_STR);
		 $result = $cmd->queryColumn();
		 return floatval($result[0]);
	 }

	 /**
	  * 通过单品编码查寻包含产品的仓库编号和库存量
	  * @param $serial
	  * @return array
	  * @throws CDbException
	  */
	 public function findProductInStroage( $serial ) {
		$warehouseTable = tbWarehouseInfo::model()->tableName();
		$sql = "SELECT A.warehouseId,B.title as `storageTitle`,positionId,productid,SUM(num) AS `total` FROM {$this->tableName()} A left join {$warehouseTable} B using(warehouseId) WHERE A.singleNumber=:serial group by A.warehouseId having total > 0";
		$cmd = $this->getDbConnection()->createCommand( $sql );
		$cmd->bindValue(':serial',$serial,PDO::PARAM_STR);
		$result = $cmd->queryAll();
		if( is_array($result) ){
			foreach ( $result as &$val ){
				$val['total'] = $val['total'];
			}
		}
		return $result;
	 }


	 /**
	  * 查询包含产品仓库信息
	  * @param string $serial
	  * @param null|integer $house
	  * @result array
	  */
	 public function strageContainProduct( $serial, $house=null ) {
		if( is_null($house) ) {
			$sql = "SELECT P.warehouseId,title AS houseTitle,positionId,singleNumber,SUM(num) AS num FROM `db_warehouse` S RIGHT JOIN {$this->tableName()} P USING(warehouseId) WHERE singleNumber=:serial GROUP BY warehouseId  having num > 0";
		}
		else {
			$sql = "SELECT P.warehouseId,title AS houseTitle,positionId,singleNumber,SUM(num) AS num FROM `db_warehouse` S RIGHT JOIN {$this->tableName()} P USING(warehouseId) WHERE warehouseId=:house AND singleNumber=:serial GROUP BY warehouseId  having num > 0";
		}

		$cmd = $this->getDbConnection()->createCommand( $sql );
		$cmd->bindValue(':serial',$serial,PDO::PARAM_STR);

		if( !is_null($house) ) {
			$cmd->bindValue(':house',$house,PDO::PARAM_INT);
		}

		$result = $cmd->queryAll();
		return $result;
	 }

	 /**
	  * 查询包含产品分区信息
	  * @param string $serial
	  * @param null|integer $house
	  * @return array
	  * @throws CDbException
	  */
	 public function areaContainProduct( $serial, $house=null ) {
		if( is_null($house) ) {
			$tWarehouse = tbWarehouseInfo::model()->tableName();
			$sql = "SELECT P.warehouseId,S.parentId as areaId,title AS positionTitle,positionId,singleNumber,SUM(num) AS num FROM {$tWarehouse} S RIGHT JOIN {$this->tableName()} P ON S.warehouseId=P.positionId WHERE singleNumber=:serial GROUP BY S.parentId  having num > 0";
		}
		else {
			$tPosition = tbWarehousePosition::model()->tableName();
			$sql = "SELECT P.warehouseId,S.parentId as areaId,title AS positionTitle,P.positionId,P.singleNumber,SUM(P.num) AS num FROM {$tPosition} S RIGHT JOIN {$this->tableName()} P ON S.positionId=P.positionId WHERE singleNumber=:serial AND P.warehouseId=:house GROUP BY S.parentId   having num > 0";
		}
		$cmd = $this->getDbConnection()->createCommand( $sql );
		$cmd->bindValue(':serial',$serial,PDO::PARAM_STR);

		if( !is_null($house) ) {
			$cmd->bindValue(':house',$house,PDO::PARAM_INT);
		}

		$result = $cmd->queryAll();
		return $result;
	 }





	  /**
	  * 查询包含产品分区信息,指定仓库ID
	  * @param string $singleNumber
	  * @param integer $warehouseId
	  * @return array
	  * @throws CDbException
	  * @time 2016-05-17
	  */
	 public function areaHasProduct( $singleNumber, $warehouseId ) {
		if( empty($singleNumber) || empty($warehouseId) ) return array();

		$c = new CDbCriteria();
		$c->select = 'p.parentId as positionId';//代表了要查询的字段，默认select='*';
		$c->compare('t.warehouseId',$warehouseId);
		$c->compare('t.singleNumber',$singleNumber);
		$c->distinct = true;//是否唯一查询
		$position = new tbWarehousePosition();
		$c->join = "left join {$position->tableName()} p on t.positionId = p.positionId "; //连接表

		$model = $this->findAll( $c );
		$model = array_map( function($i){ return $i->positionId; } ,$model );

		$zoning = $position->findAllByPk( $model );
		$result = array_map( function($i){ return $i->getAttributes( array('positionId','title')); } ,$zoning );

		return $result;
	 }

	 /**
	  * 查询包含产品分区信息
	  * @param string $serial
	  * @param null|integer $house
	  * @return array
	  * @throws CDbException
	  */
	 public function positionContainProduct( $serial, $area ) {
		$tPosition = tbWarehousePosition::model()->tableName();
		$sql = "SELECT P.warehouseId,S.parentId as areaId,title AS positionTitle,P.positionId,P.singleNumber,SUM(num) AS num FROM {$tPosition} S RIGHT JOIN {$this->tableName()} P ON S.positionId=P.positionId WHERE singleNumber=:serial AND S.parentId=:area GROUP BY S.positionId   having num > 0";
		$cmd = $this->getDbConnection()->createCommand( $sql );
		$cmd->bindValue(':serial',$serial,PDO::PARAM_STR);
		$cmd->bindValue(':area',$area,PDO::PARAM_INT);

		$result = $cmd->queryAll();
		return $result;
	 }

	 /**
	  * 同一仓库内同型号的产品有多少种批次的列表
	  * @param $houserId integer 仓库编号
	  * @param $serial string 产品编码
	  * @return array
	  * @throws CDbException
	  */
	 public function findAllBatch( $houserId, $serial ) {
		$sql = "SELECT id,productBatch,SUM(num) AS num FROM {$this->tableName()} WHERE singleNumber=:serial AND warehouseId=:hid GROUP BY productBatch  having num > 0";
	 	$cmd = $this->getDbConnection()->createCommand( $sql );
	 	$cmd->bindValue(':hid', $houserId, PDO::PARAM_STR);
	 	$cmd->bindValue(':serial', $serial, PDO::PARAM_STR);
	 	$result = $cmd->queryAll();
		foreach($result as & $item) {
			$item['num'] = Order::quantityFormat($item['num']);
		}
		return $result;
	 }

	 public function findAllPosition( $houseId, $serial ) {
	 	$sql = "select positionId,sum(num) as sum from {$this->tableName()} where warehouseId=:hid and singleNumber=:serial group by positionId   having sum > 0";
	 	$cmd = $this->getDbConnection()->createCommand( $sql );
	 	$cmd->bindValue(':hid', $houseId, PDO::PARAM_STR);
	 	$cmd->bindValue(':serial', $serial, PDO::PARAM_STR);
	 	$result = $cmd->queryAll();
		if( is_array($result) ){
			foreach ( $result as &$val ){
				$val['sum'] = $val['sum'];
			}
		}
		return $result;
	 }

	 public function findAllByPosition2( $positionId, $serial ) {
		 $sql = "select id,productBatch,positionId,sum(num) as total from {$this->tableName()} where positionId=:pid and singleNumber=:serial group by productBatch having total > 0";
		 $cmd = $this->getDbConnection()->createCommand($sql);
		 $cmd->bindValue(':pid',$positionId,PDO::PARAM_INT);
		 $cmd->bindValue(':serial',$serial,PDO::PARAM_STR);
		 $result = $cmd->queryAll();

		if( is_array($result) ){
			foreach ( $result as &$val ){
				$val['total'] = $val['total'];
			}
		}
		return $result;
	 }

	/**
	* 查找当前可用库存数量
	* 当前库存数量-对应的锁定库存数量
	* @param array $condition 查找的条件
	* @param integer/array $extraOrderId 需要排除在外的oriderId
	* @return float
	*/
	public function findValidNum( array $condition ,$extraOrderId = null ){
		$c = new CDbCriteria;
		$c->select = 'SUM(t.num) AS `num`';

		foreach ( $condition as $key=>$val ){
			$c->compare($key,$val);
		}

		$row = $this->find ( $c );
		if( !$row || !($row->num) ) return 0;

		$num = $row->num;

		//查找对应的锁写量
		if( !empty($extraOrderId) ){
			if( !is_array( $extraOrderId ) ){
				$extraOrderId = array( $extraOrderId );
			}
			$c->addNotInCondition( 'orderId',$extraOrderId );//NOT IN
		}

		$lock = tbWarehouseLock::model()->find( $c );
		if( $lock && $lock->num > 0 ){
			$num = bcsub( $num,$lock->num );
		}

		return $num;
	}
}
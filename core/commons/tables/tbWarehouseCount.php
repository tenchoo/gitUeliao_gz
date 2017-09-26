<?php
/**
 * 仓库产品统计数据
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$id
 * @property integer	$warehouseId	仓库ID
 * @property integer	$productId		产品ID
 * @property integer	$num			当前库存数量
 * @property string		$singleNumber	单品编码
 *
 */

 class tbWarehouseCount extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{warehouse_count}}";
	}

	/**
	* 增加库存
	*/
	public static function numAdd( $num,$warehouseId,$productId,$singleNumber ){
		if( ! ( is_numeric( $num ) && $num >0 ) ) return false;
		$model = tbWarehouseCount::model()->find( "warehouseId=:warehouseId and productId=:productId and singleNumber=:serial ", array(':warehouseId'=>$warehouseId,':productId'=>$productId,':serial'=>$singleNumber) );

		if( !$model ){
			$model  = new tbWarehouseCount();
			$model->warehouseId  = $warehouseId;
			$model->productId    = $productId;
			$model->singleNumber = $singleNumber;
			$model->num			 = 0;
		}

		$model->num = bcadd( $model->num,$num ,2 );
		return $model->save();
	}

	/**
	* 减少库存
	*/
	public static function numSub( $num,$warehouseId,$productId,$singleNumber ){
		if( ! ( is_numeric( $num ) && $num >0 ) ) return false;
		$model = tbWarehouseCount::model()->find( "warehouseId=:warehouseId and productId=:productId and singleNumber=:serial ", array(':warehouseId'=>$warehouseId,':productId'=>$productId,':serial'=>$singleNumber) );

		if( !$model ) return false;

		$model->num = bcsub( $model->num,$num ,2 );
		if( $model->num <= 0 ){
			return $model->delete();
		}else{
			return $model->save();
		}
	}
}
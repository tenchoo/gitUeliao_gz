<?php
/**
 * 发布产品,产品默认分拣区域
 * @version 0.1
 * @package CFormModel
 */
class ProductWarehouseForm extends CFormModel {

	public $productId;

	/**
	* 设置产品默认分拣区域
	*/
	public function save( $data ){
		if( empty( $this->productId ) ){
			return false;
		}

		$model = new tbProductPacking();
		$model->productId = $this->productId;

		//使用事务处理，以确保这组数据全部成功插入
		$transaction = Yii::app()->db->beginTransaction();
		try {
			//删除原有的，再重新录入
			$model->deleteAllByAttributes( array('productId'=>$this->productId) );
			if( !empty( $data ) && is_array( $data ) ){
				foreach ( $data as $key=>$val ){
					$_model = clone $model;
					$_model->warehouseId = $key;
					$_model->positionId = $val;
					if( !$_model->save() ){
						$transaction->rollback();
						$this->addErrors( $_model->errors );
						return false;
					}
				}
			}

			$transaction->commit();
			return true;
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			throw new CHttpException(503,$e);
			return false;
		}
	}


  public function getAll( $productId  ){
	if( empty( $productId  ) ) return array();

	$models = tbProductPacking::model()->findAllByAttributes( array('productId'=>$productId) );
	return array_map( function ( $i ){ return $i->attributes;},$models );
  }
}
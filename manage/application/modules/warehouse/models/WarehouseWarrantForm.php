<?php
/**
 * 仓库入库单
 * @author liang
 * @version 0.1
 * @package CFormModel
 */
class WarehouseWarrantForm extends CFormModel {

	public $products;

	public $warehouseId;

	public function rules()	{
		return array(
			array('products','required'),
			array('products,warehouseId','safe'),
		);
	}

	public function attributeLabels() {
		return array(
			'products'=>'入库单产品明细',
		);
	}

	/**
	* 新增入库单
	*/
	public function add(){
		if( !$this->validate() ) {
			return false ;
		}

		$detail = new tbWarehouseWarrantDetail();
		$detail->warrantId = 0;
		foreach ( $this->products as $val){
			$_detail = clone $detail ;
			$_detail->attributes = $val;
			if( !$_detail->validate() ) {
				$this->addErrors( $_detail->getErrors() );
				return false ;
			}
		}

		$transaction = Yii::app()->db->beginTransaction();
		try {
			$model = new tbWarehouseWarrant();
			$model->operator = Yii::app()->user->username;
			$model->warehouseId = $this->warehouseId;

			$model->factoryNumber = '';
			$model->contactName = '';
			$model->phone = '';
			$model->factoryName = '';
			$model->address = '';
			$model->remark = '';

			if(!$model->save()){
				$this->addErrors( $model->getErrors() );
				return false;
			}

			$detail->warrantId = $model->warrantId;
			foreach ( $this->products as $val){
				$_detail = clone $detail ;
				$_detail->attributes = $val;

				$_detail->orderId = '';
				$_detail->singleNumber = '';
				$_detail->color = '';
				$_detail->corpProductNumber = '';

				if( !$_detail->save() ) {
					$this->addErrors( $_detail->getErrors() );
					return false ;
				}

				//入库，增加仓库的数量
				$this->updateNum( $_detail->warehouseId,$_detail->stockId,$_detail->num );
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
	* 编辑入库单
	*/
	public function edit( $model ){
		if( !$this->validate()  || empty( $model ) ) {
			return false ;
		}

		$transaction = Yii::app()->db->beginTransaction();
		try {
			$products = $this->products ;
			 foreach ( $model->detail as $val ){
				if(!isset( $products[$val->id] )){
					$val->num = 0;
					// 删除明细，需要减去相应库存量
					// $this->updateNum( $model->warehouseId,$val->singleNember,-($val->num) );
				}else{
					$val->storageTime = $products[$val->id]['storageTime'];
					if( $val->num != $products[$val->id]['num'] ){
						$val->num = $products[$val->id]['num'];
						// 更改相应库存量
						// $n = $products[$val->id]['num'] - $val->num;
						// $this->updateNum( $model->warehouseId,$val->singleNember,$n );
					}
				}
				if( !$val->save() ) {
					$this->addErrors( $val->getErrors() );
					return false ;
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

	/**
	* 查找入库单列表
	* @param array $condition 搜索条件
	*/
	public function search( $condition= array(),$pageSize = 2 ){
		$result['list'] = array();
		$criteria = new CDbCriteria;

		$criteria->order = 't.createTime desc';
		if(!empty($condition['id'])){
			$criteria->compare('t.warrantId',$condition['id']);
		}
		if(!empty($condition['factoryNumber'])){
			$criteria->compare('t.factoryNumber',$condition['id']);
		}
		if(!empty($condition['singleNumber'])){
			$criteria->alias = 'd';
			$criteria->select = 'd.warrantId';
			$criteria->distinct = true;
			$criteria->join = 'left join {{warehouse_warrant}} t on t.warrantId = d.warrantId ';
			$criteria->compare('d.singleNumber',$condition['singleNumber']);
			$model = new CActiveDataProvider('tbWarehouseWarrantDetail', array(
				'criteria'=>$criteria,
				'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
			));
			$ids = $model->getData();
			if(!empty($ids)){
				foreach ( $ids as &$val){
					$val = $val->warrantId;
				}
				$c = new CDbCriteria;
				$c->compare('t.warrantId',$ids);
				$result['list'] = tbWarehouseWarrant::model()->findAll( $c );
			}
		}else{
			$model = new CActiveDataProvider('tbWarehouseWarrant', array(
				'criteria'=>$criteria,
				'pagination'=>array('pageSize'=>$pageSize,'pageVar'=>'page'),
			));
			$result['list'] = $model->getData();
		}
		$result['pages'] = $model->getPagination();
		return $result;
	}
}

<?php
/**
 * 发布产品,采购，安全库存
 * @version 0.1
 * @package CFormModel
 */
class ProductSetForm extends CFormModel {

	public $productId,$supplierId,$price,$supplierSerialnumber,$safetystock,$supplier;

	public function rules() {
		return array(
			array('productId,supplierId,price,supplierSerialnumber,supplier','required'),
			array('productId','numerical','integerOnly'=>true),
			array('supplierId','numerical','integerOnly'=>true,'message'=>Yii::t('base','Manufacturers have to search through the search click')),			
			array('price','numerical'),
			array('supplierSerialnumber','safe'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'productId' => '产品ID',
			'supplierId' => '生产厂家',
			'supplier' => '生产厂家',
			'price' => '采购价格',
			'supplierSerialnumber' => '厂家产品编号',
		);
	}

	/**
	* 保存采购信息
	* @param array $arrData
	* @param CModel $model
	*/
	public function saveProcurement( $arrData ,CModel $model){
		$model->attributes = $arrData;
		if( $model->save() ){
			return true;
		}else{
			return 	$model->getErrors();
		}
	}


	public function save() {
		$this->price = str_replace(',','',$this->price );
		
		if( !$this->validate() ) {
			return false ;
		}
		$model = tbProcurement::model()->findByPk( $this->productId );
		if( !$model ){
			$model = new tbProcurement();
			$model->productId = $this->productId ;
		}
		$model->attributes = $this->attributes;
		if($model->save()){
			return true;
		}else{
			$this->addErrors( $model->getErrors() );
			return false;
		}


	}

	/**
	* 取得采购信息
	*/
	public function getInfo( $productId ){
		if( empty( $productId ) ){
			return ;
		}

		$model = tbProcurement::model()->findByPk( $productId );
		if( $model ){
			$this->attributes = $model->attributes;
			if( $this->supplierId ){
				$supplier = tbSupplier::model()->findByPk( $this->supplierId  );
				if( $supplier ) $this->supplier = $supplier->shortname;
			}
		}
	}

	/**
	* 保存安全库存量
	* @paray array $data 要保存的信息
	*/
	public function updateSafetystock( $data ){
		$model = new tbProductStock();
		foreach ( $data as $key=>$val ){
			if( !is_numeric($key) || !is_numeric($val) || (int)$val != $val){
				$this->addError('safetystock',Yii::t('base','Safety stock must be an integer'));
				return false;
			}
			$model->updateByPk($key,array('safetyStock'=>$val));
		}
		return true;
	}
	
	
}
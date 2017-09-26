<?php
/**
 * 产品采购信息
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$productId					产品ID
 * @property integer	$supplierId					供应商,厂家 ID
 * @property numerical	$price						采购价格
 * @property string		$supplierSerialnumber		供应商对应的产品编号
 *
 */

 class tbProcurement extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{procurement}}";
	}

	public function rules() {
		return array(
			array('productId,supplierId,price,supplierSerialnumber','required'),
			array('productId,supplierId','numerical','integerOnly'=>true),
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
			'supplierId' => '供应商,厂家 ID',
			'price' => '采购价格',
			'supplierSerialnumber' => '供应商对应的产品编号',
		);
	}
	
	/**
	 * 通过产品单品编码搜索供应商信息
	 * @param string $serial
	 */
	public function findByProductSerial( $serial ) {
		$tProductStock = tbProductStock::model()->tableName();
		$tProcurement  = tbProcurement::model()->tableName();
		$tSupplier     = tbSupplier::model()->tableName();
		$sql = "select A.productId,C.supplierId,B.supplierSerialnumber,C.shortname,C.contact,C.phone from {$tProductStock} A 
		left join {$tProcurement} B using(productId) left join {$tSupplier} C on B.supplierId=C.supplierId 
		where A.singleNumber=:serial";
		
		$cmd = Yii::app()->getDb()->createCommand( $sql );
		$cmd->bindValue( ':serial', $serial, PDO::PARAM_STR );
		$result = $cmd->queryRow();
		return $result;
	}
	
	/**
	 * 搜索供应商名称
	 * @param string $shortname
	 */
	public function findByShortname( $shortname ) {
		$criteria = new CDbCriteria();
		$criteria->addSearchCondition('shortname', $shortname);
		$result = $this->find( $criteria );
		if( $result ) {
			return $result->getAttributes();
		}
		return;
	}

	/**
	 * 搜索供应商编号
	 * @param string $serial
	 */
	public function findBySerial( $serial ) {
		$tProcurement  = tbProcurement::model()->tableName();
		$tSupplier     = tbSupplier::model()->tableName();
		$sql = "select B.supplierId,B.shortname,B.contact,B.phone from {$tProcurement} A left join {$tSupplier} B on A.supplierId=B.supplierId 
		where A.serialNumber=:serial";
		
		$cmd = Yii::app()->getDb()->createCommand( $sql );
		$cmd->bindValue( ':serial', $serial, PDO::PARAM_STR );
		$result = $cmd->queryRow();
		return $result;
	}
}
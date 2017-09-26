<?php
/**
 * 产品默认分拣区域
 *
 * @property int    $productId		产品ID
 * @property int    $warehouseId	仓库ID
 * @property int 	$positionId		默认分拣区域ID
 */

class tbProductPacking extends CActiveRecord {


  public static function model($className=__CLASS__) {
    return parent::model( $className );
  }

  public function tableName() {
    return '{{product_packing}}';
  }

  public function rules() {
    return array(
      array('productId,warehouseId,positionId', 'required'),
      array('productId,warehouseId,positionId', "numerical","integerOnly"=>true,'min'=>1),
    );
  }

  public function attributeLabels() {
    return array(
      'warehouseId' => '仓库ID',
      'positionId' => '默认分拣区域ID',
      'productId'=>'产品ID',
    );
  }
}
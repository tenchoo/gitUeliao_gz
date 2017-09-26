<?php
/**
 * 产品基本信息
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$unitId			单位ID
 * @property string		$unitName		单位名称
 *
 */

 class tbUnit extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{unit}}";
	}

	public function rules() {
		return array(
			array('unitName','required'),
			array('unitName', 'length', 'max'=>4, 'min'=>1),
			array('unitName','safe'),
			array('unitName','unique'),

		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'unitName' => '单位名称',
		);
	}

	/**
	* 取得全部单位
	*/
	public function getUnits(){
		//$result = Yii::app()->session['units'];
		$result = null;
		if(empty($result )){
			$model = $this->findAll();
			$result = array();
			foreach ( $model  as $val ){
				$result[$val->unitId] = $val->unitName;
			}
			Yii::app()->session['units'] = $result;
		}
		return $result;
	}

	/**
	* 根据ID取得某个单位名称
	* @param integer $unitId
	* @override
	*/
	public static function getUnitName( $unitId ){
		$model = self::model()->findByPk( $unitId );
		if( $model ){
			return $model->unitName;
		}
		return null;
	}

	public static function getUnitByProduct($productId) {
		$product = tbProduct::model()->findByPk($productId);
		if(!$product) {
			return "";
		}
		return self::getUnitName($product->unitId);
	}

}
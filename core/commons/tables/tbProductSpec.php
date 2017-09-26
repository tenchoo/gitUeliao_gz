<?php
/**
 * 产品规格数据
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$productId				产品ID
 * @property integer	$specId					规格ID
 * @property integer	$specvalueId			规格值ID
 * @property string		$specValue				实际提交规格值
 * @property string		$picture				规格图片
 *
 */

 class tbProductSpec extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{product_spec}}";
	}

	public function rules() {
		return array(
			array('productId,specId,specvalueId','required'),
			array('productId,specId,specvalueId','numerical','integerOnly'=>true),
			array('specValue,picture','safe'),

		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'productId' => '产品ID',
			'specId' => '规格ID',
			'specvalueId' => '规格值ID',
			'specValue' => '规格值',
			'picture'=>'规格图片',
		);
	}

	/**
	* 取得产品规格
	* @param integer $productId
	*/
	public static function getSpec ( $productId ){
		if( empty ( $productId )){
			return ;
		}

		$sql = "select s.specId,s.specvalueId,s.picture,sv.colorSeriesId,sv.title,sv.code,sv.serialNumber from {{product_spec}} s ,{{specvalue}} sv where s.productId ='$productId' and s.specvalueId = sv.specvalueId";
		$command = Yii::app()->db->createCommand($sql);
		$spec =  $command->queryAll();
		$result = array();
		foreach ( $spec as $val ){
			$k = $val['specvalueId'];
			$result[$k] = $val;
		}
		return $result;
	}


	/**
	* 根据产品ID取得产品当前规格数据
	*
	*/


}
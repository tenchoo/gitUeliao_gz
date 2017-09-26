<?php
/**
 * 产品查看记录
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$id
 * @property integer	$productId
 * @property integer	$memberId			客户ID
 * @property timestamp	$createTime
 *
 */

 class tbProductView extends CActiveRecord {

	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	public function tableName() {
		return "{{product_view}}";
	}

	public function rules() {
		return array(
			array('productId','required'),
			array('memberId,productId', "numerical","integerOnly"=>true),
		);
	}



	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
			if( !Yii::app()->user->getIsGuest() ) {
				$this->memberId = Yii::app()->user->id;
			}else{
				return false;
			}
		}
		return true;
	}

}
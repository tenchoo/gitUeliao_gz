<?php

/**
 * 产品语音推荐明细
 *
 * @property integer	$id
 * @property integer	$recommendId	推荐位ID
 * @property integer	$productId		产品ID
 * @property integer	$listOrder		排序值,从小到大排序
 * @property timestamp	$createTime
 * @version 0.1
 * @package CActiveRecord
 */

class tbRecommendVoiceProduct extends CActiveRecord {

	/**
	 * 构造数据库表模型单例方法
	 * @param system $className
	 * @return static
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{recommend_voice_product}}';
	}

	public function relations(){
		return array(
			'productInfo'=>array(self::BELONGS_TO,'tbProduct','productId','select'=>'productId,price,title,serialNumber,mainPic'),
		);
	}

	/**
	 * @return array 模型验证规则.
	 */
	public function rules()	{
		return array(
			array('recommendId,productId,listOrder','required'),
			array('recommendId,productId,listOrder', 'numerical','integerOnly'=>true),
			array('productId','checkExists'),

		);
	}

	public function attributeLabels(){
		return array(
			'recommendId'=>'推荐位ID',
			'productId'=>'产品ID',
			'listOrder'=>'排序值',

		);
	}

	/**
	* 检查是否存在，同一推荐位，产品编号不能重复,rules 规格
	*/
	public function checkExists( $attribute,$params ){
		if( !$this->hasErrors() && $this->isNewRecord ){
			$flag = $this->exists('recommendId = :recommendId and productId = :productId',array(':recommendId'=>$this->recommendId,':productId'=>$this->productId));
			if( $flag ){
				$this->addError($attribute,Yii::t('product','This product has been recommended'));
				return;
			}

			//检查产品是否存在并已经上架
			$exists = tbProduct::model()->exists('productId = :productId and state = :state',array(':state'=>0,':productId'=>$this->productId));
			if( !$exists ){
				$this->addError($attribute,Yii::t('product','The product does not exist or has been off the shelf'));
				return;
			}

			//检查此产品是否有语音文件
			$exists = tbProductSound::model()->exists('productId = :productId and isDel = :isDel',array(':isDel'=>0,':productId'=>$this->productId));
			if( !$exists ){
				$this->addError($attribute,Yii::t('product','This product does not have a voice file'));
				return;
			}


			//检查推荐位是否存在
			$position = tbRecommendVoice::model()->findByPk( $this->recommendId ,'t.state=0');
			if( !$position ){
				$this->addError($attribute,Yii::t('product','The Recommend position does not exist'));
				return;
			}

			//订算当前位置已推荐条数
			$count = $this->count( 'recommendId = '.$this->recommendId );


			if( $count >= $position->maxNum ){
				$this->addError($attribute,Yii::t('product','The number of recommendations has reached the upper limit'));
				return;
			}

		}
	}

	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
		}
		return true;
	}
}
?>
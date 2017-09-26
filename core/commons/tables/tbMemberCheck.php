<?php

/**
 * 客户审核记录表模型.
 *
 * @author liang
 * @version 0.1
 * @package CActiveRecord
 *
 * @property integer	$id
 * @property integer	$memberId		客户ID
 * @property integer	$opId			操作者ID
 * @property integer	$state			审核状态
 * @property timestamp	$createTime		审核时间
 * @property string		$reason			理由
 */
class tbMemberCheck extends CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return tbMemberCheck the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{member_check}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('memberId,state', 'required'),
			array('memberId,state', 'numerical', 'integerOnly'=>true),
			array('reason', 'safe'),
		);
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'memberId' => '客户ID ',
			'opId' => '操作者ID',
			'state' => '审核状态',
			'reason' => '理由',
		);
	}
	
	/**
	* 取得最近的一条审核记录
	* @param integer $memberId
	*/
	public function getOne( $memberId ){
		$model = $this->find(
			array(
				'condition'=>'memberId = :memberId',
				'params'=>array(':memberId'=>$memberId),
				'order'=>'createTime desc',
			)
		);
		if( $model ){
			return $model->reason;
		}
		
	}
	
	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = new CDbExpression('NOW()');
			$this->opId = Yii::app()->user->id ;
		}
		return true;
	}
}
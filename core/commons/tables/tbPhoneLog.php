<?php

/**
 * 邮件/短信表 "{{phonelog}}".
 *
 * @property integer $messageId
 * @property integer $memberId
 * @property string $title
 * @property string $content
 * @property integer $state
 * @property integer $createTime
 */
class tbPhoneLog extends CActiveRecord
{
	
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Message the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * @return string 数据库表名称
	 */
	public function tableName()
	{
		return '{{phone_log}}';
	}

	/**
	 * @return array 验证字段.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('memberId, state,type', 'numerical', 'integerOnly'=>true),
			array('account,content', 'length', 'max'=>255),
			array('content,code,createTime', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('messageId, memberId, title, content, state, createTime', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational 关联表.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array label字段说明
	 */
	public function attributeLabels()
	{
		return array(
			'messageId' => 'ID',
			'memberId' => '收信息的会员ID',
			'type' => '消息类型',
			'account' => '账号',
			'content' => '内容',
			'state' => '状态',//状态:0:失败1:成功
			'createTime' => '创建时间',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('messageId',$this->messageId);
		$criteria->compare('memberId',$this->memberId);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('account',$this->content,true);
		$criteria->compare('state',$this->state);//状态:0::未查看1:已查看2:删除		
		$criteria->compare('createTime',$this->createTime);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}

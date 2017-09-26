<?php

/**
 * 会员账户变动表 "{{member_account}}".
 *
 * @property integer $accountId
 * @property integer $memberId
 * @property integer $adminId
 * @property integer $membertype
 * @property integer $type
 * @property integer $event
 * @property string orderId
 * @property double $amount
 * @property double $amount_log
 * @property string $note
 * @property integer $createTime
 */
class MemberAccount extends CActiveRecord
{
	/**
	 * @return string 数据库表名称
	 */
	public function tableName()
	{
		return '{{member_account}}';
	}

	/**
	 * @return array 验证字段.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('memberId, membertype, type,event', 'numerical', 'integerOnly'=>true),
			array('amount, amount_log', 'numerical'),
			array('orderId', 'length', 'max'=>30),
			array('note,createTime', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('accountId, memberId, adminId, membertype, type, event, orderId, amount, amount_log, note, createTime', 'safe', 'on'=>'search'),
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
			'accountId' => 'Account',
			'memberId' => 'Member',
			'adminId' => 'Admin',
			'membertype' => 'Membertype',
			'type' => 'Type',
			'event' => 'Event',
			'orderId' => 'Order',
			'amount' => 'Amount',
			'amount_log' => 'Amount Log',
			'note' => 'Note',
			'createTime' => 'Create Time',
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

		$criteria->compare('accountId',$this->accountId);
		$criteria->compare('memberId',$this->memberId);
		$criteria->compare('adminId',$this->adminId);
		$criteria->compare('membertype',$this->membertype);
		$criteria->compare('type',$this->type);
		$criteria->compare('event',$this->event);
		$criteria->compare('orderId',$this->orderId,true);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('amount_log',$this->amount_log);
		$criteria->compare('note',$this->note,true);
		$criteria->compare('createTime',$this->createTime);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MemberAccount the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * 增减类型:
	 */
	public function getZmcType($type=null){
		if($type==null){
			return array(
					''=>'请选择',
					'1'=>'收入',
					'2'=>'支出',
					'3'=>'套现'
			);
		}else{
			$level = $this->getZmcType();
			if(array_key_exists($type,$level))
				return $level[$type];
		}
	}
	
	/**
	 * 操作类型:
	 */
	public function getZmcEvent($type=null){
		if($type==null){
			return array(
					'1'=>'订单',
					'2'=>'充值',
					'3'=>'提现',
					'4'=>'现金券',
					'5'=>'退款'
			);
		}else{
			$level = $this->getZmcEvent();
			if(array_key_exists($type,$level))
				return $level[$type];
		}
	}
	/**
	 * 写入余额变动记录
	 * @param $memberId 会员ID
	 * @param $membertype 0买家1卖家
	 * @param $adminId 管理员ID
	 * @param $type 类型:1增加2.减少
	 * @param $event 操作类型:1订单2充值3提现4现金券5退款
	 * @param $orderid 订单ID/流水号
	 * @param $amount 金额
	 * @param $note 备注
	 */
	public function setAccountLog($memberId,$membertype,$adminId=null,$type=1,$event=1,$orderid,$amount=0,$note=''){
		
		$accountlog = new MemberAccount();
		$accountlog->memberId = $memberId;
		$accountlog->membertype = $membertype;
		$accountlog->adminId = $adminId;
		$accountlog->type = $type;
		$accountlog->event = $event;
		$accountlog->orderId = $orderid;
		$accountlog->amount = $amount;
		$accountlog->amount_log = 0;
		$accountlog->note = $note;
			
		if($accountlog->save()){
				
			//发送消息给卖家
			$title = '金额变动通知';
			$getZmcEvent = $this->getZmcEvent($event);
			$body ="您好!您金额有变动,操作类型:".$getZmcEvent.",变动金额为:".$amount."元".$note;
			$sendmsg = new SendMessage( $body ,$title);
			$sendmsg->type = 'sysmsg';
			$account = $memberId;
			$sendmsg->send($account);	
			return true;
		}else{
	
			return false;
		}
	
	}
	 /**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->createTime = date( 'Y-m-d H:i:s' , time() );
		}else{
			
		}
		return true;
	}
	
	/**
	 * 保存后的操作
	 */
	public function afterSave(){
		return true;
	}
}

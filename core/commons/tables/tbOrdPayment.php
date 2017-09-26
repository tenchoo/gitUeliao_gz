<?php

/**
 * This is the model class for table "{{ord_payment}}".
 * 订单付款单
 *
 * The followings are the available columns in table '{{ord_payment}}':
 * @property string  $ordpaymentId	付款单号
 * @property integer $memberId		会员ID
 * @property integer $type			支付类型:0:支付宝,1微信支付
 * @property decimal $price			付款金额
 * @property integer $state			状态:0:待付款,1:已付款,3:作废,4:删除
 * @property timestamp $createTime	创建时间
 * @property timestamp $updateTime	更新时间
 * @property string  $tradeNo		交易流水号
 * @property string  $title			交易简要说明
 * @property string  $orderIds		订单支付信息
 */

class tbOrdPayment extends CActiveRecord
{


	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Payment the static model class
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
		return '{{ord_payment}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('memberId,orderIds,price,', 'required'),
			array('state, memberId,type', 'numerical', 'integerOnly'=>true),
			array('createTime,updateTime', 'safe'),
			array('title,orderIds', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'ordpaymentId' => '付款单号',
			'memberId' => '会员ID',
			'type' => '支付类型',
			'orderIds'=>'订单号',
			'price' => '价格',
			'state' => '状态',
			'createTime' => '创建时间',
			'updateTime' => '更新时间',

		);
	}
	
	/**
	 * 生成付款单----旧方法，只有APP初始版本还在使用
	 * @param string $orderids 订单ID
	 * @param integer $price  支付价格
	 * @param integer $type  支付类型，0支付宝，1微信支付
	 */
	public static function setPaymentOrder( $orderids, $price=0 , $type=0,$memberId='' ){
		$model = new tbOrdPayment();
		$model->ordpaymentId = uniqid();
		$model->memberId = ($memberId>0)?$memberId:Yii::app()->user->id;
		$model->orderIds = $orderids;
		$model->type = $type;
		$model->price = $price;
		$model->state = 0;
		if($model->save()){
			return $model->ordpaymentId;
		}else{
			return false;
		}
	}

	/**
	 * 保存前的操作 将本配置信息系列化保存
	 */
	protected function beforeSave(){
		if(parent::beforeSave()){
			if($this->isNewRecord){
				$this->ordpaymentId = uniqid();
				$this->createTime = $this->updateTime = date('Y-m-d H:i:s',time());
				$this->state = 0;
			}else{
				$this->updateTime = date('Y-m-d H:i:s',time());
			}
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 读取数据后的操作
	 */
	protected function afterFind(){
		return true;
	}



}
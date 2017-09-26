<?php

/**
 * 单取消操作记录表模型.
 *
 * @property integer	$orderId		订单ID
 * @property integer	$opId			操作者ID
 * @property integer	$opType			操作者类型，0客户，1业务员，2 后台管理员取消  3 系统取消
 * @property timestamp	$createTime		取消订单时间
 * @property string		$reason			取消订单原因
 */
class tbOrderClose extends CActiveRecord
{

	public function init()
	{
		parent::init();
		$this->opId = Yii::app()->user->id;
		$this->createTime = new CDbExpression('NOW()');
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return tbOrderClose the static model class
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
		return '{{order_close}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('orderId,opId,opType,reason', 'required'),
			array('orderId,opId,opType', 'numerical', 'integerOnly'=>true),
			array('reason', 'safe'),
		);
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'orderId' => '订单ID',
			'opId' => '操作者ID',
			'opType' => '操作者类型',
			'reason' => '取消订单原因',
		);
	}

	public function opTypeTitle(){
		$arr = array( '0'=>'客户取消','1'=>'业务员','2'=>'管理后台取消','3'=>'系统取消' );
		return array_key_exists( $this->opType ,$arr )?$arr[$this->opType]:$this->opType;
	}


	/**
	 * 保存后的操作
	 */
	protected function afterSave(){
		if($this->isNewRecord){
			//自动调整待分配状态订单
			$flag = tbOrderDistribution::model()->cancleOrder( $this->orderId );

			//更改订单修改记录状态为已关闭
			$applyChange = tbOrderApplychange::model()->closeCheck( $this->orderId );

			$message = $this->opTypeTitle().'，理由：'.$this->reason;;

			if( $this->opType == '2' ){
				$message .= '；操作人：'. Yii::app()->user->getState('username').'(后台 userId:'.$this->opId.')';
			} else if( $this->opType == '1' ){
				$message .= '；操作人：'. tbProfile::model()->getMemberUserName( $this->opId ).'(业务员 memberId:'.$this->opId.')';
			}

			tbOrderMessage::addMessage2( $this->orderId,'取消订单',$message );
			tbWarehouseMessage::cancle( $this->orderId );
		}
		return parent::afterSave();
	}
}
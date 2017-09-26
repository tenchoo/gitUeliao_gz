<?php
/**
 * 财务收款记录表模型
 * @author liang
 * @package CActiveRecord
 * @version 0.1
 *
 * @property int        $recordsId
 * @property int        $memberId			客户ID
 * @property int        $type				结算类型：0按结算单结算，1按月结算
 * @property int        $state				状态：0正常，1已撤消
 * @property int        $settlementId		结算单ID/月份
 * @property numerical	$amount				收款金额
 * @property int        $userId				操作者ID
 * @property string     $createTime 		操作时间
 * @property string     $username			操作者名称
 */

class tbDepositRecords extends CActiveRecord
{

    const TYPE_SETTLEMENT    = 0; //按结算单ID付款
    const TYPE_MONTHLY 		 = 1; //月结客户按月月份付款

    const STATE_MORNAL = 0; //正常
	 const STATE_UNDO = 1; //被撤消

    public function rules()
    {
        return [
            ['memberId,type,state,settlementId,amount', 'required'],
            ['memberId,settlementId', 'numerical', 'integerOnly' => true],
			['amount', 'numerical','min'=>'0.1'],
			['type,state', 'in', 'range'=>[0,1]],
        ];
    }

	public function attributeLabels(){
		return array(
			'amount' => '收款金额',
		);
	}

	public function relations(){
		return array(
			'member'=>array(self::BELONGS_TO,'tbProfileDetail','memberId','select'=>'companyname'),
		);
	}



    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{deposit_records}}';
    }

	protected function beforeSave(){
		if( $this->isNewRecord ){
			$this->createTime = new CDbExpression('NOW()');
			$this->username = Yii::app()->user->getState('username');
			$this->userId  = Yii::app()->user->id;
		}

		return true;
    }

	public function getSettlement(){
		if( empty( $this->settlementId ) ) return ;

		if( $this->type == '1' ){
			$month = date( 'Y-m-d',strtotime( $this->settlementId.'01' ) );
			$model = tbOrderSettlementMonth::model()->find( 'memberId=:mid and month=:month ',array(':mid'=>$this->memberId,':month'=>$month ) );
		}else{
			$model = tbOrderSettlement::model()->findByPk( $this->settlementId );
		}

		return $model;
	}
}

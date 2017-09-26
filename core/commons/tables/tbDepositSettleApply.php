<?php
/**
 * 财务收款结算申请表模型
 * @author liang
 * @package CActiveRecord
 * @version 0.1
 *
 * @property int        $id
 * @property int        $memberId			客户ID
 * @property int        $type				结算类型：0按结算单结算，1按月结算
 * @property int        $settlementId		结算单ID/月份
 * @property numerical	$amount				收款金额
 * @property int        $state				状态：0未审核，1审核通过，2审核不通过
 * @property int        $userId				申请操作者ID
 * @property int        $checkUserId		审核操作者ID
 * @property timestamp  $createTime 		申请时间
 * @property timestamp  $checkTime 			审核时间
 * @property string     $username			申请操作者名称
 * @property string     $checkUsername		审核者名称
 * @property string     $applyCause			申请理由
 * @property string     $checkCause			审核理由
 */

class tbDepositSettleApply extends CActiveRecord
{

    const STATE_NOCHECK = 0; //未审核
	const STATE_PASS = 1; //审核通过
	const STATE_NOTPASS = 2; //审核不通过

    public function rules()
    {
        return [
            ['memberId,type,state,settlementId,amount,applyCause', 'required'],
            ['memberId,settlementId', 'numerical', 'integerOnly' => true],
			['amount', 'numerical','min'=>'0.1'],
			['type', 'in', 'range'=>[0,1]],
			['applyCause,checkCause','safe'],
			['applyCause,checkCause','length','max'=>'50']
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


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{deposit_settleapply}}';
    }

	protected function beforeSave(){
		if( $this->isNewRecord ){
			$this->createTime = new CDbExpression('NOW()');
			$this->username = Yii::app()->user->getState('username');
			$this->userId  = Yii::app()->user->id;
		}

		return true;
    }

	public function stateTitle(){
		$arr = array('0'=>'未审核','1'=>'审核通过','2'=>'审核不通过');
		return array_key_exists( $this->state,$arr)?$arr[$this->state]:'';
	}
}

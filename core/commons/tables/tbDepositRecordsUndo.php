<?php
/**
 * 财务收款记录撤消申请表模型
 * @author liang
 * @package CActiveRecord
 * @version 0.1
 *
 * @property int        $id
 * @property int        $recordsId
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

class tbDepositRecordsUndo extends CActiveRecord
{

    const STATE_NOCHECK = 0; //未审核
	const STATE_PASS = 1; //审核通过
	const STATE_NOTPASS = 2; //审核不通过

    public function rules()
    {
        return [
            ['recordsId,applyCause', 'required'],
            ['recordsId', 'numerical', 'integerOnly' => true],
			['applyCause,checkCause','safe'],
			['applyCause,checkCause','length','max'=>'50']
        ];
    }

	public function attributeLabels(){
		return array(
			'applyCause' => '申请理由',
		);
	}

	public function relations(){
		return array(
			'records'=>array(self::BELONGS_TO,'tbDepositRecords','recordsId'),
		);
	}



    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{deposit_records_undo}}';
    }

	protected function beforeSave(){
		if( $this->isNewRecord ){
			$this->createTime = new CDbExpression('NOW()');
			$this->username = Yii::app()->user->getState('username');
			$this->userId  = Yii::app()->user->id;
			$this->state = 0;
		}

		return true;
    }

	public function stateTitle(){
		$arr = array('0'=>'未审核','1'=>'审核通过','2'=>'审核不通过');
		return array_key_exists( $this->state,$arr)?$arr[$this->state]:'';
	}
}

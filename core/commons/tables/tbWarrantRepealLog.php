<?php
/**
 * 撤消入库申请日志模型
 * @author yagas
 * @package CActiveRecord
 * @version 0.1
 *
 * @property int        $id
 * @property int        $repealId    撤消单号
 * @property int        $action        动作
 * @property string        $operator    操作人
 * @property string        $datetime    日期
 * @property string        $reasons    拒绝理由
 */
class tbWarrantRepealLog extends CActiveRecord
{

    const ACTION_NEW    = 0;
    const ACTION_DONE   = 1;
    const ACTION_REFUSE = 2;

    public function rules()
    {
        return [
            ['repealId,action,operator,datetime', 'required'],
            ['repealId,action', 'numerical', 'integerOnly' => true],
			['reasons', 'length', 'max'=>50],
        ];
    }
	
	public function attributeLabels(){
		return array(
			'reasons' => '理由',
		);
	}

    public function init()
    {
        $this->datetime = date('Y-m-d H:i:s');
        $this->operator = Yii::app()->user->getState('username');
        $this->reasons  = '';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{warehouse_repeal_log}}';
    }

    public function primaryKey()
    {
        return "id";
    }

    public function relations()
    {
        return [
            'repeal' => [self::BELONGS_TO, 'tbWarrantRepeal', 'repealId'],
        ];
    }
}

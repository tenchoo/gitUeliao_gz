<?php
/**
 * 撤消入库申请模型
 * @author yagas
 * @package CActiveRecord
 * @version 0.1
 *
 * @property repealId  单号
 * @property state  状态
 * @property warrantId  申请人员ID
 * @property userId 操作人员ID
 * @property operator  操作人员名称
 * @property createTime 添加时间
 * @property remark  备注说明
 */
class tbWarrantRepeal extends CActiveRecord
{

    const STATE_NEW    = 0;
    const STATE_DONE   = 1;
    const STATE_REFUSE = 2;

    public function init()
    {
        $this->createTime = date('Y-m-d H:i:s');
        $this->userId     = Yii::app()->user->id;
        $this->operator   = Yii::app()->user->getState('username');
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{warehouse_repeal}}';
    }

    public function primaryKey()
    {
        return "repealId";
    }

    public function relations()
    {
        return [
            'warrant'        => [self::BELONGS_TO, 'tbWarehouseWarrant', 'warrantId'],
            'warrant_detail' => [self::HAS_MANY, 'tbWarehouseWarrantDetail', 'warrantId'],
            'log' => [self::HAS_MANY, 'tbWarrantRepealLog', 'warrantId', 'order'=>'datetime'],
        ];
    }

    public function rules()
    {
        return [
            ['state,warrantId,userId,createTime', 'required'],
            ['remark', 'safe'],
			['remark', 'length', 'max'=>50],

        ];
    }
	
	public function attributeLabels(){
		return array(
			'repealId'  => '申请单号',
            'state'     => '申请单状态',
            'warrantId' => '入库单号',
			'remark' => '撤消说明',
		);
	}
}

<?php
/**
 * 仓库入库单
 *
 * @property integer    $warrantId        盘点单ID
 * @property integer    $warehouseId    仓库ID
 * @property integer    $userId            操作人userId
 * @property timestamp    $createTime        制单时间
 * @property timestamp    $realTime        实际入库时间
 * @property string        $operator        操作员
 * @property string        $factoryNumber    革厂编号
 * @property string        $contactName    联系人
 * @property string        $phone            联系电话
 * @property string        $factoryName    革厂名称
 * @property string        $address        联系地址
 * @property string        $remark            备注
 * @property integer    $source            来源
 * @property integer    $postId            来源单号（采购入库对应工厂的发货单号，调拨对应调拨单号，盘点对应盘点单号，退货入库对应退货申请单，调整入库对应调整单号）
 *
 */

class tbWarehouseWarrant extends CActiveRecord
{
    //采购单
    const FROM_ORDER = 0;
    //调拨单
    const FROM_CALLBACK = 1;
    //盘点单
    const FROM_COUNT = 2;

    //直接创建入库单
    const FORM_ADDNEW = 3;

    //订单退货入库单
    const FORM_REFUND = 4;

    //仓库调整单入库
    const FORM_ADJUST = 5;

    // 入库单
    const STATE_NORMAL = 0;

    // 申请撤消
    const STATE_APPLY = 1;

    // 撤消入库
    const STATE_REPEAL = 2;

    public function init()
    {
        $this->createTime    = new CDbExpression('NOW()');
        $this->realTime      = new CDbExpression('NOW()');
        $this->userId        = Yii::app()->user->id;
        $this->warehouseId   = 0; //不同的产品入不同的库，这里这个ID有什么作用
        $this->remark        = '';
        $this->operator      = Yii::app()->getUser()->getstate('username');
        $this->postId        = 0;
        $this->contactName   = '';
        $this->phone         = '';
        $this->factoryName   = '';
        $this->address       = '';
        $this->factoryNumber = 0;
        $this->source        = self::FROM_ORDER;
    }

    /**
     * 返回基于自身的AR实例
     * @param string $className 类名
     * @return CActiveRecord 实例
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string 返回表名
     */
    public function tableName()
    {
        return '{{warehouse_warrant}}';
    }

    public function relations()
    {
        return array(
            'detail' => array(self::HAS_MANY, 'tbWarehouseWarrantDetail', 'warrantId'),
            'posts'  => array(self::BELONGS_TO, 'tbOrderPost2', 'postId'),
        );
    }

    /**
     * @return array 模型验证规则.
     * 要兼容调拨入库单与盘点入库单，这里factoryNumber不能设为必填
     */
    public function rules()
    {
        return array(
            array('warehouseId,operator,source,postId', 'required'),
            array('factoryNumber,factoryName,contactName,phone,address', 'required', 'message' => Yii::t('warehouse', 'fill {attribute} value, please'), 'on' => 'purchase'),
            array('warehouseId,factoryNumber,userId,postId', "numerical", "integerOnly" => true),
            array('source', 'in', 'range' => array(0, 1, 2, 3, 4, 5)),
            array('remark', 'safe'),
			array('postId','checkExists','on'=>'insert'),
        );
    }

	/**
	* 检查是否存在
	*/
	public function checkExists( $attribute,$params ){
		if( empty( $this->$attribute ) ){
			return;
		}

		$criteria=new CDbCriteria;
		$criteria->compare('source',$this->source);
		$criteria->compare('postId',$this->postId);

		$model = $this->exists( $criteria );
		if( $model ){
			$label = $this->getAttributeLabel($attribute);
			$this->addError($attribute,'请不要重复入库');
		}
	}

    /**
     * Declares attribute labels.
     * @return array 定制字段的显示标签 (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'warehouseId'   => '仓库编号',
            'operator'      => '操作员',
            'factoryNumber' => '革厂编号',
            'contactName'   => '联系人',
            'phone'         => '联系电话',
            'factoryName'   => '革厂名称',
            'address'       => '收货地址',
            'remark'        => '备注',
            'postId'        => '发货单号',
        );
    }

    public function setContacts($info)
    {
        $this->contactName = $info;
    }

    public function setComment($info)
    {
        $this->remark = $info;
    }
}

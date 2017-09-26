<?php
/**
 * 财务收款记录表模型
 * @author liang
 * @package CActiveRecord
 * @version 0.1
 *
 * @property int        $id
 * @property int        $warehouseId		仓库ID
 * @property int        $printerId			打印机ID
 * @property int        $isDefault			是否默认
 */

class tbWarehousePrinter extends CActiveRecord
{

    public function rules()
    {
        return [
            ['warehouseId,printerId,isDefault', 'required'],
            ['warehouseId,printerId', 'numerical', 'integerOnly' => true],
			['isDefault', 'in', 'range'=>[0,1]],
        ];
    }

	public function attributeLabels(){
		return array(
			'printerId' => '打印机',
		);
	}


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{warehouse_printer}}';
    }
}

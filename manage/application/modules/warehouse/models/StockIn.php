<?php

/**
 * 入库单
 * User: yagas
 * Date: 2016-08-06
 * Time: 10:21
 */
class StockIn extends CFormModel
{

    public $warrantId;
    public $array_detials = [];

    /**
     * 读取入库单信息
     * @param integer $warrantId 入库单号
     * @return StockIn|null
     */
    public static function ready($warrantId)
    {
        $stockin = new StockIn;
        $warrant = tbWarehouseWarrant::model()->findByPk($warrantId);
        if (!$warrant) {
            return null;
        }

        $stockin->warrantId = $warrant->warrantId;
        $stockin->array_detials = $warrant->detail();
        return $stockin;
    }

    /**
     * 撤消入库，扣除库存产品数量
     * 撤消入库前需要判断库存产品数量，如果数量不足将无法进行撤消
     * 如果商品已经被进行分拣锁定，需要从总库存中扣除锁定量
     * 库存数量=库存总量-锁定量
     */
    public function repeal()
    {
        if (!$this->warrantId) {
            $this->addError('warrantId', Yii::t('base', 'Not found warrantId'));
            return false;
        }

        foreach ($this->array_detials as $detail) {
            $condition = [
                'positionId' => $detail->positionId,
                'singleNumber' => $detail->singleNumber,
                'productBatch' => $detail->batch
            ];

            //查询产品库存数量
            $warehouse_product = tbWarehouseProduct::model()->findByAttributes($condition);

            //查询产品锁定量
            $warehouse_lock = tbWarehouseLock::model()->findByAttributes($condition);
            $lock = is_null($warehouse_lock) ? 0 : $warehouse_lock->num;

            if (!$warehouse_product) {
                $this->addError('warrantId', Yii::t('base', 'Not found product:{pro}', ['{pro}' => $detail->singleNumber]));
                return false;
            }

            if (($warehouse_product->num - $lock) < $detail->num) {
                $this->addError('warrantId', Yii::t('base', 'product:{pro} lower than the quantity of stock', ['{pro}' => $detail->singleNumber]));
                return false;
            }

            //更新产品库存记录
            $warehouse_product->num = $warehouse_product->num - $detail->num;
            if ($warehouse_product->num <= 0) {
                $warehouse_product->num = 0;
            }

            if (!$warehouse_product->save()) {
                $this->addError('warrantId', Yii::t('base', 'faild update product:{pro} quantity of stock', ['{pro}' => $detail->singleNumber]));
                return false;
            }

        }
        return true;
    }
}
<?php
/**
 * 打印订单详情
 * User: yagas
 * Date: 2016/6/10
 * Time: 16:24
 *
 * @property id 明细单号
 * @property printId 打印单号
 * @property orderId 订单编号
 * @property product 产品编号
 * @property total 数量
 * @property unit 单位
 * @property price 单价
 * @property subprice 金额
 * @property mark 备注
 */

class tbPrintOrderDetail extends CActiveRecord {
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{print_order_detail}}';
    }

    public function primaryKey() {
        return 'id';
    }

    /**
     * 查询打印订单信息
     * @param $printId
     * @return static[]
     */
    public function findByPrintId($printId) {
        return $this->findByAttributes(['printId'=>$printId]);
    }
}
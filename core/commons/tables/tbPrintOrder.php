<?php
/**
 * 打印单
 * User: yagas
 * Date: 2016/6/10
 * Time: 16:24
 *
 * @property printId 打印单号
 * @property orderId 订单编号
 * @property custom_name 公司名称
 * @property custom_phone 联系电话
 * @property order_type 订单类型
 * @property create_time 制单时间
 * @property create_by 制单人
 * @property pushTime 推送打印时间
 */

class tbPrintOrder extends CActiveRecord {
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{print_order}}';
    }

    public function primaryKey() {
        return 'printId';
    }

    /**
     * 查询打印订单信息
     * @param $printId
     * @return static[]
     */
    public function findByPrintId($printId) {
        return $this->with('detail')->findByAttributes(['printId'=>$printId]);
    }

    public function relations() {
        return array(
            'detail' => array(self::HAS_MANY, 'tbPrintOrderDetail', 'printId'),
        );
    }

	protected function beforeSave(){
		if( $this->isNewRecord ){
			$this->pushTime = date('Y-m-d H:i:s');
		}
		return true;
    }
}
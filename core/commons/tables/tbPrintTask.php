<?php
/**
 * 打印队列
 * User: yagas
 * Date: 2016/6/10
 * Time: 16:24
 *
 * @property id 自动编号
 * @property printer 打印机编号
 * @property printId 打印单号
 * @property createTime 添加时间
 * @property printed 已打印
 */

class tbPrintTask extends CActiveRecord {
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{print_tasklist}}';
    }

    public function primaryKey() {
        return 'id';
    }

    /**
     * 读取打印机队列
     * @param $printer
     * @return static[]
     */
    public function findByPrinter($printer) {
        return $this->findAllByAttributes(['printer'=>$printer, 'printed'=>0]);
    }
}
<?php
/**
 * mongoDB绑定product对应表
 * User: yagas
 * Date: 2016/1/4
 * Time: 17:46
 */
class tbOpencvMap extends CActiveRecord {

    public $productId;
    public $uid;

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return "{{opencv_map}}";
    }

    public function primaryKey() {
        return "id";
    }
}
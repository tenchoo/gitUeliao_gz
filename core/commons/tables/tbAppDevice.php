<?php

/**
 * APP终端信息收集
 * User: yagas
 * Date: 2016/4/20
 * Time: 14:45
 */
class tbAppDevice extends CActiveRecord {

    public $id;
    public $device; //设备号
    public $version; //版本号
    public $mobileModel; //机型
    public $os; //系统版本号

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{app_device}}';
    }

    public function primaryKey() {
        return "id";
    }

    public function rules() {
        return [
            ["device,version,mobileModel,os","safe"]
        ];
    }
}
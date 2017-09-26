<?php

/**
 * APP版本更新信息数据存储
 * User: yagas
 * Date: 2016/4/20
 * Time: 14:45
 */
class tbAppVersion extends CActiveRecord
{

    public $id; //自动编号
    public $version = 0; //安卓版本号(整形)
    public $versionStr;
    public $url; //下载地址
    public $force; //是否强制更新
    public $device; //设备类型
    public $comment; //升级日志

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{app_version}}';
    }

    public function primaryKey()
    {
        return "id";
    }
}
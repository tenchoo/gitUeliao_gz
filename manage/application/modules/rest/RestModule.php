<?php
/**
 * Restful API 接口模块
 * User: yagas
 * Date: 2016/3/14
 * Time: 13:52
 */

class RestModule extends CWebModule {

    public function init() {
        parent::init();
        Yii::import('rest.models.*');
    }
}
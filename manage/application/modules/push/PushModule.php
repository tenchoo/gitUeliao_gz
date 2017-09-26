<?php
/**
 * PushModule
 * 手机信息推送
 * User: yagas
 * Date: 2016/3/11
 * Time: 17:40
 */
class PushModule extends CWebModule {

    public function init() {
        parent::init();
        require_once Yii::getPathOfAlias('vendors').'/getui/IGt.Push.php';
    }
}
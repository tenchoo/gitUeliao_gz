<?php
/**
 * 系统管理菜单
 * User: yagas
 * Date: 2016/3/14
 * Time: 13:54
 */

class MenuController extends Controller {

    public function actionIndex() {

    }

    public function actionShow() {
        $menuId = Yii::app()->request->getQuery('menuId');
    }
}
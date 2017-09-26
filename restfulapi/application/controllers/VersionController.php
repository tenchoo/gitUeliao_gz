<?php

/**
 * 返回应用版本更新信息
 * @author yagas
 * @version 0.1
 * @package Controller
 */
class VersionController extends Controller {

    public function actionIndex() {
        $divice           = Yii::app()->request->getQuery('device');
        $criterial        = new CDbCriteria();
        $criterial->order = "id DESC";
        $criterial->condition = "device=:d";
        $criterial->params = array(':d'=>$divice);

        $info = tbAppVersion::model()->find($criterial);
        if($info instanceof CActiveRecord) {
            $this->showJson(true,null,$info);
        }
        else {
            $this->showJson(False,Yii::t('restful','Not found version info'),null);
        }
    }
}

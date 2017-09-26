<?php
/**
 * 收集设备信息
 * User: yagas
 * Date: 2016/4/20
 * Time: 16:52
 */
class DeviceController extends Controller {

    public function actionCreate() {
        $post = array();
        $post["device"] = Yii::app()->request->getPost("device");
        $post["version"] = Yii::app()->request->getPost("version");
        $post["mobileModel"] = Yii::app()->request->getPost("mobileModel");
        $post["os"] = Yii::app()->request->getPost("os");

        $device = new tbAppDevice();
        $device->setAttributes($post);

        //数据校验并存储
        if($device->save()) {
            $this->showJson(true,Yii::t("restful","submit successful"));
        }
        else {
            $errors = $device->getErrors();
            $field = array_shift($errors);
            $this->showJson(false, Yii::t("errors", $field[0]));
        }
    }
}
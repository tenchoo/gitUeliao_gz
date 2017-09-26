<?php
/**
 * 打印机队列
 * User: yagas
 * Date: 2016/6/10
 * Time: 14:45
 */

class PrinttaskController extends CController {

    public function actionIndex() {
        $printer = Yii::app()->request->getQuery('printer');
        $result = tbPrintTask::model()->findByPrinter($printer);
        if(!$result) {
            $this->showJson(false,'nothing');
        }

        $tasklist = array_map(function($row){
            return $row->getAttributes(['printId','createTime']);
        }, $result);
        $this->showJson(true,'successfully',$tasklist);
    }

    public function actionOrder() {
        $id        = Yii::app()->request->getQuery('id');
        $orderInfo = tbPrintOrder::model()->findByPrintId($id);

        if(is_null($orderInfo)) {
            $this->showJson(false, 'not found record');
        }

        $order = $orderInfo->getAttributes();
        $order['create_time'] = date('Y-m-d', $orderInfo->create_time);
        $order['list'] = array_map(function($row){
            return $row->getAttributes();
        }, $orderInfo->detail);

        $this->showJson(true, 'successfully', $order);
    }

    public function actionDone() {
        $id = Yii::app()->request->getPost('id');
        $order = tbPrintTask::model()->findByAttributes(['printId'=>$id]);
        if($order) {
            $order->printed = 1;
            if($order->save()) {
                $this->showJson(true, 'successfully');
            }
            else {
                $errors = $order->getErrors();
                $error = array_shift($errors);
                Yii::log($error[0], CLogger::LEVEL_ERROR, 'printTask::done');
            }
        }
        $this->showJson(false, 'faild');
    }

    public function actionReset() {
        $id = Yii::app()->request->getQuery('id',1);
        $order = tbPrintTask::model()->findByAttributes(['printId'=>$id]);
        if($order) {
            $order->printed = 0;
            if($order->save()) {
                $this->showJson(true, 'successfully');
            }
        }
        $this->showJson(false, 'faild');
    }

    public function showJson($state,$message='',$data='') {
        $json = new AjaxData($state);
        $json->setMessage($message);
        $json->data = $data;
        echo $json->toJson();
        Yii::app()->end();
    }
}

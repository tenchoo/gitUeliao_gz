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
		if( $order['order_type'] == '4' ){
			//后四位
			$order['orderMark'] = $this->getMark(  substr( $order['saleOrderId'], -4 ) );	
			$order['QRcode'] = 'http://exp';
		}

        $order['list'] = array_map(function($row){
            return $row->getAttributes();
        }, $orderInfo->detail);

        $this->showJson(true, 'successfully', $order);
    }

	private function getMark( $i ){
		$i = (int)$i;
		$arr  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$arr1 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';

	 	if( $i < 910 ){
			$t = floor( $i/35 );
			$n = $t%26;
			$t = $i%35;
			return $arr[$n].$arr1[$t];
		}else{
			$t = floor( $i/(35*35) );
			$n = $t%26;
			$t1 =  floor(( $i - $n*35*35 )/35 );
			if( $t1 >= 26 ){
				$n1 = $t1 - 26;
			}else{
				$n1 = $t1 - 26 + 35;
			}

			$n2 = ($i%(35*35))%35;
			return $arr[$n].$arr1[$n1].$arr1[$n2];
		}
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
